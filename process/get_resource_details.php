<?php

require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../config/connection_db.php";

$response = ['success' => false, 'message' => '', 'resource' => null];

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

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $resource_id = (int)$_GET['id'];

    try {
        // Obtener los detalles del recurso
        $stmt_resource = $con->prepare("SELECT id, titulo, tipo_recurso, categoria, anio_publicacion, descripcion, ruta_pdf, ruta_video, ruta_portada FROM recursos WHERE id = :resource_id");
        $stmt_resource->bindParam(':resource_id', $resource_id, PDO::PARAM_INT);
        $stmt_resource->execute();
        $resource = $stmt_resource->fetch(PDO::FETCH_ASSOC);

        if (!$resource) {
            $response['message'] = 'Recurso no encontrado.';
            echo json_encode($response);
            exit();
        }

        // Obtener los autores asociados a este recurso
        $stmt_authors = $con->prepare("
            SELECT
                a.id_autor,
                a.nombre,
                a.apellido,
                a.email_contacto_autor,
                a.telefono_contacto_autor,
                a.social_linkedin,
                a.social_twitter,
                a.social_github,
                a.social_facebook,
                a.id_usuario
            FROM
                autores a
            JOIN
                recurso_autores ra ON a.id_autor = ra.id_autor
            WHERE
                ra.id_recurso = :resource_id
            ORDER BY
                a.nombre ASC;
        ");
        $stmt_authors->bindParam(':resource_id', $resource_id, PDO::PARAM_INT);
        $stmt_authors->execute();
        $authors = $stmt_authors->fetchAll(PDO::FETCH_ASSOC);

        $resource['autores'] = $authors;

        $response['success'] = true;
        $response['message'] = 'Detalles del recurso obtenidos exitosamente.';
        $response['resource'] = $resource;

    } catch (PDOException $e) {
        error_log("Error PDO al obtener detalles del recurso: " . $e->getMessage());
        $response['message'] = 'Error de base de datos al obtener los detalles del recurso.';
        $response['debug_error'] = $e->getMessage();
    } catch (Exception $e) {
        error_log("Error general al obtener detalles del recurso: " . $e->getMessage());
        $response['message'] = 'Error interno del servidor al obtener los detalles del recurso.';
        $response['debug_error'] = $e->getMessage();
    } finally {
        $con = null;
    }
} else {
    $response['message'] = 'ID de recurso no proporcionado o inválido.';
}

echo json_encode($response);
?>
