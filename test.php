<?php
// test_dotenv.php (Este archivo debe estar en la raíz de tu proyecto, C:\xampp\htdocs\biblioteca\)

// Carga el autoloader de Composer.
// La ruta es __DIR__ . '/vendor/autoload.php' porque este script está en la raíz.
require '/vendor/autoload.php';

// Carga las variables de entorno desde el archivo .env.
// El directorio para .env es __DIR__ porque este script está en la raíz.
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    // Intenta acceder a una variable del .env
    $db_username = getenv('DB_USERNAME');

    if ($db_username !== false && $db_username !== null) {
        echo "<h1>Éxito: Las variables de entorno se cargaron correctamente.</h1>";
        echo "<p>DB_USERNAME: " . htmlspecialchars($db_username) . "</p>";
        echo "<p>BASE_URL: " . htmlspecialchars(getenv('BASE_URL')) . "</p>";
        // Si no se ve, puede que la variable exista pero esté vacía
        if (empty($db_username)) {
            echo "<p style='color:orange;'>Advertencia: DB_USERNAME está vacío en tu .env</p>";
        }
    } else {
        echo "<h1>Error: No se pudo cargar la variable DB_USERNAME del .env.</h1>";
        echo "<p>Verifica que DB_USERNAME esté definido en tu archivo .env y que el archivo .env esté en la raíz del proyecto.</p>";
        echo "<p>Ruta que se intentó cargar: " . __DIR__ . "</p>";
    }

} catch (Dotenv\Exception\InvalidPathException $e) {
    echo "<h1>Error Fatal: El archivo .env no fue encontrado.</h1>";
    echo "<p>Detalles: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Asegúrate de que '.env' está en la raíz del proyecto: " . __DIR__ . "</p>";
} catch (Exception $e) {
    echo "<h1>Error inesperado al cargar Dotenv:</h1>";
    echo "<p>Detalles: " . htmlspecialchars($e->getMessage()) . "</p>";
}

?>
