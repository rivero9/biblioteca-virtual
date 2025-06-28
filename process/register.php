<?php

require __DIR__ . "/../config/init.php";
require __DIR__ . "/../config/connection_db.php";

// libreria para validar el correo
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;

// connect
$db = new Database();
$con = $db->connect();


// funcions
function validateUserEmail($userEmail, $con)
{
    $sql = $con->prepare("SELECT id FROM usuarios WHERE correo LIKE :email LIMIT 1");
    $sql->bindParam(':email', $userEmail, PDO::PARAM_STR);

    $sql->execute();
    return $sql->fetchColumn() > 0 ? true : false;
}

function validateUserTel($userTel, $con)
{
    $sql = $con->prepare("SELECT id FROM usuarios WHERE telefono = :tel LIMIT 1");
    $sql->bindParam(':tel', $userTel, PDO::PARAM_STR);

    $sql->execute();
    return $sql->fetchColumn() > 0 ? true : false;
}

function validateUserCedula($userCedula, $con)
{
    $sql = $con->prepare("SELECT id FROM usuarios WHERE cedula = :cedula LIMIT 1");
    $sql->bindParam(':cedula', $userCedula, PDO::PARAM_STR);

    $sql->execute();
    return $sql->fetchColumn() > 0 ? true : false;
}

// Función para limpiar (sanitar) los datos de entrada de caracteres extraños
function limpiar_datos($data)
{
    $data = trim($data);
    return $data;
}

// --- Función para cargar la lista de dominios desechables (mantenerla por ahora) ---
function loadDisposableEmailDomains($filePath)
{
    if (!file_exists($filePath)) {
        error_log("Advertencia: El archivo de dominios desechables no se encontró en: " . $filePath);
        return [];
    }
    $domains = array_map('trim', file($filePath, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES));
    return array_filter($domains);
}

// --- Nueva Función de Validación de Teléfono para Venezuela ---
// Esta función valida el FORMATO del número, NO su existencia.
function validateVenezuelanPhoneNumber($phoneNumber)
{
    // Eliminar espacios, guiones y paréntesis
    $phoneNumber = str_replace([' ', '-', '(', ')'], '', $phoneNumber);

    // Regex para números de Venezuela:
    // ^                 Inicio de la cadena
    // (?:\+58)?         Opcionalmente +58 (prefijo internacional)
    // (?:0)?            Opcionalmente un 0 (prefijo nacional)
    // (?:2(?:12|3[4-9]|4[1-9]|5[1-9]|6[1-9]|7[0-8]|8[1-35-9]|9[1-9])|4(?:12|14|16|24|26))
    //                   Grupos de códigos de área/prefijos de móvil válidos.
    //                   212 (Caracas), 2xx (fijos), 4xx (móviles: 412, 414, 416, 424, 426)
    //                   Simplificado: (?:2|4)\d{2} para cualquier 2xx o 4xx
    // \d{7}             7 dígitos finales
    // $                 Fin de la cadena

    // Una regex más sencilla y comúnmente aceptada para VEN:
    // Acepta +58, 04XX, 4XX (sin 0 inicial), 02XX, 2XX (sin 0 inicial)
    // Y luego 7 dígitos.
    // Es un poco más permisiva para no ser tan estricta con códigos de área específicos
    // pero valida la estructura venezolana típica (10-11 dígitos).
    $regex = "/^(?:\+?58|0)?(?:412|414|416|424|426|2(?:12|[3-9]\d|[1-9]\d{2}))\d{7}$/";
    // Regex simplificada para móviles y fijos de 10 dígitos (después de 0/58):
    // ^(?:0|\+58)?(2(?:12|[3-9]\d{2}|[1-9]\d{2})|4(?:12|14|16|24|26))\d{7}$
    // Un poco más robusta que solo 10 dígitos, porque verifica prefijos iniciales comunes.
    // O si quieres algo muy básico (10 dígitos que empiecen por 2 o 4, con prefijos opcionales):
    // $regex = "/^(?:\+?58|0)?([24])\d{9}$/"; // (Esta es más básica y acepta 10 dígitos)

    // Vamos a usar una intermedia que sea razonablemente específica para VEN:
    // Acepta +58 o 0 al inicio (opcional), luego 4xx o 2xx, y 7 dígitos más. Total 10 dígitos funcionales.
    $regex = "/^(?:\+?58|0)?(?:2[0-9]{2}|4[0-9]{2})[0-9]{7}$/";


    // Comprobar formato
    if (!preg_match($regex, $phoneNumber)) {
        return false;
    }

    // Si necesitas validar la longitud final después de limpiar, puedes hacerlo aquí
    // Por ejemplo, números venezolanos suelen ser 10 dígitos (sin 0 inicial o +58)
    // o 11 si cuentan el 0 inicial.
    // Puedes normalizar a 10 dígitos quitando el +58 o 0 inicial si está.
    $normalizedNumber = preg_replace("/^(?:\+?58|0)/", "", $phoneNumber);
    if (strlen($normalizedNumber) !== 10) {
        return false; // Asegura que sean 10 dígitos funcionales
    }

    return true;
}


// Carga la lista de dominios desechables una sola vez al principio del script
$disposableDomainsFilePath = __DIR__ . '/../data/disposable_email_blacklist.txt';
$disposableEmailDomains = loadDisposableEmailDomains($disposableDomainsFilePath);


// Inicializar variables para almacenar los datos y los errores
$name = $tel = $email = $cedula = $pnf = $course = $password = "";
$errs = [];

// Procesar el formulario cuando se envía por POST
header('Content-Type: application/json');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $tel = $_POST["tel"];
    $email = $_POST["email"];
    $cedula = $_POST["cedula"];
    $pnf = $_POST["pnf"];
    $course = $_POST["trayecto"];

    // Validar Nombre
    if (empty($name)) {
        array_push($errs, ["name", "El nombre es obligatorio."]);
    } else {
        $name = limpiar_datos($_POST["name"]);
        // Validar longitud del input DESPUÉS de asegurar que no está vacío
        if (strlen($name) < 5 || strlen($name) > 50) { // <-- ¡Aquí está la corrección!
            array_push($errs, ["name", "El nombre debe contener entre 5 y 50 caracteres."]);
        }
        // Comprobar si el nombre solo contiene letras, espacios y caracteres UTF-8
        else if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u", $name)) { // <-- Agregado \s y modificador u
            array_push($errs, ["name", "Solo se permiten letras y espacios en blanco."]);
        }
    }

    // Validar Teléfono
    if (empty($tel)) {
        array_push($errs, ["tel", "El teléfono es obligatorio."]);
    } else {
        $tel = limpiar_datos($_POST["tel"]);

        if (!validateVenezuelanPhoneNumber($tel)) {
            array_push($errs, ["tel", "Formato de teléfono no válido (ej. 04121234567 o +584121234567)."]);
        } else {
            // Normalizar el número para almacenarlo en la base de datos
            // Esto es crucial para que todos los números se guarden de forma consistente.
            // Ejemplo: Convertir 0412... a +58412... o solo 412... si lo prefieres

            if (substr($tel, 0, 3) !== '+58') { // Comprueba si los primeros 3 caracteres no son '+58'
                // Convertimos 04xx a +584xx si empieza con 0
                if (substr($tel, 0, 1) === '0') {
                    $tel = '+58' . substr($tel, 1); // Quita el 0 y añade +58
                } else {
                    // Si no empieza con +58 ni con 0 (ej. "412..."), asumimos que es venezolano y añadimos +58
                    $tel = '+58' . preg_replace("/^\+?58/", "", $tel);
                }
            }
            // Limpiamos caracteres que no sean dígitos o el '+' inicial, para asegurar formato E.164
            $tel = preg_replace("/[^\d+]/", "", $tel);
            $tel = str_replace('++', '+', $tel); // Evita dobles + si se ingresa +58+58

            // Luego la validación de existencia en DB
            if (validateUserTel($tel, $con)) {
                array_push($errs, ["tel", "Ya existe una cuenta con ese teléfono."]);
            }
        }
    }

    // Validar Correo
    if (empty($email)) {
        array_push($errs, ["email", "El correo electrónico es obligatorio."]);
    } else {
        $email = limpiar_datos($_POST["email"]);

        // Validar longitud primero
        if (strlen($email) < 6 || strlen($email) > 254) {
            array_push($errs, ["email", "El correo debe tener entre 6 y 254 caracteres."]);
        } else {
            $validator = new EmailValidator();

            // Combina validaciones: RFC (formato básico) y DNSCheck (existencia de dominio MX)
            $multipleValidations = new MultipleValidationWithAnd([
                new RFCValidation(),
                new DNSCheckValidation()
            ]);

            // Realiza la validación
            if (!$validator->isValid($email, $multipleValidations)) {
                array_push($errs, ["email", "Formato de correo electrónico no válido o dominio inexistente."]);
            }
            // Validar si es un dominio desechable (mantenemos esto porque egulias no lo hace)
            else if (in_array(substr($email, strpos($email, '@') + 1), $disposableEmailDomains)) {
                array_push($errs, ["email", "No se permiten correos electrónicos de dominios temporales/desechables."]);
            }
            // Validar si el correo ya existe en la base de datos
            else if (validateUserEmail($email, $con)) {
                array_push($errs, ["email", "Ya existe una cuenta con ese correo, inténtalo con otro."]);
            }
        }
    }

    // --- Validaciones para Cédula ---
    if (empty($cedula)) {
        array_push($errs, ["cedula", "La cédula es obligatoria."]);
    } else {
        $email = limpiar_datos($_POST["cedula"]);

        // Remover cualquier caracter no numérico (V-, E-, etc.) para almacenar solo los dígitos
        $cedula_clean = preg_replace('/[^0-9]/', '', $cedula);
        $cedula = $cedula_clean;

        if (!is_numeric($cedula) || strlen($cedula) < 7 || strlen($cedula) > 10) {
            array_push($errs, ["cedula", "Formato de cédula no válido. Debe contener solo números y entre 7 y 10 dígitos."]);
        } 
        else if (validateUserCedula($cedula, $con)) {
            array_push($errs, ["cedula", "Ya existe una cuenta con esa cédula."]);    
        }
    }

    // --- Validaciones para PNF (Carrera) ---
    // Lista de PNFs permitidos (debe coincidir con los valores de tus <option> HTML)
    $allowed_pnfs = ["Informatica", "Electronica", "Mecanica", "Administracion", "Contaduria"];
    if (empty($pnf)) {
        array_push($errs, ["pnf", "Selecciona tu PNF (Carrera)."]);
    } else if (!in_array($pnf, $allowed_pnfs)) {
        array_push($errs, ["pnf", "El PNF seleccionado no es válido."]);
    }

    // --- Validaciones para Trayecto ---
    // Lista de Trayectos permitidos
    $allowed_trayectos = ["1", "2", "3", "4"]; // Si usas "Trayecto I" como valor, cámbialos a esos strings
    if (empty($course)) {
        array_push($errs, ["trayecto", "Selecciona tu Trayecto."]);
    } else if (!in_array($course, $allowed_trayectos)) {
        array_push($errs, ["trayecto", "El Trayecto seleccionado no es válido."]);
    }

    // Inicializa password y passwordRepeat antes de los bloques de validación
    $password = $_POST["password"] ?? '';
    $passwordRepeat = $_POST["password-repeat"] ?? '';

    // --- 4. Validar Contraseña ---
    $password = $_POST["password"] ?? '';
    if (empty($password)) {
        array_push($errs, ["password", "La contraseña es obligatoria."]);
    } else {
        $password = limpiar_datos($password);
        $password_valid_length = true; // Nueva bandera para controlar la longitud

        // Validar longitud de la contraseña
        if (strlen($password) < 8 || strlen($password) > 24) {
            array_push($errs, ["password", "La contraseña debe contener entre 8 y 24 caracteres."]);
            $password_valid_length = false; // La longitud no es válida
        }

        // Validaciones de complejidad (solo si la longitud es válida para evitar mensajes redundantes)
        if ($password_valid_length) { // Solo si la longitud es correcta, evaluamos la complejidad
            if (!preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/[0-9]/", $password) || !preg_match("/[!@#$%^&*()\-_=+{};:,<.>¿¡]/", $password)) {
                array_push($errs, ["password", "La contraseña debe contener al menos un número, una letra mayuscula y una minuscula, y un caracter especial."]);
            }
        }
    }

    // Validar Confirmación de Contraseña
    if (empty($passwordRepeat)) {
        array_push($errs, ["password-repeat", "Debe confirmar la contraseña."]);
    } else {
        $passwordRepeat = limpiar_datos($passwordRepeat);
        // No repetir validaciones de longitud y complejidad aquí, ya se hicieron para $password
        // Solo compara si las contraseñas coinciden DESPUÉS de que la primera sea válida
        if (count($errs) == 0 && $password !== $passwordRepeat) { // Solo si no hay errores en la principal y no coinciden
            array_push($errs, ["password-repeat", "Las contraseñas no coinciden."]);
        }
    }

    // Si no hay errores, se puede proceder a registrar el usuario (aquí solo mostramos un mensaje)
    if (count($errs) == 0) {
        session_start();

        $pass = password_hash($password, PASSWORD_DEFAULT);
        $sql = $con->prepare("INSERT INTO usuarios (nombre, telefono, correo, cedula, pnf, trayecto, clave, fecha_registro) VALUES (?,?,?,?,?,?,?,now())");

        // si hay un error al insertar en la tabla, enviar un mensaje de error
        // en caso de que todo salga bien, no enviar ningun mensaje dde error, el js lo tomara como registro exitoso
        if (!$sql->execute([$name, $tel, $email, $cedula, $pnf, $course, $pass])) array_push($errs, ["main", "EL registro no a sido completado debido a un error."]);
        else $_SESSION['flash_message'] = "¡Registro exitoso!";

        // Es una buena práctica limpiar los campos después de un registro exitoso
        $name = $tel = $email = $cedula = $pnf = $course = $password = "";
    }
}

header('Content-Type: application/json');
echo json_encode($errs);