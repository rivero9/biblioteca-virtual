<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" xintegrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0V4LLanw2qksYuRlEzr+zGxFNsFTQx84uF/2aUtsTfnB2/g2Pz/5v5R6E7g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="styles/normalize.css">
    <link rel="stylesheet" href="styles/form.css">
    <script src="js/register.js" defer></script>
</head>

<body>
    <!-- header -->
    <?php include __DIR__ . "/includes/header.php" ?>

    <div class="container">
        <main class="login-container">
            <div class="info-section">
                <h1>Rápido, Eficiente y Productivo</h1>
                <p>¡Bienvenido! Registrate para crear tu cuenta y emprender tu viaje con nosotros. Gestiona tus proyectos, colabora con tu equipo y mantente organizado.</p>
            </div>
            <div class="form-section">
                <h2>Registro</h2>
                <p class="subtitle">Ingresa tus credenciales para acceder a tu cuenta.</p>
                <form action="" method="POST" autocomplete="off" id="form">
                    <div class="form-group">
                        <label for="name">Nombre y Apellido</label>
                        <input type="text" id="name" name="name" placeholder="Pablo Gonzales" class="input" email>
                        <span id="errName" class="input-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="tel">Telefono</label>
                        <input type="tel" id="tel" name="tel" placeholder="0412000000" class="input" email>
                        <span id="errTel" class="input-error"></span>
                    </div>
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
                    <div class="form-group">
                        <label for="password-repeat">Confirmar Contraseña</label>
                        <input type="password" id="password-repeat" name="password-repeat" placeholder="Repetir contraseña" class="input" email>
                        <span id="errPass-repeat" class="input-error"></span>
                    </div>
                    <button type="submit" class="btn btn-primary">Registrarme</button>
                    <span id="errMain" class="input-error"></span>
                    <p class="signup-link">
                        ¿Ya tienes una cuenta? <a href="login.php">Iniciar Sesion</a>
                    </p>
                </form>
            </div>
        </main>
    </div>

    <!-- footer -->
    <?php include __DIR__ . "/includes/footer.php" ?>
</body>

</html>