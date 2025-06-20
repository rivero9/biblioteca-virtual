<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Biblioteca Virtual UPT Aragua</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css"> <!-- Tu CSS principal -->
    <style>
        /* Estilos adicionales para el formulario de recuperación (pueden ir en style.css) */
        .recovery-container {
            background-color: var(--color-white);
            border-radius: 15px;
            box-shadow: var(--shadow-md);
            padding: 40px;
            max-width: 450px;
            width: 90%;
            text-align: center;
        }

        .recovery-container h2 {
            font-size: 28px;
            font-weight: 600;
            color: var(--color-blue-900);
            margin-bottom: 15px;
        }

        .recovery-container p {
            font-size: 14px;
            color: var(--color-gray-600);
            margin-bottom: 25px;
        }

        .recovery-form .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .recovery-form label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--color-gray-700);
        }

        .recovery-form .input {
            width: calc(100% - 24px); /* Ajuste por padding */
            padding: 12px 12px;
            border: 1px solid var(--color-gray-300);
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color 0.3s, box-shadow 0.3s;
            background-color: var(--color-gray-50);
        }

        .recovery-form .input:focus {
            border-color: var(--color-blue-600);
            outline: none;
            box-shadow: 0 0 0 3px rgba(var(--color-blue-600), 0.25);
        }

        .recovery-form .btn-primary {
            margin-top: 20px;
        }

        .back-to-login {
            display: block;
            margin-top: 25px;
            font-size: 14px;
            color: var(--color-blue-600);
            text-decoration: none;
            transition: text-decoration 0.2s ease;
        }

        .back-to-login:hover {
            text-decoration: underline;
        }

        /* Flash message styles (add these to your main style.css as well if not already) */
        .flash-message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            box-sizing: border-box; /* Ensures padding doesn't push it out of width */
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
<body class="app-body">
    <div class="recovery-container">
        <h2>Recuperar Contraseña</h2>
        <p>Introduce tu correo electrónico asociado a tu cuenta para recibir un enlace de restablecimiento de contraseña.</p>

        <?php
        // Este bloque PHP debe ir al principio de tu archivo PHP si lo procesas directamente
        // o incluido en tu plantilla si usas un sistema de plantillas.
        // Asumiendo que has incluido tu lógica de flash messages aquí o en un archivo común
        session_start(); // Asegúrate de que session_start() se llama una vez al principio.

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

        <form action="process_recovery_request.php" method="POST" class="recovery-form">
            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" class="input" required>
                <span class="input-error" id="errEmail"></span>
            </div>
            <button type="submit" class="btn btn-primary">Enviar Enlace de Restablecimiento</button>
        </form>
        <a href="login.php" class="back-to-login">Volver al inicio de sesión</a>
    </div>

    <script>
        // JS para el cierre manual del flash message (si no está en tu script.js)
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
