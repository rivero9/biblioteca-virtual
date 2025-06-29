<?php
// process/auth_check.php

// Este archivo asume que 'init.php' (para session_start()) y
// que la variable $con (conexión PDO a la DB) ya ha sido definida
// en el ámbito local del archivo que incluye auth_check.php (ej. update_user_data.php).

// Comprobar si la sesión ya ha sido iniciada por init.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? null;

$redirectUrl = getenv('BASE_URL') . '/login.php';
$errorMessage = ''; // Variable para almacenar mensajes de error

// Bandera para detectar si es una solicitud AJAX/Fetch
// Esta es la parte CRUCIAL que faltaba para diferenciar peticiones.
$isAjaxRequest = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

try {
    // --- Verificar la disponibilidad de la conexión a la base de datos ---
    // Si $con no está definida o no es un objeto PDO válido, lanzamos una excepción.
    // Esto es vital, ya que ahora confiamos en que el archivo que nos incluye define $con.
    if (!isset($con) || !($con instanceof PDO)) {
        throw new Exception("Error interno: La conexión a la base de datos no está disponible en este contexto.");
    }

    // --- Validación de Sesión ---
    if (empty($user_id)) {
        $errorMessage = "Debes iniciar sesión para acceder a esta página.";
    } else {
        // --- 1. Verificar la existencia del usuario en la base de datos de forma eficiente ---
        $sql = $con->prepare("SELECT COUNT(*) FROM usuarios WHERE id = :user_id LIMIT 1");
        $sql->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $sql->execute();
        $userExists = $sql->fetchColumn();

        if ($userExists == 0) {
            $errorMessage = "Tu sesión no es válida. El usuario asociado no fue encontrado en la base de datos.";
        } else {
            // --- 2. Prevención de Secuestro de Sesión (User Agent Check) ---
            $sessionUserAgent = $_SESSION['user_agent'] ?? '';

            if ($sessionUserAgent !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
                $errorMessage = "Tu sesión ha sido invalidada por razones de seguridad (posible cambio de navegador).";
                session_unset();
                session_destroy();
                session_start();
                session_regenerate_id(true);
            } else {
                // --- 3. Cargar los datos completos del usuario desde la DB ---
                // Los datos del usuario actual se hacen disponibles en el ámbito del script que incluye auth_check.php
                $sqlUser = $con->prepare("SELECT id, nombre, telefono, correo, cedula, pnf, trayecto FROM usuarios WHERE id = :user_id LIMIT 1");
                $sqlUser->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $sqlUser->execute();
                $currentUser = $sqlUser->fetch(PDO::FETCH_ASSOC);

                if (!$currentUser) {
                    $errorMessage = "Error al cargar los datos de tu perfil. Por favor, reinicia sesión.";
                }
            }
        }
    }

} catch (PDOException $e) {
    error_log("Error de BD en validación de sesión (process/auth_check.php): " . $e->getMessage());
    $errorMessage = "Error interno de la base de datos. Por favor, inténtalo más tarde.";
} catch (Exception $e) {
    error_log("Error general en validación de sesión (process/auth_check.php): " . $e->getMessage());
    $errorMessage = "Ocurrió un problema inesperado en la validación. Por favor, reinicia sesión.";
}

// --- Lógica de Redirección o Respuesta JSON si la Validación Falla ---
if (!empty($errorMessage)) {
    if ($isAjaxRequest) {
        // Si es una solicitud AJAX, devuelve JSON con un código de estado de error HTTP 401
        http_response_code(401); // 401 Unauthorized
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'Authentication Failed',
            'message' => $errorMessage,
            'redirect' => $redirectUrl // Envía la URL de redirección en el JSON para que JS la use
        ]);
        exit(); // Crucial: Termina la ejecución para evitar que se envíe más contenido
    } else {
        // Si es una solicitud HTTP normal (navegación directa), redirige
        $_SESSION['flash_message'] = ['type' => 'error', 'message' => $errorMessage];
        // print_r($_SESSION['flash_message']); // <-- ¡Eliminar esta línea! Rompe la redirección
        header('Location: ' . $redirectUrl);
        exit(); // Crucial: Termina la ejecución del script aquí
    }
}
// Si el script llega hasta este punto, la sesión es válida y $currentUser está disponible.
?>
