<header class="app-header">
    <div class="header-container">
        <!-- Logo y Título -->
        <div class="app-logo-title-group">
            <a href="./"><i class="fas fa-book-open app-icon-book"></i></a>
        </div>
        <!-- Botones de Autenticación -->
        <nav class="auth-buttons-nav">
            <a href="#">Libros</a>
            <?php
            if (isset($_SESSION['user_id'])) {
            ?>
                <!-- Menú Desplegable de Usuario -->
                <div class="user-dropdown">
                    <button class="user-dropdown-trigger" id="userDropdownTrigger">
                        <i class="fas fa-user-circle user-icon"></i>
                        <span><?php echo $_SESSION['user_name'] ?></span> <!-- Placeholder para el nombre del usuario -->
                        <i class="fas fa-chevron-down arrow-icon"></i>
                    </button>
                    <ul class="dropdown-menu" id="userDropdownMenu">
                        <li class="dropdown-menu-item user-info"><?php echo $_SESSION['user_name'] ?></li>
                        <li><a href="#" class="dropdown-menu-item">Mi Perfil</a></li>
                        <li><a href="#" class="dropdown-menu-item">Historial de libros</a></li>
                        <li><a href="#" class="dropdown-menu-item">Configuración</a></li>
                        <li class="dropdown-separator"></li>
                        <li><a href="process/logout.php" class="dropdown-menu-item">Salir</a></li>
                    </ul>
                </div>
                <!-- script menu usuario -->
                <script>
                    // Referencias a elementos del DOM para el menú desplegable de usuario
                    const userDropdownTrigger = document.getElementById('userDropdownTrigger');
                    const userDropdownMenu = document.getElementById('userDropdownMenu');

                    /**
                     * Alterna la visibilidad del menú desplegable del usuario.
                     */
                    function toggleUserDropdown() {
                        if (userDropdownMenu) {
                            userDropdownMenu.classList.toggle('active'); // Alterna la clase 'active'
                        }
                        if (userDropdownTrigger) {
                            userDropdownTrigger.classList.toggle('active'); // Alterna la clase 'active' para rotar la flecha
                        }
                    }

                    /**
                     * Cierra el menú desplegable si se hace clic fuera de él.
                     * @param {Event} event - El evento de clic.
                     */
                    function closeUserDropdown(event) {
                        if (userDropdownMenu && userDropdownTrigger) {
                            // Si el clic no fue dentro del menú ni en el botón que lo activa
                            if (!userDropdownMenu.contains(event.target) && !userDropdownTrigger.contains(event.target)) {
                                userDropdownMenu.classList.remove('active'); // Oculta el menú
                                userDropdownTrigger.classList.remove('active'); // Restablece la flecha
                            }
                        }
                    }

                    // Asignar evento al botón que activa el menú desplegable
                    if (userDropdownTrigger) {
                        userDropdownTrigger.addEventListener('click', toggleUserDropdown);
                    }

                    // Asignar evento al documento para cerrar el menú cuando se hace clic fuera
                    document.addEventListener('click', closeUserDropdown);
                </script>
                <!-- Fin del Menú Desplegable de Usuario -->
            <?php } else { ?>
                <a href="login.php" class="btn-auth btn-login">Iniciar sesion</a>
                <a href="registro.php" class="btn-auth btn-register">Registrarse</a>
            <?php } ?>
        </nav>
    </div>
</header>