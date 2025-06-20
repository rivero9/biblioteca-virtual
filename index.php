<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca Virtual UPT Aragua</title>
    <!-- Incluye Font Awesome para los íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" xintegrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0V4LLanw2qksYuRlEzr+zGxFNsFTQx84uF/2aUtsTfnB2/g2Pz/5v5R6E7g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="styles/normalize.css">
    <link rel="stylesheet" href="styles/main.css">
    <script src="js/main.js" defer></script>
</head>
<body class="app-body">
    <!-- header inlcuido -->
    <?php include __DIR__ . "/includes/header.php" ?>
    <!-- Contenido Principal -->
    <main class="main-content">
        <!-- Sección de Búsqueda Principal (Hero) -->
        <section class="section-main">
            <div class="search-hero-section">
                <h1 class="search-hero-title">Biblioteca virtual UPT Aragua</h1>
                <!-- <h2 class="search-hero-title">Encuentra tu próximo libro</h2> -->
                <p class="search-hero-description">Explora nuestra colección de recursos académicos, investigaciones y literatura.</p>
                <div class="search-input-group">
                    <input
                        type="text"
                        placeholder="Buscar por título, autor o palabra clave..."
                        class="search-input"
                    >
                    <button class="btn-search">
                        <i class="fas fa-search search-icon"></i>
                    </button>
                </div>
            </div>
        </section>

        <!-- Sección de Categorías -->
        <section class="categories-section">
            <h3 class="section-title">Explorar por Categorías</h3>
            <div class="categories-grid">
                <!-- Tarjeta de Categoría -->
                <div class="category-card">
                    <i class="fas fa-flask category-icon"></i>
                    <span class="category-name">Ciencias</span>
                </div>
                <div class="category-card">
                    <i class="fas fa-laptop-code category-icon"></i>
                    <span class="category-name">Tecnología</span>
                </div>
                <div class="category-card">
                    <i class="fas fa-landmark category-icon"></i>
                    <span class="category-name">Historia</span>
                </div>
                <div class="category-card">
                    <i class="fas fa-chart-line category-icon"></i>
                    <span class="category-name">Economía</span>
                </div>
                <div class="category-card">
                    <i class="fas fa-hard-hat category-icon"></i>
                    <span class="category-name">Ingeniería</span>
                </div>
            </div>
        </section>

        <!-- Sección de Libros Destacados -->
        <section class="featured-books-section">
            <h3 class="section-title">Novedades y Destacados</h3>
            <div class="books-grid">
                <!-- Tarjeta de Libro 1 -->
                <div class="book-card">
                    <img src="https://placehold.co/400x600/E0E0E0/333333?text=Portada+Libro+1" alt="Portada de Libro 1" class="book-cover">
                    <div class="book-info">
                        <h4 class="book-title-card">Principios de la Computación</h4>
                        <p class="book-author">Autor: John Doe</p>
                        <p id="desc1" class="book-description-short">Una introducción completa a los fundamentos de la informática y la programación moderna, abarcando desde algoritmos básicos hasta estructuras de datos complejas. Este libro es ideal para estudiantes y profesionales que buscan una base sólida en el campo de la computación.</p>
                    </div>
                    <div class="book-actions">
                        <button onclick="openBookModal('Principios de la Computación', 'John Doe', document.getElementById('desc1').innerText)" class="btn-book-details">
                            Ver Detalles
                        </button>
                    </div>
                </div>

                <!-- Tarjeta de Libro 2 -->
                <div class="book-card">
                    <img src="https://placehold.co/400x600/E0E0E0/333333?text=Portada+Libro+2" alt="Portada de Libro 2" class="book-cover">
                    <div class="book-info">
                        <h4 class="book-title-card">Historia de Venezuela Siglo XX</h4>
                        <p class="book-author">Autor: María Rodríguez</p>
                        <p id="desc2" class="book-description-short">Un análisis profundo de los eventos clave y las figuras influyentes en la Venezuela del siglo pasado, desde el inicio del siglo XX hasta la llegada del nuevo milenio. Cubre aspectos políticos, sociales y económicos que moldearon la nación.</p>
                    </div>
                    <div class="book-actions">
                        <button onclick="openBookModal('Historia de Venezuela Siglo XX', 'María Rodríguez', document.getElementById('desc2').innerText)" class="btn-book-details">
                            Ver Detalles
                        </button>
                    </div>
                </div>

                <!-- Tarjeta de Libro 3 -->
                <div class="book-card">
                    <img src="https://placehold.co/400x600/E0E0E0/333333?text=Portada+Libro+3" alt="Portada de Libro 3" class="book-cover">
                    <div class="book-info">
                        <h4 class="book-title-card">Matemáticas Aplicadas</h4>
                        <p class="book-author">Autor: Carlos Gómez</p>
                        <p id="desc3" class="book-description-short">Ejercicios y teorías para la aplicación de conceptos matemáticos en la vida real, con un enfoque en la resolución de problemas en ingeniería, economía y ciencias naturales. Incluye ejemplos prácticos y desafíos para el aprendizaje.</p>
                    </div>
                    <div class="book-actions">
                        <button onclick="openBookModal('Matemáticas Aplicadas', 'Carlos Gómez', document.getElementById('desc3').innerText)" class="btn-book-details">
                            Ver Detalles
                        </button>
                    </div>
                </div>

                <!-- Tarjeta de Libro 4 -->
                <div class="book-card">
                    <img src="https://placehold.co/400x600/E0E0E0/333333?text=Portada+Libro+4" alt="Portada de Libro 4" class="book-cover">
                    <div class="book-info">
                        <h4 class="book-title-card">Diseño Gráfico Moderno</h4>
                        <p class="book-author">Autor: Ana Pérez</p>
                        <p id="desc4" class="book-description-short">Guía completa para diseñadores gráficos, con las últimas tendencias y herramientas del sector. Desde los principios básicos del color y la composición hasta el uso de software avanzado y la creación de portfolios impactantes.</p>
                    </div>
                    <div class="book-actions">
                        <button onclick="openBookModal('Diseño Gráfico Moderno', 'Ana Pérez', document.getElementById('desc4').innerText)" class="btn-book-details">
                            Ver Detalles
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sección de Recursos Adicionales / Noticias -->
        <section class="resources-news-section">
            <h3 class="section-title">Recursos y Noticias</h3>
            <div class="news-grid">
                <!-- Tarjeta de Noticia/Recurso -->
                <div class="news-card">
                    <h4 class="news-title">Nuevo Horario de Atención en Biblioteca Física</h4>
                    <p class="news-description">A partir del lunes 17 de junio, la biblioteca física extenderá sus horarios de atención para el período de exámenes finales...</p>
                    <a href="#" class="news-link">Leer más &rarr;</a>
                </div>
                <div class="news-card">
                    <h4 class="news-title">Taller Gratuito: Investigación Académica Efectiva</h4>
                    <p class="news-description">Únete a nuestro taller el 25 de junio para aprender técnicas avanzadas de investigación y uso de bases de datos...</p>
                    <a href="#" class="news-link">Registrarse &rarr;</a>
                </div>
            </div>
        </section>
    </main>

    <!-- footer inlcuido -->
    <?php include __DIR__ . "/includes/footer.php" ?>

    <!-- Modal de Detalles del Libro -->
    <div id="bookModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeBookModal()">&times;</span>
            <h3 id="modalBookTitle" class="modal-book-title"></h3>
            <p id="modalBookAuthor" class="modal-book-author"></p>
            <h4 class="modal-section-title">Descripción:</h4>
            <p id="modalBookDescription" class="modal-book-description"></p>
        </div>
    </div>
</body>
</html>