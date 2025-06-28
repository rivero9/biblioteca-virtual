<?php

require __DIR__ . "/../config/init.php";
require_once __DIR__ . '/../config/connection_db.php';

// connect
$db = new Database();
$con = $db->connect();

function limpiar_datos($data)
{
    $data = trim($data);
    return $data;
}

// --- Configuración de PHPMailer (requerido para el envío de correos) ---
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = $_POST['email'] ?? '';

// 1. Validación del lado del servidor (básica)
if (empty($email)) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Por favor, introduce un correo electrónico.'];
    header('Location: ../recuperar_contraseña.php'); // Redirige de vuelta con el mensaje
    exit();
} else {
    $email = limpiar_datos($email);
    // Solo valida el formato básico de email. La existencia la comprobaremos más adelante.
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Mensaje genérico por seguridad: no indicamos si el correo existe o no.
        $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Si tu correo electrónico existe en nuestro sistema, recibirás un enlace para restablecer tu contraseña.'];
        header('Location: ../recuperar_contraseña.php');
        exit();
    }
}

// 2. Buscar al usuario en la base de datos
$stmt = $con->prepare("SELECT id, nombre FROM usuarios WHERE correo LIKE :email limit 1");
$stmt->bindParam(":email", $email, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && $user["id"]) {
    $user_id = $user['id'];
    $user_name = $user['nombre'];

    // 3. Generar un token único y seguro
    $token = bin2hex(random_bytes(32)); // Genera un token de 64 caracteres hex
    $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour')); // Expira en 1 hora

    // 4. Guardar el token en la base de datos
    // Primero, eliminar tokens antiguos o no usados para este usuario para mantener limpia la tabla.
    $stmt_delete_old = $con->prepare("DELETE FROM password_resets WHERE user_id = :user AND used = FALSE");
    $stmt_delete_old->bindParam(":user", $user_id, PDO::PARAM_STR);
    $stmt_delete_old->execute();

    $stmt_insert = $con->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");

    if ($stmt_insert->execute([$user_id, $token, $expires_at])) {
        // 5. Enviar correo electrónico con el enlace de restablecimiento
        $reset_link = getenv('BASE_URL')."/reset_password.php?token=" . $token;

        $subject = "Restablecimiento de Contraseña para tu cuenta de Biblioteca Virtual UPT Aragua";
        $body = "Hola " . htmlspecialchars($user_name) . ",<br><br>"
              . "Has solicitado un restablecimiento de contraseña para tu cuenta de Biblioteca Virtual UPT Aragua.<br>"
              . "Haz clic en el siguiente enlace para restablecer tu contraseña:<br><br>"
              . "<a href='" . htmlspecialchars($reset_link) . "'>Restablecer mi Contraseña</a><br><br>"
              . "Este enlace expirará en 1 hora. Si no solicitaste esto, por favor, ignora este correo.<br><br>"
              . "Saludos,<br>Equipo de Biblioteca Virtual UPT Aragua";

        // --- Lógica de envío de correo con PHPMailer ---
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = getenv('MAIL_HOST'); // Específica tu servidor SMTP (ej. smtp.gmail.com, mail.yourdomain.com)
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('MAIL_USERNAME'); // Tu correo electrónico SMTP
            $mail->Password   = getenv('MAIL_PASSWORD');    // La contraseña de tu correo SMTP (¡usa una contraseña de aplicación si es Gmail!)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // O PHPMailer::ENCRYPTION_SMTPS para puerto 465
            $mail->Port       = getenv('MAIL_PORT'); // Puerto SMTP (587 para STARTTLS, 465 para SMTPS)
            $mail->CharSet    = 'UTF-8'; // Muy importante para caracteres especiales

            //Recipients
            $mail->setFrom(getenv('MAIL_FROM_ADDRESS'), getenv('MAIL_FROM_NAME')); // Tu dirección de correo de envío
            $mail->addAddress($email, $user_name); // Destinatario

            //Content
            $mail->isHTML(true); // Formato de correo HTML
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags($body); // Versión en texto plano para clientes sin HTML

            $mail->send();
            $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Si tu correo electrónico existe en nuestro sistema, recibirás un enlace para restablecer tu contraseña.'];

        } catch (Exception $e) {
            // En producción, solo registra el error, no lo muestres al usuario.
            error_log("Error al enviar correo de restablecimiento: " . $mail->ErrorInfo . " para " . $email);
            $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Ocurrió un error al enviar el correo. Por favor, inténtalo de nuevo más tarde.'];
        }

    } else {
        // Error al guardar el token
        error_log("Error al guardar token de restablecimiento: " . $stmt_insert->error);
        $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Ocurrió un error interno. Por favor, inténtalo de nuevo más tarde.'];
    }
} else {
    // Usuario no encontrado. Mismo mensaje genérico por seguridad.
    $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Si tu correo electrónico existe en nuestro sistema, recibirás un enlace para restablecer tu contraseña.'];
}

header('Location: ../recuperar_contraseña.php');

exit();
?>
