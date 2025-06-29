<?php 

require __DIR__ . '/config/init.php';

?>
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
                <span class="ico"><i class="fas fa-book-open"></i></span>
                <h1>Rápido, Eficiente y Productivo</h1>
                <p>¡Bienvenido! Registrate para crear tu cuenta y emprender tu viaje con nosotros. Gestiona tus proyectos, colabora con tu equipo y mantente organizado.</p>
                <ul class="benefits-list">
                    <li><i class="fas fa-book-reader"></i> Acceso ilimitado a miles de recursos académicos.</li>
                    <li><i class="fas fa-search-dollar"></i> Búsqueda avanzada y filtros inteligentes para una mejor experiencia.</li>
                    <li><i class="fas fa-download"></i> Gestiona tu historial de lectura y descarga de documentos fácilmente.</li>
                    <li><i class="fas fa-graduation-cap"></i> Contenido exclusivo y herramientas adaptadas a tu PNF y trayecto.</li>
                </ul>
            </div>
            <div class="form-section">
                <h2>Registro</h2>
                <p class="subtitle">Ingresa tus credenciales para acceder a tu cuenta.</p>
                <form action="" method="POST" autocomplete="off" id="form">
                    <div class="form-group">
                        <label for="name">Nombre y Apellido</label>
                        <input type="text" id="name" name="name" placeholder="Ej: Pablo Gonzales" class="input" required>
                        <span id="errName" class="input-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="tel">Telefono</label>
                        <input type="tel" id="tel" name="tel" placeholder="Ej: 0412-1234567" class="input" required>
                        <span id="errTel" class="input-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" id="email" name="email" placeholder="tuemail@ejemplo.com" class="input" required>
                        <span id="errEmail" class="input-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="cedula">Cédula</label>
                        <input type="text" id="cedula" name="cedula" class="input" placeholder="Ej: 12345678" required>
                        <span class="input-error" id="errCedula"></span>
                    </div>
                    <div class="form-group">
                        <label for="pnf">PNF (Carrera)</label>
                        <select id="pnf" name="pnf" class="input" required>
                            <option value="" selected disabled>Selecciona tu PNF</option>
                            <option value="Informatica">Informática</option>
                            <option value="Electronica">Electrónica</option>
                            <option value="Mecanica">Mecánica</option>
                            <option value="Administracion">Administración</option>
                            <option value="Contaduria">Contaduría Pública</option>
                        </select>
                        <span class="input-error" id="errPnf"></span>
                    </div>
                    <div class="form-group">
                        <label for="trayecto">Trayecto</label>
                        <select id="trayecto" name="trayecto" class="input" required>
                            <option value="" selected disabled>Selecciona tu Trayecto</option>
                            <option value="1">Trayecto I</option>
                            <option value="2">Trayecto II</option>
                            <option value="3">Trayecto III</option>
                            <option value="4">Trayecto IV</option>
                        </select>
                        <span class="input-error" id="errTrayecto"></span>
                    </div>
                    <div class="form-group password-field-wrapper">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" class="input" placeholder="Ingresa tu contraseña." required>
                        <span class="toggle-password" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </span>
                        <span class="input-error" id="errPass"></span>
                    </div>
                    <div class="form-group password-field-wrapper">
                        <label for="password-repeat">Confirmar Contraseña</label>
                        <input type="password" id="password-repeat" name="password-repeat" class="input" placeholder="Repite tu contraseña" required>
                        <span class="toggle-password" id="togglePasswordRepeat">
                            <i class="fas fa-eye"></i>
                        </span>
                        <span class="input-error" id="errPassRepeat"></span>
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
    <!-- Loader -->
    <div id="loader-overlay" class="loader-overlay">
        <div class="loader-spinner"></div>
    </div>
</body>

</html>