<?php

require_once __DIR__ . "/config/init.php";
require_once __DIR__ . "/config/connection_db.php";

try {
    $db = new Database();
    $con = $db->connect();
    if (!$con) {
        throw new Exception("No se pudo establecer la conexión a la base de datos.");
    }
} catch (Exception $e) {
    echo "<!DOCTYPE html><html><head><title>Error</title><style>body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f8d7da; color: #721c24; text-align: center; } .error-box { padding: 20px; border: 1px solid #f5c6cb; border-radius: 8px; background-color: #f8d7da; }</style></head><body><div class='error-box'><h1>Error del Servidor</h1><p>Lo sentimos, no se pudo conectar a la base de datos. Por favor, inténtalo más tarde.</p><p>Error: " . htmlspecialchars($e->getMessage()) . "</p></div></body></html>";
    error_log("Fallo crítico: No se pudo conectar a la base de datos en admin_dashboard.php: " . $e->getMessage());
    exit();
}

require_once __DIR__ . "/process/auth_check_admin.php";

$displayAdminName = htmlspecialchars($currentUser['nombre'] ?? 'Administrador');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Biblioteca UPTA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/normalize.css">
    <link rel="stylesheet" href="styles/admin_dashboard.css">
</head>
<body class="app-body-admin">
    <div class="admin-dashboard-container">
        <!-- Sidebar del Administrador -->
        <aside class="admin-sidebar">
            <div class="admin-sidebar-header">
                <a href="#" class="admin-sidebar-logo">
                    <i class="fas fa-user-shield"></i> Admin Biblioteca
                </a>
            </div>
            <nav class="admin-sidebar-nav">
                <ul>
                    <li><a href="./"><i class="fas fa-home"></i> Inicio</a></li>
                    <li><a href="#" data-section="overview" class="active"><i class="fas fa-th-large"></i> Dashboard</a></li>
                    <li><a href="#" data-section="users"><i class="fas fa-users"></i> Gestión Usuarios</a></li>
                    <li><a href="#" data-section="books"><i class="fas fa-book"></i> Gestión Recursos</a></li>
                    <li><a href="#" data-section="add-resource"><i class="fas fa-plus-circle"></i> Añadir Recurso</a></li>
                    <li><a href="#" data-section="categories"><i class="fas fa-tags"></i> Categorías</a></li>
                    <li><a href="#" data-section="requests"><i class="fas fa-paper-plane"></i> Solicitudes</a></li>
                    <li><a href="#" data-section="reports"><i class="fas fa-chart-line"></i> Reportes</a></li>
                    <li class="admin-separator"></li>
                    <li><a href="#" data-section="settings"><i class="fas fa-cogs"></i> Configuración</a></li>
                    <li><a href="process/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Contenido Principal del Administrador -->
        <main class="admin-main-content">
            <!-- Barra Superior del Administrador -->
            <header class="admin-navbar">
                <div class="admin-navbar-left">
                    <h1 class="admin-navbar-section-title">Dashboard</h1>
                </div>
                <div class="admin-navbar-right">
                    <div class="admin-navbar-icon-group">
                        <i class="fas fa-bell admin-icon"></i>
                        <i class="fas fa-cog admin-icon"></i>
                        <i class="fas fa-question-circle admin-icon"></i>
                    </div>
                    <div class="admin-user-profile">
                        <img src="https://placehold.co/40x40/3B82F6/FFFFFF?text=A" alt="Admin Avatar" class="admin-profile-avatar">
                        <span class="admin-profile-name"><?= $displayAdminName ?></span>
                    </div>
                </div>
            </header>

            <!-- Área de Contenido Dinámico -->
            <div class="admin-content-area">
                <section id="overview" class="admin-content-section active">
                    <h2>Resumen General</h2>
                    <div class="admin-card-grid">
                        <!-- Tarjetas de Resumen -->
                        <div class="admin-card admin-summary-card">
                            <i class="fas fa-users admin-summary-icon"></i>
                            <p class="admin-summary-title">Total Usuarios</p>
                            <p class="admin-summary-value">212</p>
                            <span class="admin-summary-trend increase"><i class="fas fa-arrow-up"></i> 9.5%</span>
                        </div>
                        <div class="admin-card admin-summary-card">
                            <i class="fas fa-book admin-summary-icon"></i>
                            <p class="admin-summary-title">Total Recursos</p>
                            <p class="admin-summary-value">82</p>
                            <span class="admin-summary-trend decrease"><i class="fas fa-arrow-down"></i> 5.5%</span>
                        </div>
                        <div class="admin-card admin-summary-card">
                            <i class="fas fa-download admin-summary-icon"></i>
                            <p class="admin-summary-title">Total Descargas</p>
                            <p class="admin-summary-value">132</p>
                            <span class="admin-summary-trend increase"><i class="fas fa-arrow-up"></i> 8.5%</span>
                        </div>
                        <div class="admin-card admin-summary-card">
                            <i class="fas fa-handshake admin-summary-icon"></i>
                            <p class="admin-summary-title">Solicitudes Pendientes</p>
                            <p class="admin-summary-value">164</p>
                            <span class="admin-summary-trend decrease"><i class="fas fa-arrow-down"></i> 5.5%</span>
                        </div>
                        <div class="admin-card admin-chart-card" style="grid-column: span 1;">
                            <h3>Actividad de Usuarios</h3>
                            <div class="chart-placeholder">Gráfico de barras de actividad</div>
                        </div>
                        <div class="admin-card admin-chart-card" style="grid-column: span 1;">
                            <h3>Recursos Más Populares</h3>
                            <div class="chart-placeholder">Gráfico de recursos más descargados</div>
                        </div>
                        <div class="admin-card admin-large-card" style="grid-column: span 1;">
                            <h3>Estado General del Sistema</h3>
                            <div class="chart-placeholder">Gráfico de donut o pastel</div>
                        </div>
                        <div class="admin-card admin-large-card users-container" style="grid-column: span 2; width: 95%;">
                            <h3>Registros Recientes de Usuarios</h3>
                            <table class="admin-data-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th class="truncate-text">Nombre</th>
                                        <th class="truncate-text">Email</th>
                                        <th>Rol</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td class="truncate-text">Juan Pérez</td>
                                        <td class="truncate-text">juan@example.com</td>
                                        <td>Usuario</td>
                                        <td class="status active">Activo</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td class="truncate-text">María García</td>
                                        <td class="truncate-text">maria@example.com</td>
                                        <td>Usuario</td>
                                        <td class="status active">Activo</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td class="truncate-text">Carlos Ruiz</td>
                                        <td class="truncate-text">carlos@example.com</td>
                                        <td>Admin</td>
                                        <td class="status active">Activo</td>
                                    </tr>
                                </tbody>
                            </table>
                            <a href="#" class="button button-secondary" style="margin-top: 20px;">Ver todos los usuarios</a>
                        </div>
                    </div>
                </section>

                <section id="users" class="admin-content-section">
                    <h2>Gestión de Usuarios</h2>
                    <div class="admin-card" style="margin-top: 20px;">
                        <h3>Listado de Usuarios</h3>
                        <div class="table-responsive">
                            <table class="admin-data-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Rol</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>101</td>
                                        <td class="truncate-text">Ana López</td>
                                        <td class="truncate-text">ana.lopez@example.com</td>
                                        <td>Usuario</td>
                                        <td class="status active">Activo</td>
                                        <td>
                                            <button class="action-btn"><i class="fas fa-edit"></i></button>
                                            <button class="action-btn"><i class="fas fa-trash-alt"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>102</td>
                                        <td class="truncate-text">Pedro Martínez</td>
                                        <td class="truncate-text">pedro.m@example.com</td>
                                        <td>Usuario</td>
                                        <td class="status paused">Pausado</td>
                                        <td>
                                            <button class="action-btn"><i class="fas fa-edit"></i></button>
                                            <button class="action-btn"><i class="fas fa-trash-alt"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- SECCIÓN: Gestión de Recursos/Recursos -->
                <section id="books" class="admin-content-section">
                    <h2>Gestión de Recursos de la Biblioteca</h2>
                    <div class="admin-section-actions">
                        <a href="#" data-section="add-resource" class="button"><i class="fas fa-plus"></i> Añadir Nuevo Recurso</a>
                    </div>
                    <div class="admin-card" style="margin-top: 20px;">
                        <h3>Listado de Recursos</h3>
                        <div class="table-responsive">
                            <table class="admin-data-table" id="resources-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Título</th>
                                        <th>Autor(es)</th>
                                        <th>Tipo</th>
                                        <th>Categoría</th>
                                        <th>Año</th>
                                        <th>PDF</th>
                                        <th>Video</th>
                                        <th>Portada</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- SECCIÓN: Añadir Nuevo Recurso (Formulario) -->
                <section id="add-resource" class="admin-content-section">
                    <h2>Añadir Nuevo Recurso</h2>
                    <div class="admin-card">
                        <h3>Detalles del Recurso</h3>
                        <p>Completa los campos para añadir un nuevo recurso a la biblioteca.</p>
                        <form id="form-add-resource" style="margin-top: 20px;" enctype="multipart/form-data">
                            <div style="margin-bottom: 15px;">
                                <label for="add-resource-type" class="form-label">Tipo de Recurso</label>
                                <select id="add-resource-type" name="resource_type" class="form-input">
                                    <option value="">Seleccione un tipo...</option>
                                    <option value="Libro">Libro</option>
                                    <option value="Proyecto">Proyecto</option>
                                    <option value="Articulo">Artículo</option>
                                    <option value="Otro">Otro</option>
                                </select>
                                <span class="error-message" id="errAddResourceType"></span>
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label for="add-book-title" class="form-label">Título del Recurso</label>
                                <input type="text" id="add-book-title" name="title" class="form-input">
                                <span class="error-message" id="errAddTitle"></span>
                            </div>

                            <!-- Sección para Múltiples Autores -->
                            <div  style="margin-bottom: 15px;"class="form-group">
                                <label class="form-label">Autores del Recurso</label>
                                <div id="author-fields-container">
                                    <!-- Grupo inicial de campos de autor -->
                                    <div class="author-input-group mb-10">
                                        <input type="text" name="authors[0][name]" class="form-input author-name-input" placeholder="Nombre completo del autor" required>
                                        <input type="hidden" name="authors[0][id_autor]" class="author-id-input">
                                        <input type="email" name="authors[0][email_contacto_autor]" class="form-input mt-5" placeholder="Email de contacto (opcional)">
                                        <input type="text" name="authors[0][telefono_contacto_autor]" class="form-input mt-5" placeholder="Teléfono de contacto (opcional)">
                                        <div class="social-media-inputs mt-5">
                                            <div class="social-input-group">
                                                <i class="fab fa-linkedin social-icon"></i>
                                                <input type="url" name="authors[0][social_linkedin]" class="form-input" placeholder="https://www.linkedin.com/in/usuario">
                                            </div>
                                            <div class="social-input-group mt-5">
                                                <i class="fab fa-twitter social-icon"></i>
                                                <input type="url" name="authors[0][social_twitter]" class="form-input" placeholder="https://twitter.com/usuario">
                                            </div>
                                            <div class="social-input-group mt-5">
                                                <i class="fab fa-github social-icon"></i>
                                                <input type="url" name="authors[0][social_github]" class="form-input" placeholder="https://github.com/usuario">
                                            </div>
                                            <div class="social-input-group mt-5">
                                                <i class="fab fa-facebook social-icon"></i>
                                                <input type="url" name="authors[0][social_facebook]" class="form-input" placeholder="https://www.facebook.com/usuario">
                                            </div>
                                        </div>
                                        <span class="error-message" id="errAddAuthor0"></span>
                                        <button type="button" class="button button-secondary button-remove-author mt-10" style="display: none;"><i class="fas fa-minus"></i> Eliminar Autor</button>
                                    </div>
                                </div>
                                <button type="button" id="add-author-btn" class="button button-secondary mt-15">
                                    <i class="fas fa-plus"></i> Añadir Otro Autor
                                </button>
                                <span class="error-message" id="errAddAuthors"></span>
                            </div>

                            <div style="margin-bottom: 15px;">
                                <label for="add-book-category" class="form-label">Categoría</label>
                                <select id="add-book-category" name="category" class="form-input">
                                    <option value="">Seleccione una categoría...</option>
                                    <option value="Informatica">Informática</option>
                                    <option value="Electronica">Electrónica</option>
                                    <option value="Mecanica">Mecánica</option>
                                    <option value="Administracion">Administración</option>
                                    <option value="Contaduria">Contaduría</option>
                                    <option value="Ciencias">Ciencias</option>
                                    <option value="Historia">Historia</option>
                                    <option value="Literatura">Literatura</option>
                                </select>
                                <span class="error-message" id="errAddCategory"></span>
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label for="add-book-year" class="form-label">Año de Publicación</label>
                                <input type="number" id="add-book-year" name="publication_year" class="form-input" min="1900" max="<?= date('Y') ?>">
                                <span class="error-message" id="errAddPublicationYear"></span>
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label for="add-book-description" class="form-label">Descripción</label>
                                <textarea id="add-book-description" name="description" class="form-input" rows="5"></textarea>
                                <span class="error-message" id="errAddDescription"></span>
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label for="add-book-pdf" class="form-label">Archivo PDF del Recurso</label>
                                <input type="file" id="add-book-pdf" name="book_pdf" class="form-input-file" accept=".pdf">
                                <span class="error-message" id="errAddBookPdf"></span>
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label for="add-resource-video" class="form-label">Video Demo del Recurso (Opcional)</label>
                                <input type="file" id="add-resource-video" name="resource_video" class="form-input-file" accept="video/*">
                                <span class="error-message" id="errAddResourceVideo"></span>
                            </div>
                            <div style="margin-bottom: 20px;">
                                <label for="add-book-cover" class="form-label">Portada del Recurso (Opcional)</label>
                                <input type="file" id="add-book-cover" name="book_cover" class="form-input-file" accept="image/*">
                                <span class="error-message" id="errAddBookCover"></span>
                            </div>
                            <button type="submit" class="button">Añadir Recurso</button>
                        </form>
                    </div>
                </section>

                <section id="categories" class="admin-content-section">
                    <h2>Gestión de Categorías</h2>
                    <div class="admin-card" style="margin-top: 20px;">
                        <h3>Listado de Categorías</h3>
                        <div class="table-responsive">
                            <table class="admin-data-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre de Categoría</th>
                                        <th>Recursos Asociados</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Informática</td>
                                        <td>50</td>
                                        <td>
                                            <button class="action-btn"><i class="fas fa-edit"></i></button>
                                            <button class="action-btn"><i class="fas fa-trash-alt"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Electrónica</td>
                                        <td>30</td>
                                        <td>
                                            <button class="action-btn"><i class="fas fa-edit"></i></button>
                                            <button class="action-btn"><i class="fas fa-trash-alt"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <button class="button" style="margin-top: 20px;"><i class="fas fa-plus"></i> Añadir Nueva Categoría</button>
                    </div>
                </section>
                <section id="requests" class="admin-content-section">
                    <h2>Gestión de Solicitudes</h2>
                    <div class="admin-card" style="margin-top: 20px;">
                        <h3>Solicitudes Pendientes</h3>
                        <div class="table-responsive">
                            <table class="admin-data-table">
                                <thead>
                                    <tr>
                                        <th>ID Solicitud</th>
                                        <th>Usuario</th>
                                        <th>Recurso Solicitado</th>
                                        <th>Fecha</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>S001</td>
                                        <td>usuario1@example.com</td>
                                        <td class="truncate-text">Libro: Redes de Computadoras</td>
                                        <td>2024-07-01</td>
                                        <td class="status paused">Pendiente</td>
                                        <td>
                                            <button class="action-btn"><i class="fas fa-check-circle"></i></button>
                                            <button class="action-btn"><i class="fas fa-times-circle"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
                <section id="reports" class="admin-content-section">
                    <h2>Reportes y Estadísticas</h2>
                    <div class="admin-card-grid">
                        <div class="admin-card admin-chart-card">
                            <h3>Descargas por Categoría</h3>
                            <div class="chart-placeholder">Gráfico de pastel/barras</div>
                        </div>
                        <div class="admin-card admin-chart-card">
                            <h3>Actividad Mensual</h3>
                            <div class="chart-placeholder">Gráfico de líneas</div>
                        </div>
                    </div>
                </section>
                <section id="settings" class="admin-content-section">
                    <h2>Configuración del Sistema</h2>
                    <div class="admin-card">
                        <h3>Ajustes Generales</h3>
                        <p>Aquí puedes configurar opciones generales del sistema.</p>
                        <form style="margin-top: 20px;">
                            <div style="margin-bottom: 15px;">
                                <label for="site-name" class="form-label">Nombre del Sitio</label>
                                <input type="text" id="site-name" name="site_name" class="form-input" value="Biblioteca UPTA">
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label for="admin-email" class="form-label">Email de Contacto Admin</label>
                                <input type="email" id="admin-email" name="admin_email" class="form-input" value="admin@upta.edu.ve">
                            </div>
                            <button type="submit" class="button">Guardar Configuración</button>
                        </form>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Modal para Editar Recurso -->
    <div id="edit-resource-modal" class="message-modal">
        <div class="message-content admin-modal-content">
            <span id="close-edit-modal-btn" class="close-btn">&times;</span>
            <h3>Editar Recurso</h3>
            <form id="form-edit-resource" style="margin-top: 20px;" enctype="multipart/form-data">
                <input type="hidden" id="edit-resource-id" name="resource_id">
                <input type="hidden" id="edit-current-pdf" name="current_pdf_path">
                <input type="hidden" id="edit-current-cover" name="current_cover_path">
                <input type="hidden" id="edit-current-video" name="current_video_path">

                <div style="margin-bottom: 15px;">
                    <label for="edit-resource-type" class="form-label">Tipo de Recurso</label>
                    <select id="edit-resource-type" name="resource_type" class="form-input">
                        <option value="Libro">Libro</option>
                        <option value="Proyecto">Proyecto</option>
                        <option value="Articulo">Artículo</option>
                        <option value="Otro">Otro</option>
                    </select>
                    <span class="error-message" id="errEditResourceType"></span>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="edit-book-title" class="form-label">Título del Recurso</label>
                    <input type="text" id="edit-book-title" name="title" class="form-input">
                    <span class="error-message" id="errEditTitle"></span>
                </div>

                <!-- Sección para Múltiples Autores en Edición -->
                <div class="form-group">
                    <label class="form-label">Autores del Recurso</label>
                    <div id="edit-author-fields-container">
                        <!-- Los campos de autor se cargarán y añadirán aquí dinámicamente por JS -->
                        <div class="author-input-group mb-10">
                            <input type="text" name="authors[0][name]" class="form-input author-name-input" placeholder="Nombre completo del autor" required>
                            <input type="hidden" name="authors[0][id_autor]" class="author-id-input">
                            <input type="email" name="authors[0][email_contacto_autor]" class="form-input mt-5" placeholder="Email de contacto (opcional)">
                            <input type="text" name="authors[0][telefono_contacto_autor]" class="form-input mt-5" placeholder="Teléfono de contacto (opcional)">
                            <div class="social-media-inputs mt-5">
                                <div class="social-input-group">
                                    <i class="fab fa-linkedin social-icon"></i>
                                    <input type="url" name="authors[0][social_linkedin]" class="form-input" placeholder="URL de LinkedIn">
                                </div>
                                <div class="social-input-group mt-5">
                                    <i class="fab fa-twitter social-icon"></i>
                                    <input type="url" name="authors[0][social_twitter]" class="form-input" placeholder="URL de Twitter/X">
                                </div>
                                <div class="social-input-group mt-5">
                                    <i class="fab fa-github social-icon"></i>
                                    <input type="url" name="authors[0][social_github]" class="form-input" placeholder="URL de GitHub">
                                </div>
                                <div class="social-input-group mt-5">
                                    <i class="fab fa-facebook social-icon"></i>
                                    <input type="url" name="authors[0][social_facebook]" class="form-input" placeholder="URL de Facebook">
                                </div>
                            </div>
                            <span class="error-message" id="errEditAuthor0"></span>
                            <button type="button" class="button button-secondary button-remove-author mt-10" style="display: none;"><i class="fas fa-minus"></i> Eliminar Autor</button>
                        </div>
                    </div>
                    <button type="button" id="edit-add-author-btn" class="button button-secondary mt-15">
                        <i class="fas fa-plus"></i> Añadir Otro Autor
                    </button>
                    <span class="error-message" id="errEditAuthors"></span>
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="edit-book-category" class="form-label">Categoría</label>
                    <select id="edit-book-category" name="category" class="form-input">
                        <option value="Informatica">Informática</option>
                        <option value="Electronica">Electrónica</option>
                        <option value="Mecanica">Mecánica</option>
                        <option value="Administracion">Administración</option>
                        <option value="Contaduria">Contaduría</option>
                        <option value="Ciencias">Ciencias</option>
                        <option value="Historia">Historia</option>
                        <option value="Literatura">Literatura</option>
                    </select>
                    <span class="error-message" id="errEditCategory"></span>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="edit-book-year" class="form-label">Año de Publicación</label>
                    <input type="number" id="edit-book-year" name="publication_year" class="form-input" min="1900" max="<?= date('Y') ?>">
                    <span class="error-message" id="errEditPublicationYear"></span>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="edit-book-description" class="form-label">Descripción</label>
                    <textarea id="edit-book-description" name="description" class="form-input" rows="5"></textarea>
                    <span class="error-message" id="errEditDescription"></span>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="edit-book-pdf" class="form-label">Archivo PDF del Recurso (Dejar vacío para mantener el actual)</label>
                    <input type="file" id="edit-book-pdf" name="book_pdf" class="form-input-file" accept=".pdf">
                    <span class="error-message" id="errEditBookPdf"></span>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="edit-resource-video" class="form-label">Video Demo del Recurso (Dejar vacío para mantener el actual)</label>
                    <input type="file" id="edit-resource-video" name="resource_video" class="form-input-file" accept="video/*">
                    <span class="error-message" id="errEditResourceVideo"></span>
                    <div id="current-video-preview" style="margin-top: 10px; text-align: center;">
                        <p style="font-size: 13px; color: var(--color-light-text);">No hay video actual.</p>
                    </div>
                </div>
                <div style="margin-bottom: 20px;">
                    <label for="edit-book-cover" class="form-label">Portada del Recurso (Dejar vacío para mantener la actual)</label>
                    <input type="file" id="edit-book-cover" name="book_cover" class="form-input-file" accept="image/*">
                    <span class="error-message" id="errEditBookCover"></span>
                    <div id="current-cover-preview" style="margin-top: 10px; text-align: center;">
                        <p style="font-size: 13px; color: var(--color-light-text);">No hay portada actual.</p>
                    </div>
                </div>
                <button type="submit" class="button">Guardar Cambios</button>
            </form>
        </div>
    </div>


    <!-- Modal para mensajes de éxito/error -->
    <div id="message-modal" class="message-modal">
        <div class="message-content">
            <span id="close-modal-btn" class="close-btn">&times;</span>
            <p id="message-text"></p>
        </div>
    </div>

    <!-- Loader Overlay -->
    <div id="loader-overlay" class="loader-overlay">
        <div class="loader-spinner"></div>
    </div>

    <script src="js/admin_dashboard.js" defer></script>
</body>
</html>
