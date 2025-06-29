<header class="app-header">
    <div class="header-container">
        <!-- Logo y Título -->
        <div class="app-logo-title-group">
            <a href="./"><i class="fas fa-book-open app-icon-book"></i></a>
        </div>
        <!-- Botones de Autenticación -->
        <nav class="auth-buttons-nav">
            <a href="./">Inicio</a>
            <a href="#">Libros</a>
            <?php
            if (isset($_SESSION['user_id'])) {
            ?>
                <!-- Menú Desplegable de Usuario -->
                <div class="user">
                    <a href="dashboard.php" class="user-trigger" id="userDropdownTrigger">
                        <span><?php echo $_SESSION['user_name'] ?? 'usuario' ?></span>
                        <i class="fas fa-user-circle user-icon"></i>
                    </a>
                </div>
            <?php } else { ?>
                <a href="login.php" class="btn-auth btn-login">Iniciar sesion</a>
                <a href="registro.php" class="btn-auth btn-register">Registrarse</a>
            <?php } ?>
        </nav>
    </div>
</header>