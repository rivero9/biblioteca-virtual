<?php

require "../config/connection_db.php";

// connect
$db = new Database();
$con = $db->connect();

// Inicializar variables para almacenar los datos y los errores
$email = $password = "";
// $nameErr = $telErr = $emailErr = $passwordErr = "";
$errs = [];

// Procesar el formulario cuando se envía por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {


    // Validar Correo
    if (empty($_POST["email"])) {
        array_push($errs, ["email", "El correo electrónico es obligatorio."]);
    } else {
        $email = limpiar_datos($_POST["email"]);

        // Validar longitud primero
        if (strlen($email) < 6 || strlen($email) > 254) {
            array_push($errs, ["email", "El correo debe tener entre 6 y 254 caracteres."]);
        }
    }

    // --- Validar Contraseña ---
    $password = $_POST["password"] ?? ''; // Usar el operador ?? para evitar errores si no se envía el campo

    if (empty($password)) {
        array_push($errs, ["password", "La contraseña es obligatoria."]);
    } else {
        $password = limpiar_datos($password); // Sanea la entrada

        // Validar la longitud de la contraseña.
        // Esto debe coincidir con la longitud mínima y máxima que permitiste en el registro.
        if (strlen($password) < 8 || strlen($password) > 24) {
            // Mensaje genérico para no dar pistas sobre la política de contraseñas.
            array_push($errs, ["password", "Usuario o contraseña incorrectos."]); // O un mensaje menos específico como "Longitud de contraseña no válida."
        }
    }

    // Si no hay errores, se puede proceder a iniciar sesion
    if (count($errs) == 0) {


        $sql = $con->prepare("SELECT id, nombre, clave FROM usuarios WHERE correo LIKE :email LIMIT 1");
        $sql->bindParam(':email', $_POST["email"], PDO::PARAM_STR);
        $sql->execute();
        $user = $sql->fetch(PDO::FETCH_ASSOC);

        if ($user && $user["clave"]) {
            if (password_verify($password, $user["clave"])) {
                // iniciar sesion (guardar datos del usuario para el sitio)
                session_start();

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nombre'];
            } else array_push($errs, ["main", "Correo o contraseña incorrectos."]);
        } else array_push($errs, ["main", "Correo o contraseña incorrectos."]);


        // Es una buena práctica limpiar los campos después de un registro exitoso
        $name = $tel = $email = $password = "";
    }
}

// Función para limpiar los datos de entrada
function limpiar_datos($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}


header('Content-Type: application/json');
echo json_encode($errs);
