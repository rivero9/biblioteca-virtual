<?php
require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../config/connection_db.php";

// 3. Conectar a la base de datos.
try {
    $db = new Database();
    $con = $db->connect();
    if (!$con) {
        throw new Exception("No se pudo establecer la conexión a la base de datos.");
    }
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error de conexión interna: ' . $e->getMessage()]);
    exit();
}

require_once __DIR__ . "/auth_check.php";

// Acceder a los datos del usuario autenticado cargados por auth_check.php
if (!isset($currentUser) || !is_array($currentUser)) {
    http_response_code(500); // Internal Server Error si $currentUser no se cargó
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Error interno: Datos del usuario no disponibles.']);
    exit();
}

// --- Funciones de Utilidad ---
function limpiar_datos($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}


// --- Inicializar la estructura de respuesta JSON ---
header('Content-Type: application/json');
$response = ['success' => false, 'message' => '', 'errors' => []];

// Asegurarse de que la solicitud sea POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $response['message'] = "Método de solicitud no permitido.";
    http_response_code(405);
    echo json_encode($response);
    exit();
}

// --- Recopilar y Sanitizar Datos del Formulario ---
$current_password = limpiar_datos($_POST['current_password'] ?? '');
$new_password = limpiar_datos($_POST['new_password'] ?? '');
$confirm_password = limpiar_datos($_POST['confirm_password'] ?? '');

// --- Validaciones de Contraseña ---

// 1. Validar Contraseña Actual (obligatoria)
if (empty($current_password)) {
    $response['errors']['current_password'] = "La contraseña actual es obligatoria.";
} else {
    // Necesitamos obtener el hash de la contraseña del usuario desde la base de datos
    try {
        $sql = $con->prepare("SELECT clave FROM usuarios WHERE id = :user_id LIMIT 1");
        $sql->bindParam(':user_id', $currentUser['id'], PDO::PARAM_INT);
        $sql->execute();
        $user_data_from_db = $sql->fetch(PDO::FETCH_ASSOC);

        if (!$user_data_from_db || !password_verify($current_password, $user_data_from_db['clave'])) {
            $response['errors']['current_password'] = "La contraseña actual es incorrecta.";
        }
    } catch (PDOException $e) {
        $response['message'] = "Error de base de datos al verificar contraseña actual.";
        error_log("PDO Exception en update_password.php (verificar current_password): " . $e->getMessage());
        // En caso de error de BD al verificar contraseña, marcamos un error general y no continuamos.
        echo json_encode($response);
        exit();
    }
}

// 2. Validar Nueva Contraseña
if (empty($new_password)) {
    $response['errors']['new_password'] = "La nueva contraseña es obligatoria.";
} elseif (strlen($new_password) < 8 || strlen($new_password) > 24) { // Mínimo 8 caracteres, puedes ajustar
    $response['errors']['new_password'] = "La contraseña debe contener entre 8 y 24 caracteres.";
} elseif (!preg_match("/[A-Z]/", $new_password) || 
        !preg_match("/[a-z]/", $new_password) || 
        !preg_match("/[0-9]/", $new_password) || 
        !preg_match("/[!@#$%^&*()\-_=+{};:,<.>¿¡]/", $new_password)) 
    {
    $response['errors']['new_password'] = "Debe contener al menos un número, una letra mayuscula y una minuscula, y un caracter especial.";
}

// 3. Validar Confirmación de Contraseña
if (empty($confirm_password)) {
    $response['errors']['confirm_password'] = "Confirma la nueva contraseña.";
} elseif ($new_password !== $confirm_password) {
    $response['errors']['confirm_password'] = "Las contraseñas no coinciden.";
}

// 4. Asegurarse de que la nueva contraseña no sea la misma que la actual
// Solo verifica si la contraseña actual fue correcta y no hubo otros errores antes.
if (empty($response['errors']['current_password']) && empty($response['errors']['new_password'])) {
    if (password_verify($new_password, $user_data_from_db['clave'])) {
        $response['errors']['new_password'] = "La nueva contraseña no puede ser igual a la actual.";
    }
}


// --- Si no hay errores de validación, proceder con la actualización en la DB ---
if (empty($response['errors'])) {
    try {
        // Hashear la nueva contraseña
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Preparar la consulta UPDATE para la contraseña
        $sql = $con->prepare("
            UPDATE usuarios
            SET clave = :password
            WHERE id = :user_id
        ");

        $sql->bindParam(':password', $hashed_password, PDO::PARAM_STR);
        $sql->bindParam(':user_id', $currentUser['id'], PDO::PARAM_INT);

        if ($sql->execute()) {
            $response['success'] = true;
            $response['message'] = "Contraseña actualizada exitosamente.";

            // Invalidar la sesión actual por seguridad después de un cambio de contraseña
            // Esto forzaría al usuario a volver a iniciar sesión con la nueva contraseña.
            session_unset();
            session_destroy();
            session_start();
            session_regenerate_id(true);

            echo json_encode($response);
            exit();
        } else {
            $response['message'] = "Error al actualizar la contraseña en la base de datos.";
            error_log("Error PDO al actualizar contraseña: " . implode(":", $sql->errorInfo()));
        }

    } catch (PDOException $e) {
        $response['message'] = "Error de base de datos durante la actualización de contraseña: " . $e->getMessage();
        error_log("PDO Exception en update_password.php: " . $e->getMessage());
    } catch (Exception $e) {
        $response['message'] = "Ocurrió un error inesperado al procesar la solicitud de contraseña.";
        error_log("General Exception en update_password.php: " . $e->getMessage());
    }
} else {
    // Si hay errores de validación, el mensaje general ya se ha establecido,
    // y el array 'errors' se enviará con los detalles.
    $response['message'] = "Por favor, corrige los errores en el formulario.";
}

// --- Enviar la respuesta JSON final ---
echo json_encode($response);
exit();
?>
