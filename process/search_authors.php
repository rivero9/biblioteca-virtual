<?php
// process/search_authors.php

require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../config/connection_db.php";

$response = ['success' => false, 'message' => '', 'authors' => []];

$db = new Database();
/** @var PDO $con */ // Indica a Intelephense que $con es un objeto PDO
$con = $db->connect();

if (!$con) {
    $response['message'] = 'Error de conexión a la base de datos.';
    echo json_encode($response);
    exit();
}

require_once __DIR__ . "/auth_check_admin.php"; // Asegura que solo administradores accedan

header('Content-Type: application/json');

if (isset($_GET['query']) && !empty(trim($_GET['query']))) {
    $search_query = '%' . trim($_GET['query']) . '%';

    try {
        // Buscar autores por nombre o apellido
        $stmt = $con->prepare("
            SELECT
                id_autor,
                nombre,
                apellido,
                email_contacto_autor,
                telefono_contacto_autor,
                social_linkedin,
                social_twitter,
                social_github,
                social_facebook
            FROM
                autores
            WHERE
                nombre LIKE :query OR apellido LIKE :query
            LIMIT 10; -- Limitar resultados para eficiencia
        ");
        $stmt->bindParam(':query', $search_query, PDO::PARAM_STR);
        $stmt->execute();
        $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response['success'] = true;
        $response['authors'] = $authors;

    } catch (PDOException $e) {
        error_log("Error PDO al buscar autores: " . $e->getMessage());
        $response['message'] = 'Error de base de datos al buscar autores.';
        $response['debug_error'] = $e->getMessage();
    } catch (Exception $e) {
        error_log("Error general al buscar autores: " . $e->getMessage());
        $response['message'] = 'Error interno del servidor al buscar autores.';
        $response['debug_error'] = $e->getMessage();
    } finally {
        $con = null;
    }
} else {
    $response['message'] = 'Parámetro de búsqueda "query" no proporcionado o vacío.';
}

echo json_encode($response);
?>
