<?php
// Requiere el archivo base de verificación de autenticación de usuario
require_once __DIR__ . "/auth_check.php"; // Esto validará la sesión y cargará $currentUser

// Si el script de auth_check.php ya redirigió o salió, este código no se ejecutará.
// Si llega aquí, significa que hay un usuario logueado y $currentUser está disponible.

// Ahora, verificamos si el usuario tiene el rol de administrador.
if (!isset($currentUser) || !is_array($currentUser) || ($currentUser['rol'] ?? 'usuario') !== 'admin') {
    // Si el usuario no es administrador, redirigirlo o devolver un JSON de error si es AJAX.
    $errorMessage = 'Acceso denegado. Solo los administradores pueden acceder a esta sección.';
    $redirectUrl = (defined('BASE_URL') ? BASE_URL : '/') . 'login.php';

    // Detección de solicitud AJAX (copiado de auth_check.php)
    $isAjaxRequest = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

    if ($isAjaxRequest) {
        http_response_code(403); // 403 Forbidden
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'Forbidden Access',
            'message' => $errorMessage,
            'redirect' => $redirectUrl
        ]);
        exit();
    } else {
        $_SESSION['flash_message'] = ['type' => 'error', 'message' => $errorMessage];
        header('Location: ' . $redirectUrl);
        exit();
    }
}

// Si el script llega hasta aquí, significa que el usuario está logueado Y tiene el rol de administrador.
// $currentUser contiene todos los datos del administrador.
?>
