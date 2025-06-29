<?php

$user_id = $_SESSION['user_id'] ?? null;

$redirectUrl =  getenv('BASE_URL') . '/login.php';
$errorMessage = ''; // Variable para almacenar mensajes de error

try {
    // --- Verificar la disponibilidad de la conexión a la base de datos ---
    if (!isset($con) || !($con instanceof PDO)) {
        throw new Exception("Error: Conexión a la base de datos no disponible para la validación de sesión.");
    }

    // --- Validación de Sesión ---
    if (empty($user_id)) {
        $errorMessage = "Debes iniciar sesión para acceder a esta página.";
    } else {
        // --- 1. Verificar la existencia del usuario en la base de datos de forma eficiente ---
        $sql = $con->prepare("SELECT COUNT(*) FROM usuarios WHERE id = :user_id LIMIT 1");
        $sql->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $sql->execute();
        $userExists = $sql->fetchColumn(); // Obtiene el número de filas (0 o 1)

        if ($userExists == 0) {
            $errorMessage = "Tu sesión no es válida. El usuario asociado no fue encontrado en la base de datos.";
        } else {
            // --- 2. Prevención de Secuestro de Sesión (Session Hijacking) ---
            $sessionUserAgent = $_SESSION['user_agent'] ?? '';

            if ($sessionUserAgent !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
                $errorMessage = "Tu sesión ha sido invalidada por razones de seguridad (posible cambio de dispositivo).";
                // Destruir la sesión actual si se detecta una discrepancia
                session_unset();    // Elimina todas las variables de sesión
                session_destroy();  // Destruye la sesión
                session_start();    // Iniciar una nueva sesión limpia para evitar advertencias
                session_regenerate_id(true); // Generar un nuevo ID de sesión para la nueva sesión
            } else {
                // --- 3. Opcional: Cargar los datos completos del usuario desde la DB ---
                $sqlUser = $con->prepare("SELECT id, nombre, correo, cedula, pnf, trayecto FROM usuarios WHERE id = :user_id LIMIT 1");
                $sqlUser->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $sqlUser->execute();
                $currentUser = $sqlUser->fetch(PDO::FETCH_ASSOC);

                if (!$currentUser) { // Doble chequeo por si algo falló en la segunda consulta
                    $errorMessage = "Error al cargar los datos de tu perfil. Por favor, reinicia sesión.";
                }
                // Si todo es OK, $currentUser estará disponible para la página que incluya este archivo
            }
        }
    }

} catch (PDOException $e) {
    // Capturar errores específicos de la base de datos PDO
    error_log("Error de BD en validación de sesión (process/auth_check.php): " . $e->getMessage());
    $errorMessage = "Error interno del servidor. Por favor, inténtalo más tarde o contacta a soporte.";
} catch (Exception $e) {
    // Capturar cualquier otra excepción general
    error_log("Error general en validación de sesión (process/auth_check.php): " . $e->getMessage());
    $errorMessage = "Ocurrió un problema inesperado. Por favor, reinicia sesión.";
}

// --- Lógica de Redirección si la Validación Falla ---
if (!empty($errorMessage)) {
    // Almacenar el mensaje de error en la sesión para mostrarlo en la página de login (flash message)
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => $errorMessage];
    print_r($_SESSION['flash_message']);
    header('Location: ' . $redirectUrl);
    exit();
}

?>
