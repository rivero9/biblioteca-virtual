<?php

require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../config/connection_db.php";

$response = ['success' => false, 'message' => '', 'resources' => []];

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

try {
    // Consulta para obtener todos los recursos y sus autores asociados
    $stmt = $con->prepare("
        SELECT
            r.id,
            r.titulo,
            r.tipo_recurso,
            r.categoria,
            r.anio_publicacion,
            r.ruta_pdf,
            r.ruta_video,
            r.ruta_portada,
            GROUP_CONCAT(CONCAT(a.nombre, ' ', a.apellido) SEPARATOR ', ') AS autores_nombres
        FROM
            recursos r
        LEFT JOIN
            recurso_autores ra ON r.id = ra.id_recurso
        LEFT JOIN
            autores a ON ra.id_autor = a.id_autor
        GROUP BY
            r.id
        ORDER BY
            r.fecha_subida DESC;
    ");

    $stmt->execute();
    $resources = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response['success'] = true;
    $response['message'] = 'Recursos obtenidos exitosamente.';
    $response['resources'] = $resources;

} catch (PDOException $e) {
    error_log("Error PDO al obtener recursos: " . $e->getMessage());
    $response['message'] = 'Error de base de datos al obtener los recursos.';
    $response['debug_error'] = $e->getMessage();
} catch (Exception $e) {
    error_log("Error general al obtener recursos: " . $e->getMessage());
    $response['message'] = 'Error interno del servidor al obtener los recursos.';
    $response['debug_error'] = $e->getMessage();
} finally {
    $con = null;
}

echo json_encode($response);
?>
