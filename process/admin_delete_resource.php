<?php

require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../config/connection_db.php";

$response = ['success' => false, 'message' => ''];

$db = new Database();
/** @var PDO $con */ // Indica a Intelephense que $con es un objeto PDO
$con = $db->connect();

if (!$con) {
    $response['message'] = 'Error de conexión a la base de datos.';
    echo json_encode($response);
    exit();
}

require_once __DIR__ . "/auth_check_admin.php";

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $resource_id = (int)($_POST['resource_id'] ?? 0);

    if ($resource_id <= 0) {
        $response['message'] = 'ID de recurso inválido.';
        echo json_encode($response);
        exit();
    }

    $con->beginTransaction();

    try {
        // 1. Obtener las rutas de los archivos asociados al recurso
        $stmt_get_paths = $con->prepare("SELECT ruta_pdf, ruta_video, ruta_portada FROM recursos WHERE id = :resource_id");
        $stmt_get_paths->bindParam(':resource_id', $resource_id, PDO::PARAM_INT);
        $stmt_get_paths->execute();
        $resource_paths = $stmt_get_paths->fetch(PDO::FETCH_ASSOC);

        if (!$resource_paths) {
            $response['message'] = 'Recurso no encontrado.';
            $con->rollBack();
            echo json_encode($response);
            exit();
        }

        // 2. Eliminar las vinculaciones del recurso con los autores
        $stmt_delete_author_links = $con->prepare("DELETE FROM recurso_autores WHERE id_recurso = :resource_id");
        $stmt_delete_author_links->bindParam(':resource_id', $resource_id, PDO::PARAM_INT);
        $stmt_delete_author_links->execute();

        // 3. Eliminar el recurso de la tabla 'recursos'
        $stmt_delete_resource = $con->prepare("DELETE FROM recursos WHERE id = :resource_id");
        $stmt_delete_resource->bindParam(':resource_id', $resource_id, PDO::PARAM_INT);
        $stmt_delete_resource->execute();

        // Verificar si se eliminó alguna fila
        if ($stmt_delete_resource->rowCount() > 0) {
            // 4. Eliminar los archivos físicos del servidor
            $base_upload_dir = __DIR__ . "/../"; // Directorio base donde están las carpetas 'uploads'

            if ($resource_paths['ruta_pdf'] && file_exists($base_upload_dir . $resource_paths['ruta_pdf'])) {
                unlink($base_upload_dir . $resource_paths['ruta_pdf']);
            }
            if ($resource_paths['ruta_video'] && file_exists($base_upload_dir . $resource_paths['ruta_video'])) {
                unlink($base_upload_dir . $resource_paths['ruta_video']);
            }
            if ($resource_paths['ruta_portada'] && file_exists($base_upload_dir . $resource_paths['ruta_portada'])) {
                unlink($base_upload_dir . $resource_paths['ruta_portada']);
            }

            $con->commit();
            $response['success'] = true;
            $response['message'] = 'Recurso eliminado exitosamente.';
        } else {
            $response['message'] = 'No se encontró el recurso para eliminar o ya fue eliminado.';
            $con->rollBack(); // Revertir si por alguna razón no se eliminó el recurso (aunque la validación inicial ya lo cubrió)
        }

    } catch (PDOException $e) {
        $con->rollBack();
        error_log("Error PDO al eliminar recurso: " . $e->getMessage());
        $response['message'] = 'Error de base de datos al eliminar el recurso. Por favor, inténtelo de nuevo más tarde.';
        $response['debug_error'] = $e->getMessage();
    } catch (Exception $e) {
        $con->rollBack();
        error_log("Error general al eliminar recurso: " . $e->getMessage());
        $response['message'] = 'Error interno del servidor al eliminar el recurso. Por favor, inténtelo de nuevo más tarde.';
        $response['debug_error'] = $e->getMessage();
    } finally {
        $stmt_get_paths = null;
        $stmt_delete_author_links = null;
        $stmt_delete_resource = null;
        $con = null;
    }
} else {
    $response['message'] = 'Método de solicitud no permitido.';
}

echo json_encode($response);
?>
