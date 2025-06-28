<?php
// includes/init.php

// Iniciar la sesión si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Cargar las variables de entorno desde el archivo .env ---
// Desde 'includes/', necesitamos subir un nivel (../) para llegar a la raíz del proyecto.

$path = "./vendor/autoload.php";
$extraPath = "./";

while (!file_exists($path)) {
  $extraPath .= "../";
  $path = "../".$path;
}

require $path;
$dotenv = Dotenv\Dotenv::createUnsafeMutable($extraPath);
$dotenv->load();

// Ahora las variables de entorno están disponibles a través de getenv() o $_ENV

// Definir BASE_URL (que ya está en .env)
define('BASE_URL', getenv('BASE_URL'));

// Opcional: configurar reporte de errores en desarrollo
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

?>
