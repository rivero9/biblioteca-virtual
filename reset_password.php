<?php

require __DIR__ . "/config/init.php";
require_once __DIR__ . '/config/connection_db.php';


// connect
$db = new Database();
$con = $db->connect();

// functions
function limpiar_datos($data)
{
    $data = trim($data);
    return $data;
}

$token = $_GET['token'] ?? '';
$errors = [];
$show_reset_form = false; // Bandera para controlar si se muestra el formulario de nueva contraseña
$user_id = null; // Para almacenar el ID del usuario si el token es válido

// 1. Validar y limpiar el token de la URL
if (empty($token) || !preg_match('/^[a-f0-9]{64}$/', $token)) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'El enlace de restablecimiento es inválido o ha expirado.'];
    header('Location: recuperar_contraseña.php');
    exit();
}

// 2. Buscar el token en la base de datos y verificar su validez
$stmt = $con->prepare("SELECT user_id, expires_at, used FROM password_resets WHERE token = :token");
$stmt->bindParam(":token", $token, PDO::PARAM_STR);
$stmt->execute();

if ($stmt->rowCount() === 1) {
    $reset_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $user_id = $reset_data['user_id'];
    $expires_at = strtotime($reset_data['expires_at']);
    $is_used = $reset_data['used'];

    // Verificar si el token ha expirado o ya ha sido usado
    if ($is_used || time() > $expires_at) {
        $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'El enlace de restablecimiento es inválido o ha expirado.'];
        header('Location: recuperar_contraseña.php');
        exit();
    } else {
        // Token válido, mostrar el formulario para la nueva contraseña
        $show_reset_form = true;
    }
} else {
    // Token no encontrado en la BD
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'El enlace de restablecimiento es inválido o ha expirado.'];
    header('Location: recuperar_contraseña.php');
    exit();
}

// --- Lógica para cuando se envía el formulario de nueva contraseña (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $show_reset_form && $user_id !== null) {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validaciones de la nueva contraseña (igual que en el registro)
    if (empty($new_password) || empty($confirm_password)) {
        array_push($errors, "Ambos campos de contraseña son obligatorios.");
    } else if ($new_password !== $confirm_password) {
        array_push($errors, "Las contraseñas no coinciden.");
    } else {
        $new_password = limpiar_datos($new_password);
        if (strlen($new_password) < 8 || strlen($new_password) > 24) {
            array_push($errors, "La contraseña debe contener entre 8 y 24 caracteres.");
        }
        if (!preg_match("/[A-Z]/", $new_password) || !preg_match("/[a-z]/", $new_password) || !preg_match("/[0-9]/", $new_password) || !preg_match("/[!@#$%^&*()\-_=+{};:,<.>¿¡]/", $new_password)) {
            array_push($errors, "La contraseña debe contener al menos un número, una letra mayúscula y una minúscula, y un carácter especial.");
        }
    }

    if (empty($errors)) {
        // Hashear la nueva contraseña
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        // Actualizar la contraseña del usuario en la tabla 'usuarios'
        $stmt_update_pass = $con->prepare("UPDATE usuarios SET clave = ? WHERE id = ?");

        if ($stmt_update_pass->execute([$hashed_password, $user_id])) {
            // Marcar el token como usado para evitar su reutilización
            $stmt_mark_used = $con->prepare("UPDATE password_resets SET used = TRUE WHERE token = :token");
            $stmt_mark_used->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt_mark_used->execute();

            $_SESSION['flash_message'] = ['type' => 'success', 'message' => '¡Tu contraseña ha sido restablecida con éxito! Ya puedes iniciar sesión con tu nueva contraseña.'];
            header('Location: recuperar_contraseña.php');
            exit();
        } else {
            error_log("Error al actualizar contraseña: " . $stmt_update_pass->error . " para user_id: " . $user_id);
            array_push($errors, "Ocurrió un error al actualizar la contraseña. Por favor, inténtalo de nuevo.");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - Biblioteca Virtual UPT Aragua</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="styles/normalize.css">
    <link rel="stylesheet" href="styles/form.css">
    <script src="js/toggle_password.js" defer></script>
    <style>
        /* Estilos adicionales para el formulario de restablecimiento */
        .reset-container {
            background-color: var(--color-white);
            border-radius: 15px;
            box-shadow: var(--shadow-md);
            padding: 40px;
            max-width: 450px;
            width: 90%;
            text-align: center;
            margin: 50px auto; /* Centrar en la página */
            box-shadow: 0 18px 50px rgba(0, 0, 0, 0.12);
        }

        .reset-container::before {
            width: 450px;
            height: 450px;
            background: linear-gradient( 135deg, var(--color-blue-900), var(--color-light-blue-gradient-end) );
            top: -120px;
            left: -180px;
            content: "";
            position: absolute;
            border-radius: 50%;
            opacity: 0.5;
            z-index: -1;
            filter: blur(70px);
        }

        .reset-container h2 {
            font-size: 28px;
            font-weight: 600;
            color: var(--color-blue-900);
            margin-bottom: 15px;
        }

        .reset-container p {
            font-size: 14px;
            color: var(--color-gray-600);
            margin-bottom: 25px;
        }

        .reset-form .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .reset-form label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--color-gray-700);
        }

        .reset-form .input:focus {
            border-color: var(--color-blue-600);
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
        }

        .reset-form .btn-primary {
            margin-top: 20px;
            display: block; /* Asegura que ocupe todo el ancho */
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.1s;
            text-align: center;
            text-decoration: none;
            background-color: var(--color-blue-600);
            color: var(--color-white);
        }
        .reset-form .btn-primary:hover {
            background-color: var(--color-blue-700);
        }

        .reset-form .input-error {
            color: #e74c3c; /* Red color for error messages */
            font-size: 13px;
            margin-top: 5px;
            display: block;
        }

        /* Flash message styles (colocar en tu style.css principal) */
        .flash-message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            box-sizing: border-box;
        }
        .flash-message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .flash-message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .flash-message.warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        .flash-message .fa-icon {
            font-size: 1.2em;
        }
        .flash-message .close-btn {
            margin-left: auto;
            cursor: pointer;
            font-size: 1.5em;
            line-height: 1;
            background: none;
            border: none;
            color: inherit;
            opacity: 0.7;
            transition: opacity 0.2s ease;
        }
        .flash-message .close-btn:hover {
            opacity: 1;
        }
    </style>
</head>
<body>
    <!-- header inlcuido -->
    <?php include __DIR__ . "/includes/header.php" ?>
    <div class="reset-container">
        <h2>Establecer Nueva Contraseña</h2>
        <p>Por favor, introduce tu nueva contraseña.</p>

        <?php
        // Mostrar mensajes de error de validación del formulario de nueva contraseña
        if (!empty($errors)) {
            foreach ($errors as $error_msg) { // Cambié el nombre de la variable para evitar conflicto con $errors array
                echo '<div class="flash-message error">' . htmlspecialchars($error_msg) . '</div>';
            }
        }

        // Mostrar mensajes flash de sesión (ej. token expirado/inválido)
        if (isset($_SESSION['flash_message'])) {
            $message_data = $_SESSION['flash_message'];
            $type = htmlspecialchars($message_data['type']);
            $message = htmlspecialchars($message_data['message']);
            $icon_class = '';

            switch ($type) {
                case 'success':
                    $icon_class = 'fas fa-check-circle';
                    break;
                case 'error':
                    $icon_class = 'fas fa-times-circle';
                    break;
                case 'warning':
                    $icon_class = 'fas fa-exclamation-triangle';
                    break;
                default:
                    $icon_class = 'fas fa-info-circle';
            }
        ?>
            <div class="flash-message <?php echo $type; ?>" id="flashMessage">
                <i class="<?php echo $icon_class; ?> fa-icon"></i>
                <span><?php echo $message; ?></span>
                <button class="close-btn" onclick="document.getElementById('flashMessage').style.display='none';">&times;</button>
            </div>
        <?php
            unset($_SESSION['flash_message']); // Elimina el mensaje después de mostrarlo
        }
        ?>

        <?php if ($show_reset_form): ?>
        <form action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST" class="reset-form">
            <div class="form-group password-field-wrapper">
                <label for="new_password">Nueva Contraseña</label>
                <input type="password" id="new_password" name="new_password" class="input" placeholder="Ingresa tu nueva contraseña" required>
                <span class="toggle-password" id="togglePassword">
                    <i class="fas fa-eye"></i>
                </span>
            </div>
            <div class="form-group password-field-wrapper">
                <label for="confirm_password">Confirmar Contraseña</label>
                <input type="password" id="confirm_password" name="confirm_password" class="input" placeholder="Repite tu contraseña" required>
                <span class="toggle-password" id="togglePasswordRepeat">
                    <i class="fas fa-eye"></i>
                </span>
            </div>
            <button type="submit" class="btn btn-primary">Restablecer Contraseña</button>
        </form>
        <?php endif; ?>

        <a href="login.php" class="back-to-login">Volver al inicio de sesión</a>
    </div>
    <!-- footer inlcuido -->
    <?php include __DIR__ . "/includes/footer.php" ?>
</body>
</html>
