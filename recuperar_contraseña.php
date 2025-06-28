<?php

require __DIR__ . "/config/init.php";

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Biblioteca Virtual UPT Aragua</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="styles/normalize.css">
    <link rel="stylesheet" href="styles/form.css">
    <link rel="stylesheet" href="styles/recovery.css">
</head>
<body>
    <!-- include header -->
    <?php include "includes/header.php" ?>
    <div class="recovery-container">
        <h2>Recuperar Contraseña</h2>
        <p>Introduce tu correo electrónico asociado a tu cuenta para recibir un enlace de restablecimiento de contraseña.</p>

        <?php
        // Mostrar mensajes flash de sesión
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

        <form action="process/recovery_request.php" method="POST" class="recovery-form">
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" class="input" placeholder="correo@gmail.com" required>
                <span class="input-error" id="errEmail"></span>
            </div>
            <button type="submit" class="btn btn-primary">Enviar Enlace de Restablecimiento</button>
        </form>
        <a href="login.php" class="back-to-login">Volver al inicio de sesión</a>
    </div>
    
    <!-- include footer -->
    <?php include "includes/footer.php" ?>

    <script>
        // JS para el cierre manual del flash message
        document.addEventListener('DOMContentLoaded', function() {
            const flashMessage = document.getElementById('flashMessage');
            if (flashMessage) {
                const closeBtn = flashMessage.querySelector('.close-btn');
                if (closeBtn) {
                    closeBtn.addEventListener('click', function() {
                        flashMessage.style.display = 'none';
                    });
                }
            }
        });
    </script>
</body>
</html>
