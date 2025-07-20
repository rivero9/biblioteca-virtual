<?php

require_once __DIR__ . "/../config/init.php";
require_once __DIR__ . "/../config/connection_db.php";

$response = ['success' => false, 'message' => ''];
$errors = [];

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
    $con->beginTransaction();

    try {
        // Recuperar y validar datos del RECURSO
        $resource_id = (int)($_POST['resource_id'] ?? 0);
        $title = trim(htmlspecialchars($_POST['title'] ?? '', ENT_QUOTES, 'UTF-8'));
        $resource_type = trim(htmlspecialchars($_POST['resource_type'] ?? '', ENT_QUOTES, 'UTF-8'));
        $category = trim(htmlspecialchars($_POST['category'] ?? '', ENT_QUOTES, 'UTF-8'));
        $publication_year = trim($_POST['publication_year'] ?? '');
        $description = trim(htmlspecialchars($_POST['description'] ?? '', ENT_QUOTES, 'UTF-8'));

        // Obtener rutas de archivos actuales (ocultas en el formulario)
        $current_pdf_path = $_POST['current_pdf_path'] ?? null;
        $current_video_path = $_POST['current_video_path'] ?? null;
        $current_cover_path = $_POST['current_cover_path'] ?? null;

        if ($resource_id <= 0) { $errors['resource_id'] = "ID de recurso inválido."; }
        if (empty($title)) { $errors['title'] = "El título es obligatorio."; }
        if (empty($resource_type)) { $errors['resource_type'] = "El tipo de recurso es obligatorio."; }
        if (empty($category)) { $errors['category'] = "La categoría es obligatoria."; }
        if (empty($publication_year) || !is_numeric($publication_year) || $publication_year < 1900 || $publication_year > date('Y')) { $errors['publication_year'] = "Año de publicación inválido."; }

        if (mb_strlen($title, 'UTF-8') > 255) { $errors['title'] = "El título es demasiado largo (máx. 255 caracteres)."; }
        if (mb_strlen($description, 'UTF-8') > 65535) { $errors['description'] = "La descripción es demasiado larga."; }
        if (mb_strlen($category, 'UTF-8') > 100) { $errors['category'] = "La categoría es demasiado larga (máx. 100 caracteres)."; }
        if (mb_strlen($resource_type, 'UTF-8') > 50) { $errors['resource_type'] = "El tipo de recurso es demasiado largo (máx. 50 caracteres)."; }

        // Manejo de subida y actualización de archivos
        $upload_dir = __DIR__ . "/../uploads/resources/";
        $pdf_dir = $upload_dir . "pdf/";
        $video_dir = $upload_dir . "videos/";
        $cover_dir = $upload_dir . "covers/";

        define('MAX_PDF_SIZE', 20 * 1024 * 1024); // 20 MB
        define('MAX_VIDEO_SIZE', 100 * 1024 * 1024); // 100 MB
        define('MAX_COVER_SIZE', 5 * 1024 * 1024); // 5 MB

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        $pdf_path = $current_pdf_path;
        if (isset($_FILES['book_pdf']) && $_FILES['book_pdf']['error'] == UPLOAD_ERR_OK) {
            $pdf_file = $_FILES['book_pdf'];
            $pdf_mime_type = finfo_file($finfo, $pdf_file['tmp_name']);
            $pdf_extension = pathinfo($pdf_file['name'], PATHINFO_EXTENSION);
            $pdf_filename = uniqid('pdf_') . '.' . $pdf_extension;
            $pdf_destination = $pdf_dir . $pdf_filename;

            if ($pdf_file['size'] > MAX_PDF_SIZE) { $errors['book_pdf'] = "El archivo PDF excede el tamaño máximo permitido (20MB)."; }
            elseif ($pdf_mime_type !== 'application/pdf') { $errors['book_pdf'] = "El archivo debe ser un documento PDF válido."; }
            elseif (!move_uploaded_file($pdf_file['tmp_name'], $pdf_destination)) { $errors['book_pdf'] = "Error al subir el nuevo archivo PDF."; }
            else {
                if ($current_pdf_path && file_exists(__DIR__ . "/../" . $current_pdf_path)) { unlink(__DIR__ . "/../" . $current_pdf_path); }
                $pdf_path = "uploads/resources/pdf/" . $pdf_filename;
            }
        } else if (isset($_FILES['book_pdf']) && $_FILES['book_pdf']['error'] != UPLOAD_ERR_NO_FILE) {
            $errors['book_pdf'] = "Error al subir el archivo PDF: " . $_FILES['book_pdf']['error'];
        }

        $video_path = $current_video_path;
        if (isset($_FILES['resource_video']) && $_FILES['resource_video']['error'] == UPLOAD_ERR_OK) {
            $video_file = $_FILES['resource_video'];
            $video_mime_type = finfo_file($finfo, $video_file['tmp_name']);
            $video_extension = pathinfo($video_file['name'], PATHINFO_EXTENSION);
            $video_filename = uniqid('video_') . '.' . $video_extension;
            $video_destination = $video_dir . $video_filename;
            $allowed_video_mimes = ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime', 'video/x-msvideo'];

            if ($video_file['size'] > MAX_VIDEO_SIZE) { $errors['resource_video'] = "El archivo de video excede el tamaño máximo permitido (100MB)."; }
            elseif (!in_array($video_mime_type, $allowed_video_mimes)) { $errors['resource_video'] = "Formato de video no permitido. Use MP4, WebM, Ogg, MOV o AVI."; }
            elseif (!move_uploaded_file($video_file['tmp_name'], $video_destination)) { $errors['resource_video'] = "Error al subir el nuevo archivo de video."; }
            else {
                if ($current_video_path && file_exists(__DIR__ . "/../" . $current_video_path)) { unlink(__DIR__ . "/../" . $current_video_path); }
                $video_path = "uploads/resources/videos/" . $video_filename;
            }
        } else if (isset($_FILES['resource_video']) && $_FILES['resource_video']['error'] != UPLOAD_ERR_NO_FILE) {
            $errors['resource_video'] = "Error al subir el archivo de video: " . $_FILES['resource_video']['error'];
        }

        $cover_path = $current_cover_path;
        if (isset($_FILES['book_cover']) && $_FILES['book_cover']['error'] == UPLOAD_ERR_OK) {
            $cover_file = $_FILES['book_cover'];
            $cover_mime_type = finfo_file($finfo, $cover_file['tmp_name']);
            $cover_extension = pathinfo($cover_file['name'], PATHINFO_EXTENSION);
            $cover_filename = uniqid('cover_') . '.' . $cover_extension;
            $cover_destination = $cover_dir . $cover_filename;
            $allowed_image_mimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

            if ($cover_file['size'] > MAX_COVER_SIZE) { $errors['book_cover'] = "La portada excede el tamaño máximo permitido (5MB)."; }
            elseif (!in_array($cover_mime_type, $allowed_image_mimes)) { $errors['book_cover'] = "La portada debe ser una imagen (JPG, PNG, GIF, WebP)."; }
            elseif (!move_uploaded_file($cover_file['tmp_name'], $cover_destination)) { $errors['book_cover'] = "Error al subir la nueva portada."; }
            else {
                if ($current_cover_path && file_exists(__DIR__ . "/../" . $current_cover_path)) { unlink(__DIR__ . "/../" . $current_cover_path); }
                $cover_path = "uploads/resources/covers/" . $cover_filename;
            }
        } else if (isset($_FILES['book_cover']) && $_FILES['book_cover']['error'] != UPLOAD_ERR_NO_FILE) {
            $errors['book_cover'] = "Error al subir la portada: " . $_FILES['book_cover']['error'];
        }
        finfo_close($finfo);

        // Validar Autores
        $authors_data = $_POST['authors'] ?? [];
        if (empty($authors_data)) {
            $errors['authors'] = "Debe añadir al menos un autor.";
        } else {
            foreach ($authors_data as $index => $author_info) {
                $author_name_raw = trim($author_info['name'] ?? '');
                $author_name = htmlspecialchars($author_name_raw, ENT_QUOTES, 'UTF-8');
                
                if (mb_strlen($author_name, 'UTF-8') > 200) { $errors["authors[{$index}][name]"] = "El nombre del autor es demasiado largo (máx. 200 caracteres)."; }
                if (empty($author_name)) { $errors["authors[{$index}][name]"] = "El nombre del autor es obligatorio."; }
                
                $email_contacto_autor = trim($author_info['email_contacto_autor'] ?? '');
                if (!empty($email_contacto_autor)) {
                    $email_contacto_autor = htmlspecialchars($email_contacto_autor, ENT_QUOTES, 'UTF-8');
                    if (!filter_var($email_contacto_autor, FILTER_VALIDATE_EMAIL)) { $errors["authors[{$index}][email_contacto_autor]"] = "Email de contacto inválido."; }
                    if (mb_strlen($email_contacto_autor, 'UTF-8') > 255) { $errors["authors[{$index}][email_contacto_autor]"] = "Email de contacto demasiado largo."; }
                }

                $telefono_contacto_autor = trim($author_info['telefono_contacto_autor'] ?? '');
                if (!empty($telefono_contacto_autor)) {
                    $telefono_contacto_autor = htmlspecialchars($telefono_contacto_autor, ENT_QUOTES, 'UTF-8');
                    if (!preg_match('/^[0-9\s\-\(\)\+]+$/', $telefono_contacto_autor) || mb_strlen($telefono_contacto_autor, 'UTF-8') > 50) { $errors["authors[{$index}][telefono_contacto_autor]"] = "Formato de teléfono inválido o demasiado largo (máx. 50 caracteres)."; }
                }

                $social_fields = ['social_linkedin', 'social_twitter', 'social_github', 'social_facebook'];
                foreach ($social_fields as $field) {
                    $social_url = trim($author_info[$field] ?? '');
                    if (!empty($social_url)) {
                        $social_url = htmlspecialchars($social_url, ENT_QUOTES, 'UTF-8');
                        if (!filter_var($social_url, FILTER_VALIDATE_URL)) { $errors["authors[{$index}][{$field}]"] = "URL de " . ucfirst(str_replace('social_', '', $field)) . " inválida."; }
                        if (mb_strlen($social_url, 'UTF-8') > 255) { $errors["authors[{$index}][{$field}]"] = "URL de " . ucfirst(str_replace('social_', '', $field)) . " demasiado larga."; }
                    }
                }
            }
        }

        if (!empty($errors)) {
            $response['message'] = 'Errores de validación.';
            $response['errors'] = $errors;
            $con->rollBack();
            echo json_encode($response);
            exit();
        }

        // Actualizar el Recurso en la tabla `recursos`
        $stmt_recurso = $con->prepare("UPDATE recursos SET titulo = :title, tipo_recurso = :resource_type, categoria = :category, anio_publicacion = :publication_year, descripcion = :description, ruta_pdf = :pdf_path, ruta_video = :video_path, ruta_portada = :cover_path WHERE id = :resource_id");
        $stmt_recurso->bindParam(':title', $title);
        $stmt_recurso->bindParam(':resource_type', $resource_type);
        $stmt_recurso->bindParam(':category', $category);
        $stmt_recurso->bindParam(':publication_year', $publication_year);
        $stmt_recurso->bindParam(':description', $description);
        $stmt_recurso->bindParam(':pdf_path', $pdf_path);
        $stmt_recurso->bindParam(':video_path', $video_path);
        $stmt_recurso->bindParam(':cover_path', $cover_path);
        $stmt_recurso->bindParam(':resource_id', $resource_id, PDO::PARAM_INT);
        $stmt_recurso->execute();

        // Gestionar Autores: Eliminar todas las vinculaciones existentes para este recurso
        $stmt_delete_links = $con->prepare("DELETE FROM recurso_autores WHERE id_recurso = :resource_id");
        $stmt_delete_links->bindParam(':resource_id', $resource_id, PDO::PARAM_INT);
        $stmt_delete_links->execute();

        // Procesar e insertar las nuevas vinculaciones de autores
        $stmt_find_author = $con->prepare("SELECT id_autor FROM autores WHERE nombre = :first_name AND apellido = :last_name");
        $stmt_insert_author = $con->prepare("INSERT INTO autores (nombre, apellido, email_contacto_autor, telefono_contacto_autor, social_linkedin, social_twitter, social_github, social_facebook) VALUES (:first_name, :last_name, :email, :phone, :linkedin, :twitter, :github, :facebook)");
        $stmt_link_author = $con->prepare("INSERT INTO recurso_autores (id_recurso, id_autor) VALUES (:resource_id, :author_id)");

        foreach ($authors_data as $author_info) {
            $author_id = null;
            $full_name_raw = trim($author_info['name']);
            $full_name_sanitized = htmlspecialchars($full_name_raw, ENT_QUOTES, 'UTF-8');
            
            $name_parts = explode(' ', $full_name_sanitized, 2);
            $first_name = $name_parts[0];
            $last_name = $name_parts[1] ?? '';

            $email_contacto_autor = trim(htmlspecialchars($author_info['email_contacto_autor'] ?? null, ENT_QUOTES, 'UTF-8'));
            $telefono_contacto_autor = trim(htmlspecialchars($author_info['telefono_contacto_autor'] ?? null, ENT_QUOTES, 'UTF-8'));
            $social_linkedin = trim(htmlspecialchars($author_info['social_linkedin'] ?? null, ENT_QUOTES, 'UTF-8'));
            $social_twitter = trim(htmlspecialchars($author_info['social_twitter'] ?? null, ENT_QUOTES, 'UTF-8'));
            $social_github = trim(htmlspecialchars($author_info['social_github'] ?? null, ENT_QUOTES, 'UTF-8'));
            $social_facebook = trim(htmlspecialchars($author_info['social_facebook'] ?? null, ENT_QUOTES, 'UTF-8'));

            if (!empty($author_info['id_autor'])) {
                $author_id = (int)$author_info['id_autor'];
                $check_stmt = $con->prepare("SELECT id_autor FROM autores WHERE id_autor = :author_id");
                $check_stmt->bindParam(':author_id', $author_id, PDO::PARAM_INT);
                $check_stmt->execute();
                if ($check_stmt->rowCount() === 0) { $author_id = null; }
            }

            if (is_null($author_id)) {
                $stmt_find_author->bindParam(':first_name', $first_name);
                $stmt_find_author->bindParam(':last_name', $last_name);
                $stmt_find_author->execute();
                $found_author = $stmt_find_author->fetch(PDO::FETCH_ASSOC);

                if ($found_author) {
                    $author_id = $found_author['id_autor'];
                } else {
                    $stmt_insert_author->bindParam(':first_name', $first_name);
                    $stmt_insert_author->bindParam(':last_name', $last_name);
                    $stmt_insert_author->bindParam(':email', $email_contacto_autor);
                    $stmt_insert_author->bindParam(':phone', $telefono_contacto_autor);
                    $stmt_insert_author->bindParam(':linkedin', $social_linkedin);
                    $stmt_insert_author->bindParam(':twitter', $social_twitter);
                    $stmt_insert_author->bindParam(':github', $social_github);
                    $stmt_insert_author->bindParam(':facebook', $social_facebook);
                    $stmt_insert_author->execute();
                    $author_id = $con->lastInsertId();
                }
            }

            if ($author_id && $resource_id) {
                $check_link_stmt = $con->prepare("SELECT COUNT(*) FROM recurso_autores WHERE id_recurso = :resource_id AND id_autor = :author_id");
                $check_link_stmt->bindParam(':resource_id', $resource_id, PDO::PARAM_INT);
                $check_link_stmt->bindParam(':author_id', $author_id, PDO::PARAM_INT);
                $check_link_stmt->execute();
                if ($check_link_stmt->fetchColumn() == 0) {
                    $stmt_link_author->bindParam(':resource_id', $resource_id, PDO::PARAM_INT);
                    $stmt_link_author->bindParam(':author_id', $author_id, PDO::PARAM_INT);
                    $stmt_link_author->execute();
                }
            }
        }

        $con->commit();
        $response['success'] = true;
        $response['message'] = 'Recurso actualizado exitosamente.';

    } catch (PDOException $e) {
        $con->rollBack();
        error_log("Error PDO al editar recurso: " . $e->getMessage());
        $response['message'] = 'Error de base de datos al actualizar el recurso.';
        $response['debug_error'] = $e->getMessage();
    } catch (Exception $e) {
        $con->rollBack();
        error_log("Error general al editar recurso: " . $e->getMessage());
        $response['message'] = 'Error interno del servidor al actualizar el recurso.';
        $response['debug_error'] = $e->getMessage();
    } finally {
        $stmt_recurso = null;
        $stmt_delete_links = null;
        $stmt_find_author = null;
        $stmt_insert_author = null;
        $stmt_link_author = null;
        $con = null;
    }
} else {
    $response['message'] = 'Método de solicitud no permitido.';
}
   
echo json_encode($response);

?>
