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
    <style>
        /* flash message */
        .flash-message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .flash-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
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
    <!-- header -->
    <?php include __DIR__ . "/includes/header.php" ?>

    <div class="container">
        <div class="login-container">
            <div class="info-section">
                <h1>Rápido, Eficiente y Productivo</h1>
                <p>¡Bienvenido de nuevo! Inicia sesión para acceder a tu cuenta y continuar tu viaje con nosotros. Gestiona tus proyectos, colabora con tu equipo y mantente organizado.</p>
            </div>
            <div class="form-section">
                <h2>Iniciar Sesión</h2>
                <p class="subtitle">Ingresa tus credenciales para acceder a tu cuenta.</p>

                <?php
                // Verifica si hay un mensaje flash en la sesión
                if (isset($_SESSION['flash_message'])) {
                    $message = htmlspecialchars($_SESSION['flash_message']);
                ?>
                    <div class="flash-message" id="flashMessage">
                        <i class="fas fa-check-circle fa-icon"></i>
                        <span><?php echo $message; ?></span>
                        <button class="close-btn" onclick="document.getElementById('flashMessage').style.display='none';">&times;</button>
                    </div>
                <?php
                    // Una vez que el mensaje se ha mostrado, elimínalo de la sesión para que no se muestre de nuevo
                    unset($_SESSION['flash_message']);
                }
                ?>
                <form action="" method="POST" autocomplete="off" id="form">
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" placeholder="tuemail@ejemplo.com" class="input" email>
                        <span id="errEmail" class="input-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" class="input" email>
                        <span id="errPass" class="input-error"></span>
                    </div>
                    <button type="submit" class="btn btn-primary">Iniciar Sesion</button>
                    <span id="errMain" class="input-error"></span>
                    <p class="signup-link">
                        ¿Olvidaste tu contraseña? <a href="registro.php">Recuperar</a>
                    </p>
                    <p class="signup-link" style>- o -</p>
                    <p class="signup-link">
                        ¿No tienes cuenta? <a href="registro.php">Registrarme</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <!-- footer -->
    <?php include __DIR__ . "/includes/footer.php" ?>
</body>

</html>