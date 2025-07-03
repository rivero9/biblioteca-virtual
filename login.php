<?php 

require __DIR__ . '/config/init.php';

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Inicio de Sesión</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" xintegrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0V4LLanw2qksYuRlEzr+zGxFNsFTQx84uF/2aUtsTfnB2/g2Pz/5v5R6E7g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="styles/normalize.css">
    <link rel="stylesheet" href="styles/form.css">
    <script src="js/login.js" defer></script>
</head>

<body>
    <!-- header -->
    <?php include __DIR__ . "/includes/header.php" ?>

    <div class="container">
        <div class="login-container">
            <div class="info-section">
                <span class="ico"><i class="fas fa-book-open"></i></span>
                <h1>Rápido, Eficiente y Productivo</h1>
                <p>¡Bienvenido de nuevo! Inicia sesión para continuar tu acceso a la Biblioteca Virtual. Tus recursos te esperan, listos para tu estudio y consulta.</p>
            </div>
            <div class="form-section">
                <h2>Iniciar Sesión</h2>
                <p class="subtitle">Ingresa tus credenciales para acceder a tu cuenta.</p>

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
                <form action="" method="POST" id="form">
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" placeholder="tuemail@ejemplo.com" class="input" require>
                        <span id="errEmail" class="input-error"></span>
                    </div>
                    <div class="form-group password-field-wrapper">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" class="input" require>
                        <button type="button" class="toggle-password" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                        <span id="errPass" class="input-error"></span>
                    </div>
                    <button type="submit" class="btn btn-primary">Iniciar Sesion</button>
                    <span id="errMain" class="input-error"></span>
                    <p class="signup-link">
                        ¿Olvidaste tu contraseña? <a href="recuperar_contraseña.php">Recuperar</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <!-- footer -->
    <?php include __DIR__ . "/includes/footer.php" ?>
    <!-- Loader -->
    <div id="loader-overlay" class="loader-overlay">
        <div class="loader-spinner"></div>
    </div>
</body>

</html>