<?php

// verificar inicio de sesion
require __DIR__ . "/config/init.php";
require __DIR__ . "/config/connection_db.php";

// connect
$db = new Database();
$con = $db->connect();

require __DIR__ . "/process/auth_check.php";

$user_name = $currentUser['nombre'];
$user_cedula = $currentUser['cedula'];
$user_email = $currentUser['correo'];
$user_tel = $currentUser['telefono'];
$user_pnf = $currentUser['pnf'];
$user_course = $currentUser['trayecto'];

// --- Validar que el pnf en la DB sea válido
$validPnf = false;

switch ($user_course) {
    case 'Informatica':
        $validPnf = true;
        break;
    case 'Electronica':
        $validCourse = true;
        break;
    case 'Mecanica':
        $validPnf = true;
        break;
    case 'Administracion':
        $validPnf = true;
        break;
    case 'Contaduria':
        $validPnf = true;
        break;
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuario - Biblioteca Virtual UPT Aragua</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="styles/normalize.css">
    <link rel="stylesheet" href="styles/dashboard.css">
    <script src="js/dashboard.js" defer></script>
</head>

<body class="app-body">
    <div class="dashboard-container">
        <!-- Barra Lateral (Sidebar) -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="#" class="sidebar-logo">
                    <i class="fas fa-book"></i> Biblioteca
                </a>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="./"><i class="fas fa-home"></i> Inicio</a></li>
                    <li><a href="#" data-section="overview" class="active"><i class="fas fa-th-large"></i> Overview</a></li>
                    <li><a href="#" data-section="profile"><i class="fas fa-user-circle"></i> Mi Perfil</a></li>
                    <li><a href="#" data-section="downloads"><i class="fas fa-download"></i> Historial Descargas</a></li>
                    <!-- Elementos adicionales del dashboard de la imagen, adaptados -->
                    <li><a href="#" data-section="books"><i class="fas fa-book-open"></i> Explorar Libros</a></li>
                    <li><a href="#" data-section="requests"><i class="fas fa-paper-plane"></i> Solicitudes</a></li>
                    <li><a href="#" data-section="stats"><i class="fas fa-chart-bar"></i> Mis Estadísticas</a></li>
                    <li class="separator"></li> <!-- Separador visual -->
                    <li><a href="#" data-section="help"><i class="fas fa-question-circle"></i> Ayuda y Soporte</a></li>
                    <li><a href="#" data-section="settings"><i class="fas fa-cogs"></i> Configuración</a></li>
                    <li><a href="process/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Contenido Principal -->
        <main class="main">
            <!-- Barra Superior (Navbar) -->
            <header class="navbar">
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Buscar en la Biblioteca...">
                </div>
                <div class="navbar-right">
                    <div class="navbar-icon-group">
                        <i class="fas fa-bell navbar-icon"></i>
                        <i class="fas fa-question-circle navbar-icon"></i>
                    </div>
                    <div class="user-profile">
                        <i class="fas fa-user-circle user-icon user-profile-avatar"></i>
                        <span class="user-profile-name"><?php echo $user_name ?></span>
                    </div>
                </div>
            </header>

            <!-- Secciones de Contenido Dinámico -->
            <div class="content-area">
                <section id="overview" class="content-section active">
                    <h2>Overview</h2>
                    <div class="card-grid">
                        <div class="card">
                            <h3>Total Descargas</h3>
                            <div class="metric-value">1245</div>
                            <div class="metric-description increase"><i class="fas fa-arrow-up"></i> Aumento del 15% vs el mes pasado</div>
                            <div class="card-chart-placeholder">Gráfico de Descargas</div>
                        </div>
                        <div class="card">
                            <h3>Libros Leídos</h3>
                            <div class="metric-value">320</div>
                            <div class="metric-description decrease"><i class="fas fa-arrow-down"></i> Disminución del 3% vs el mes pasado</div>
                            <div class="card-chart-placeholder">Gráfico de Lecturas</div>
                        </div>
                        <div class="card">
                            <h3>Mis Libros Recientes</h3>
                            <div class="card-image-container">
                                <img src="https://placehold.co/300x150/F4F7FC/4B5563?text=Portada+Libro+1" alt="Portada de libro 1">
                            </div>
                            <p style="color: var(--color-medium-text); margin-bottom: 5px;">Introducción a Python</p>
                            <a href="#" class="button">Ver Libro</a>
                        </div>
                        <div class="card">
                            <h3>Libro Recomendado</h3>
                            <div class="card-image-container">
                                <img src="https://placehold.co/300x150/F4F7FC/4B5563?text=Portada+Libro+2" alt="Portada de libro 2">
                            </div>
                            <p style="color: var(--color-medium-text); margin-bottom: 5px;">Física Cuántica</p>
                            <a href="#" class="button">Descargar Ahora</a>
                        </div>
                    </div>
                </section>

                <section id="profile" class="content-section">
                    <h2>Mi Perfil</h2>
                    <div class="card-grid">
                        <div class="card">
                            <h3>Datos Personales</h3>
                            <p>Actualiza tu información personal para mantener tu cuenta al día.</p>
                            <form action="update.php" id="user-data" style="margin-top: 20px;">
                                <div style="margin-bottom: 15px;">
                                    <label for="profile-name" style="display: block; margin-bottom: 5px; font-size: 14px; color: var(--color-medium-text);">Nombre y Apellido</label>
                                    <input type="text" name="name" id="profile-name" class="form-input" value="<?php echo $user_name; ?>">
                                    <span class="input-error" id="errName"></span>
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label for="profile-tel" style="display: block; margin-bottom: 5px; font-size: 14px; color: var(--color-medium-text);">Teléfono</label>
                                    <input type="text" name="tel" id="profile-tel" class="form-input" value="<?php echo $user_tel; ?>">
                                    <span class="input-error" id="errTel"></span>
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label for="profile-email" style="display: block; margin-bottom: 5px; font-size: 14px; color: var(--color-medium-text);">Correo Electrónico</label>
                                    <input type="email" name="email" id="profile-email" class="form-input" value="<?php echo $user_email; ?>">
                                    <span class="input-error" id="errEmail"></span>
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label for="profile-cedula" style="display: block; margin-bottom: 5px; font-size: 14px; color: var(--color-medium-text);">Cédula</label>
                                    <input type="text" name="cedula" id="profile-cedula" class="form-input" value="<?php echo $user_cedula; ?>">
                                    <span class="input-error" id="errCedula"></span>
                                </div>
                                <div style="margin-bottom: 15px;">
                                    <label for="profile-pnf" style="display: block; margin-bottom: 5px; font-size: 14px; color: var(--color-medium-text);">PNF (Carrera)</label>
                                    <select name="pnf" id="profile-pnf" class="form-input">
                                        <?php if (!$validPnf) echo '<option value="" selected disabled>Selecciona tu PNF</option>'; ?>
                                        <option value="Informatica" <?php if ($user_pnf == "Informatica") echo "selected"; ?>>Informática</option>
                                        <option value="Electronica" <?php if ($user_pnf == "Electronica") echo "selected"; ?>>Electrónica</option>
                                        <option value="Mecanica" <?php if ($user_pnf == "Mecanica") echo "selected"; ?>>Mecánica</option>
                                        <option value="Administracion" <?php if ($user_pnf == "Administracion") echo "selected"; ?>>Administración</option>
                                        <option value="Contaduria" <?php if ($user_pnf == "Contaduria") echo "selected"; ?>>Contaduría Pública</option>
                                    </select>
                                    <span class="input-error" id="errPnf"></span>
                                </div>
                                <div style="margin-bottom: 20px;">
                                    <label for="profile-trayecto" style="display: block; margin-bottom: 5px; font-size: 14px; color: var(--color-medium-text);">Trayecto</label>
                                    <select name="trayecto" id="profile-trayecto" class="form-input">
                                        <?php if ($user_course < 1 || $user_course > 4) echo '<option value="" selected disabled>Selecciona tu Trayecto</option>'; ?>
                                        <option value="1" <?php if ($user_course == 1) echo "selected"; ?>>Trayecto I</option>
                                        <option value="2" <?php if ($user_course == 2) echo "selected"; ?>>Trayecto II</option>
                                        <option value="3" <?php if ($user_course == 3) echo "selected"; ?>>Trayecto III</option>
                                        <option value="4" <?php if ($user_course == 4) echo "selected"; ?>>Trayecto IV</option>
                                    </select>
                                    <span class="input-error" id="errTrayecto"></span>
                                </div>
                                <button type="submit" class="button">Guardar Cambios</button>
                            </form>
                        </div>
                        <div class="profile-right-column">
                            <div class="card">
                                <h3>Seguridad de la Cuenta</h3>
                                <p>Gestiona las configuraciones de seguridad de tu cuenta.</p>
                                <a href="#" class="button" style="background-color: var(--color-gray-600); margin-top: 15px;">Historial de Sesiones</a>
                            </div>
                            <div class="card">
                                <h3>Cambiar Contraseña</h3>
                                <p>Mantén tu cuenta segura actualizando tu contraseña regularmente.</p>
                                <form id="form-password-update" style="margin-top: 20px;">
                                    <div style="margin-bottom: 15px;">
                                        <label for="current-password" style="display: block; margin-bottom: 5px; font-size: 14px; color: var(--color-medium-text);">Contraseña Actual</label>
                                        <div class="password-input-wrapper">
                                            <input type="password" id="current-password" name="current_password" class="form-input">
                                            <i class="fas fa-eye toggle-password" data-target="current-password"></i>
                                        </div>
                                        <span class="error-message" id="errCurrentPassword" style="color: var(--color-red); font-size: 0.85em; margin-top: 5px; display: block;"></span>
                                    </div>
                                    <div style="margin-bottom: 15px;">
                                        <label for="new-password" style="display: block; margin-bottom: 5px; font-size: 14px; color: var(--color-medium-text);">Nueva Contraseña</label>
                                        <div class="password-input-wrapper">
                                            <input type="password" id="new-password" name="new_password" class="form-input">
                                            <i class="fas fa-eye toggle-password" data-target="new-password"></i>
                                        </div>
                                        <span class="error-message" id="errNewPassword" style="color: var(--color-red); font-size: 0.85em; margin-top: 5px; display: block;"></span>
                                    </div>
                                    <div style="margin-bottom: 20px;">
                                        <label for="confirm-password" style="display: block; margin-bottom: 5px; font-size: 14px; color: var(--color-medium-text);">Confirmar Nueva Contraseña</label>
                                        <div class="password-input-wrapper">
                                            <input type="password" id="confirm-password" name="confirm_password" class="form-input">
                                            <i class="fas fa-eye toggle-password" data-target="confirm-password"></i>
                                        </div>
                                        <span class="error-message" id="errConfirmPassword" style="color: var(--color-red); font-size: 0.85em; margin-top: 5px; display: block;"></span>
                                    </div>
                                    <button type="submit" class="button">Actualizar Contraseña</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="downloads" class="content-section">
                    <h2>Historial de Descargas</h2>
                    <div class="card">
                        <h3>Tus Libros Descargados</h3>
                        <table class="downloads-table">
                            <thead>
                                <tr>
                                    <th>Título del Libro</th>
                                    <th>Autor</th>
                                    <th>Fecha de Descarga</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Introducción a la Programación con Python</td>
                                    <td>John Doe</td>
                                    <td>2024-06-25</td>
                                    <td><a href="#" style="color: var(--color-primary-blue); text-decoration: none;">Descargar</a></td>
                                </tr>
                                <tr>
                                    <td>Fundamentos de Redes y Comunicaciones</td>
                                    <td>Jane Smith</td>
                                    <td>2024-06-20</td>
                                    <td><a href="#" style="color: var(--color-primary-blue); text-decoration: none;">Descargar</a></td>
                                </tr>
                                <tr>
                                    <td>Diseño de Bases de Datos Relacionales</td>
                                    <td>Alice Johnson</td>
                                    <td>2024-06-18</td>
                                    <td><a href="#" style="color: var(--color-primary-blue); text-decoration: none;">Descargar</a></td>
                                </tr>
                                <tr>
                                    <td>Metodologías Ágiles para el Desarrollo de Software</td>
                                    <td>Bob Williams</td>
                                    <td>2024-06-15</td>
                                    <td><a href="#" style="color: var(--color-primary-blue); text-decoration: none;">Descargar</a></td>
                                </tr>
                                <tr>
                                    <td>Cálculo Integral para Ingeniería</td>
                                    <td>Dr. Elara Vance</td>
                                    <td>2024-06-10</td>
                                    <td><a href="#" style="color: var(--color-primary-blue); text-decoration: none;">Descargar</a></td>
                                </tr>
                            </tbody>
                        </table>
                        <a href="#" class="button" style="margin-top: 25px;">Ver todas las descargas</a>
                    </div>
                </section>

                <!-- Secciones adicionales del dashboard replicadas -->
                <section id="books" class="content-section">
                    <h2>Explorar Libros</h2>
                    <div class="card">
                        <h3>Novedades y Categorías</h3>
                        <p>Descubre los últimos libros añadidos y explora por categorías.</p>
                        <a href="#" class="button">Ir al Catálogo</a>
                    </div>
                </section>

                <section id="requests" class="content-section">
                    <h2>Mis Solicitudes</h2>
                    <div class="card">
                        <h3>Estado de Solicitudes Pendientes</h3>
                        <p>Aquí puedes ver el estado de tus solicitudes de libros o recursos especiales.</p>
                        <a href="#" class="button">Ver Solicitudes</a>
                    </div>
                </section>

                <section id="stats" class="content-section">
                    <h2>Mis Estadísticas</h2>
                    <div class="card">
                        <h3>Tus Hábitos de Lectura</h3>
                        <p>Visualiza tus estadísticas de lectura, temas favoritos y progreso.</p>
                        <a href="#" class="button">Ver Estadísticas Detalladas</a>
                    </div>
                </section>

                <section id="help" class="content-section">
                    <h2>Ayuda y Soporte</h2>
                    <div class="card">
                        <h3>Centro de Ayuda</h3>
                        <p>Encuentra respuestas a tus preguntas frecuentes o contacta con soporte técnico.</p>
                        <a href="#" class="button">Visitar Centro de Ayuda</a>
                    </div>
                </section>

                <section id="settings" class="content-section">
                    <h2>Configuración</h2>
                    <div class="card">
                        <h3>Ajustes Generales de la Cuenta</h3>
                        <p>Gestiona tus preferencias de notificación, privacidad y seguridad.</p>
                        <a href="#" class="button">Gestionar Ajustes</a>
                    </div>
                </section>
            </div>
        </main>
    </div>
    <!-- Loader -->
    <div id="loader-overlay" class="loader-overlay">
        <div class="loader-spinner"></div>
    </div>
    <!-- Modal para mensajes de éxito/error -->
    <div id="message-modal" class="message-modal">
        <div class="message-content">
            <span id="close-modal-btn" class="close-btn">&times;</span>
            <p id="message-text"></p>
        </div>
    </div>
</body>

</html>