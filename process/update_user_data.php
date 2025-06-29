<?php
// process/update_user_data.php

require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../config/connection_db.php";

try {
    $db = new Database();
    $con = $db->connect();
    if (!$con) {
        throw new Exception("No se pudo establecer la conexión a la base de datos.");
    }
} catch (Exception $e) {
    // Si la conexión falla aquí, no podemos proceder.
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error de conexión interna: ' . $e->getMessage()]);
    exit();
}

// 4. Incluir el archivo de verificación de autenticación.
// Si el usuario no está logueado o la sesión es inválida, este script
// enviará una respuesta JSON de error (si es petición AJAX) y detendrá la ejecución.
// Si el script continúa, la variable $currentUser (con los datos del usuario logueado)
// estará disponible en este ámbito.
require_once __DIR__ . "/auth_check.php";

// Acceder a los datos del usuario autenticado cargados por auth_check.php
// $currentUser estará disponible si la validación en auth_check.php fue exitosa.
// Si auth_check.php falló, el script ya habría terminado con una respuesta JSON o una redirección.
// Por seguridad, siempre verificar que $currentUser exista y sea un array.
if (!isset($currentUser) || !is_array($currentUser)) {
    http_response_code(500); // Internal Server Error si $currentUser no se cargó
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error interno: Datos del usuario no disponibles.']);
    exit();
}


// --- Carga de librerías externas (asegúrate de que estén instaladas con Composer) ---
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;


// --- Funciones de Utilidad y Validación ---

// Función para limpiar (sanitar) los datos de entrada
function limpiar_datos($data) {
    $data = trim($data);
    $data = stripslashes($data);       // Elimina barras invertidas agregadas por PHP automáticamente
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8'); // Convierte caracteres especiales a entidades HTML
    return $data;
}

// Función para cargar la lista de dominios desechables
function loadDisposableEmailDomains($filePath) {
    if (!file_exists($filePath)) {
        error_log("Advertencia: El archivo de dominios desechables no se encontró en: " . $filePath);
        return [];
    }
    $domains = array_map('trim', file($filePath, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES));
    return array_filter($domains);
}

// Función de Validación de Formato de Teléfono para Venezuela
// Esta función valida el FORMATO del número.
function validateVenezuelanPhoneNumberFormat($phoneNumber) {
    $phoneNumber = str_replace([' ', '-', '(', ')'], '', $phoneNumber); // Limpiar caracteres no numéricos excepto '+'
    // Regex robusta para números de teléfono venezolanos (10 dígitos después de prefijos opcionales)
    // Acepta: +58xxxxxxxxxx, 0xxxxxxxxxx, 412xxxxxxx, 414xxxxxxx, etc.
    // Los prefijos 2xx son para fijos, 4xx para móviles.
    // La regex valida que el número resultante tenga 10 dígitos funcionales.
    $regex = "/^(?:\+?58|0)?(?:2(?:12|[3-9]\d{2})|4(?:12|14|16|24|26))\d{7}$/";
    return preg_match($regex, $phoneNumber);
}


// --- Funciones de Validación de Existencia en BD (MODIFICADAS para UPDATE) ---
// Estas funciones ahora aceptan el ID del usuario actual y lo excluyen de la comprobación,
// permitiendo que un usuario actualice su propio email/teléfono/cédula si no ha cambiado
// o si el nuevo valor no pertenece a OTRA persona.
function userEmailExists(string $userEmail, PDO $con, int $currentUserId): bool {
    $sql = $con->prepare("SELECT id FROM usuarios WHERE correo = :email AND id != :current_user_id LIMIT 1");
    $sql->bindParam(':email', $userEmail, PDO::PARAM_STR);
    $sql->bindParam(':current_user_id', $currentUserId, PDO::PARAM_INT);
    $sql->execute();
    return $sql->fetchColumn() > 0;
}

function userTelExists(string $userTel, PDO $con, int $currentUserId): bool {
    $sql = $con->prepare("SELECT id FROM usuarios WHERE telefono = :tel AND id != :current_user_id LIMIT 1");
    $sql->bindParam(':tel', $userTel, PDO::PARAM_STR);
    $sql->bindParam(':current_user_id', $currentUserId, PDO::PARAM_INT);
    $sql->execute();
    return $sql->fetchColumn() > 0;
}

function userCedulaExists(string $userCedula, PDO $con, int $currentUserId): bool {
    $sql = $con->prepare("SELECT id FROM usuarios WHERE cedula = :cedula AND id != :current_user_id LIMIT 1");
    $sql->bindParam(':cedula', $userCedula, PDO::PARAM_STR);
    $sql->bindParam(':current_user_id', $currentUserId, PDO::PARAM_INT);
    $sql->execute();
    return $sql->fetchColumn() > 0;
}


// --- Carga la lista de dominios desechables una sola vez ---
$disposableDomainsFilePath = __DIR__ . '/../data/disposable_email_blacklist.txt';
$disposableEmailDomains = loadDisposableEmailDomains($disposableDomainsFilePath);


// --- Inicializar la estructura de respuesta JSON ---
$response = ['success' => false, 'message' => '', 'errors' => []];

// Asegurarse de que la solicitud sea POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $response['message'] = "Método de solicitud no permitido.";
    http_response_code(405); // Método no permitido
    echo json_encode($response);
    exit();
}


// --- Recopilar y Sanitizar Datos del Formulario ---
// Usamos ?? '' para asegurar que las variables siempre existan y sean cadenas.
$new_name = limpiar_datos($_POST["name"] ?? '');
$new_tel = limpiar_datos($_POST["tel"] ?? '');
$new_email = limpiar_datos($_POST["email"] ?? '');
$new_cedula = limpiar_datos($_POST["cedula"] ?? '');
$new_pnf = limpiar_datos($_POST["pnf"] ?? '');
$new_trayecto = limpiar_datos($_POST["trayecto"] ?? ''); // Corregido el nombre de la variable POST


// --- 5. Validaciones (Solo si el dato ha cambiado) ---

// Validar Nombre
if ($new_name !== $currentUser['nombre']) {
    if (empty($new_name)) {
        $response['errors']['name'] = "El nombre es obligatorio.";
    } elseif (strlen($new_name) < 5 || strlen($new_name) > 50) {
        $response['errors']['name'] = "El nombre debe contener entre 5 y 50 caracteres.";
    } elseif (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u", $new_name)) {
        $response['errors']['name'] = "Solo se permiten letras y espacios en blanco.";
    }
}

// Validar Teléfono
if ($new_tel !== $currentUser['telefono']) {
    if (empty($new_tel)) {
        $response['errors']['tel'] = "El teléfono es obligatorio.";
    } elseif (!validateVenezuelanPhoneNumberFormat($new_tel)) {
        $response['errors']['tel'] = "Formato de teléfono no válido (ej. 04121234567 o +584121234567).";
    } else {
        // Normalizar el número a un formato consistente (ej. +58XXXXXXXXXX)
        $clean_tel = str_replace([' ', '-', '(', ')'], '', $new_tel);
        if (substr($clean_tel, 0, 1) === '0') {
            $normalized_new_tel = '+58' . substr($clean_tel, 1);
        } elseif (substr($clean_tel, 0, 3) !== '+58' && strlen($clean_tel) === 10) {
            $normalized_new_tel = '+58' . $clean_tel;
        } else {
            $normalized_new_tel = $clean_tel;
        }
        $new_tel = $normalized_new_tel; // Usar el número normalizado para la DB y la validación de existencia

        if (userTelExists($new_tel, $con, $currentUser['id'])) {
            $response['errors']['tel'] = "Ya existe una cuenta con ese teléfono.";
        }
    }
}

// Validar Correo Electrónico
if ($new_email !== $currentUser['correo']) {
    if (empty($new_email)) {
        $response['errors']['email'] = "El correo electrónico es obligatorio.";
    } elseif (strlen($new_email) < 6 || strlen($new_email) > 254) {
        $response['errors']['email'] = "El correo debe tener entre 6 y 254 caracteres.";
    } else {
        $validator = new EmailValidator();
        $multipleValidations = new MultipleValidationWithAnd([
            new RFCValidation(),
            new DNSCheckValidation()
        ]);

        if (!$validator->isValid($new_email, $multipleValidations)) {
            $response['errors']['email'] = "Formato de correo electrónico no válido o dominio inexistente.";
        } elseif (in_array(substr($new_email, strpos($new_email, '@') + 1), $disposableEmailDomains)) {
            $response['errors']['email'] = "No se permiten correos electrónicos de dominios temporales/desechables.";
        } elseif (userEmailExists($new_email, $con, $currentUser['id'])) {
            $response['errors']['email'] = "Ya existe una cuenta con ese correo, inténtalo con otro.";
        }
    }
}

// Validar Cédula
if ($new_cedula !== $currentUser['cedula']) {
    if (empty($new_cedula)) {
        $response['errors']['cedula'] = "La cédula es obligatoria.";
    } else {
        $new_cedula_clean = preg_replace('/[^0-9]/', '', $new_cedula); // Remover no-dígitos
        $new_cedula = $new_cedula_clean; // Asegurar que $new_cedula ahora es solo dígitos

        if (!is_numeric($new_cedula) || strlen($new_cedula) < 7 || strlen($new_cedula) > 10) {
            $response['errors']['cedula'] = "Formato de cédula no válido. Debe contener solo números y entre 7 y 10 dígitos.";
        } elseif (userCedulaExists($new_cedula, $con, $currentUser['id'])) {
            $response['errors']['cedula'] = "Ya existe una cuenta con esa cédula.";
        }
    }
}

// Validar PNF (Carrera)
$allowed_pnfs = ["Informatica", "Electronica", "Mecanica", "Administracion", "Contaduria"];
if ($new_pnf !== $currentUser['pnf']) {
    if (empty($new_pnf)) {
        $response['errors']['pnf'] = "Selecciona tu PNF (Carrera).";
    } elseif (!in_array($new_pnf, $allowed_pnfs)) {
        $response['errors']['pnf'] = "El PNF seleccionado no es válido.";
    }
}

// Validar Trayecto
$allowed_trayectos = ["1", "2", "3", "4"];
if ($new_trayecto !== $currentUser['trayecto']) {
    if (empty($new_trayecto)) {
        $response['errors']['trayecto'] = "Selecciona tu Trayecto.";
    } elseif (!in_array($new_trayecto, $allowed_trayectos)) {
        $response['errors']['trayecto'] = "El Trayecto seleccionado no es válido.";
    }
}


// --- 6. Si no hay errores de validación, proceder con la actualización en la DB ---
if (empty($response['errors'])) {
    try {
        // Preparar la consulta UPDATE
        $sql = $con->prepare("
            UPDATE usuarios
            SET nombre = :nombre, telefono = :telefono, correo = :correo,
                cedula = :cedula, pnf = :pnf, trayecto = :trayecto
            WHERE id = :user_id
        ");

        // Bindear los parámetros (usar los valores YA validados y sanitizados)
        $sql->bindParam(':nombre', $new_name, PDO::PARAM_STR);
        $sql->bindParam(':telefono', $new_tel, PDO::PARAM_STR);
        $sql->bindParam(':correo', $new_email, PDO::PARAM_STR);
        $sql->bindParam(':cedula', $new_cedula, PDO::PARAM_STR);
        $sql->bindParam(':pnf', $new_pnf, PDO::PARAM_STR);
        $sql->bindParam(':trayecto', $new_trayecto, PDO::PARAM_STR);
        $sql->bindParam(':user_id', $currentUser['id'], PDO::PARAM_INT); // Usar el ID del usuario autenticado

        if ($sql->execute()) {
            // Actualización exitosa
            $response['success'] = true;
            $response['message'] = "Datos actualizados exitosamente.";

            // Opcional: Actualizar el nombre de usuario en la sesión si cambió, para reflejar en el header inmediatamente
            if ($new_name !== $currentUser['nombre']) {
                $_SESSION['user_name'] = $new_name;
            }

            // --- ¡NUEVO BLOQUE DE CÓDIGO! Cargar los datos del usuario recién actualizados de la DB ---
            // para devolverlos al frontend. Esto asegura que obtienes los datos más frescos.
            $sqlUpdatedUser = $con->prepare("SELECT id, nombre, telefono, correo, cedula, pnf, trayecto FROM usuarios WHERE id = :user_id LIMIT 1");
            $sqlUpdatedUser->bindParam(':user_id', $currentUser['id'], PDO::PARAM_INT);
            $sqlUpdatedUser->execute();
            $updatedUserData = $sqlUpdatedUser->fetch(PDO::FETCH_ASSOC);

            if ($updatedUserData) {
                $response['user_data'] = $updatedUserData; // Añadir los datos actualizados a la respuesta JSON
            } else {
                // Fallback si por alguna razón no se pueden recargar los datos
                error_log("Advertencia: No se pudieron recargar los datos del usuario después de la actualización.");
            }

        } else {
            // Error al ejecutar la consulta UPDATE
            $response['message'] = "Error al actualizar los datos en la base de datos.";
            error_log("Error PDO al actualizar datos en update_user_data.php: " . implode(":", $sql->errorInfo()));
        }

    } catch (PDOException $e) {
        // Capturar errores PDO durante la actualización
        $response['message'] = "Error de base de datos durante la actualización: " . $e->getMessage();
        error_log("PDO Exception en update_user_data.php: " . $e->getMessage());
    } catch (Exception $e) {
        // Capturar otros errores inesperados
        $response['message'] = "Ocurrió un error inesperado al procesar la solicitud.";
        error_log("General Exception en update_user_data.php: " . $e->getMessage());
    }
} else {
    // Si hay errores de validación, el mensaje general ya se ha establecido,
    // y el array 'errors' se enviará con los detalles.
    $response['message'] = "Por favor, corrige los errores en el formulario.";
}

// --- 7. Enviar la respuesta JSON final ---
echo json_encode($response);
exit(); // Terminar el script para asegurar que solo se envíe el JSON
?>
