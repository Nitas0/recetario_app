<?php
// public/edit_recipe.php
// Permite a los usuarios editar sus propias recetas.

$page_title = "Editar Receta";
require_once '../includes/header.php';

// --- Configuración de Errores ---
// Activa la visualización y el registro de errores para facilitar la depuración.
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);
ini_set('error_log', __DIR__ . '/debug.log');

require_once '../includes/db_connect.php';

// --- Protección de la Página ---
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = '<p class="message error">Debes iniciar sesión para editar recetas.</p>';
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$recipe = null;
$id_receta = null;

// --- Carga Inicial de Datos ---
// Obtiene el ID de la receta desde GET o POST y carga sus datos.
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_receta = $_GET['id'];
} elseif (isset($_POST['id_receta']) && is_numeric($_POST['id_receta'])) {
    $id_receta = $_POST['id_receta'];
} else {
    $_SESSION['message'] = '<p class="message error">ID de receta no válido para editar.</p>';
    header('Location: dashboard.php');
    exit();
}

// Carga la receta desde la base de datos, asegurándose de que pertenezca al usuario.
try {
    $stmt = $pdo->prepare("SELECT * FROM recetas WHERE id_receta = ? AND id_usuario = ?");
    $stmt->execute([$id_receta, $user_id]);
    $recipe = $stmt->fetch();

    if (!$recipe) {
        $_SESSION['message'] = '<p class="message error">Receta no encontrada o no tienes permiso para editarla.</p>';
        header('Location: dashboard.php');
        exit();
    }
} catch (\PDOException $e) {
    error_log("Error al cargar receta: " . $e->getMessage());
    $_SESSION['message'] = '<p class="message error">Error al cargar la receta.</p>';
    header('Location: dashboard.php');
    exit();
}

// Cargar categorías
try {
    $stmt = $pdo->query("SELECT id_categoria, nombre_categoria FROM categorias ORDER BY nombre_categoria");
    $categorias = $stmt->fetchAll();
    
    $stmt = $pdo->prepare("SELECT id_categoria FROM recetas_categorias WHERE id_receta = ?");
    $stmt->execute([$id_receta]);
    $categorias_receta = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (\PDOException $e) {
    error_log("Error al cargar categorías: " . $e->getMessage());
    $categorias = [];
    $categorias_receta = [];
}

// Inicializar variables
$nombre_receta = $recipe['nombre_receta'];
$ingredientes = $recipe['ingredientes'];
$preparacion = $recipe['preparacion'];
$tiempo_preparacion_minutos = $recipe['tiempo_preparacion_minutos'];

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_receta = trim($_POST['nombre_receta']);
    $ingredientes = trim($_POST['ingredientes']);
    $preparacion = trim($_POST['preparacion']);
    $tiempo_preparacion_minutos = filter_var($_POST['tiempo_preparacion_minutos'], FILTER_VALIDATE_INT);
    $imagen_url_a_guardar = $recipe['imagen_url'];
    
    $errors = [];
    $should_process = true;

    // Validaciones básicas
    if (empty($nombre_receta)) $errors[] = "El nombre de la receta es obligatorio.";
    if (empty($ingredientes)) $errors[] = "Los ingredientes son obligatorios.";
    if (empty($preparacion)) $errors[] = "La preparación es obligatoria.";
    if ($tiempo_preparacion_minutos === false || $tiempo_preparacion_minutos < 0) {
        $errors[] = "El tiempo de preparación debe ser un número entero positivo.";
    }

    // Procesar imagen si se ha subido una nueva
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $filesize = $_FILES['imagen']['size'];
        $max_size = 5 * 1024 * 1024; // 5MB en bytes
        $should_process = true;

        // Validar tamaño antes de cualquier otro procesamiento
        if ($filesize > $max_size) {
            $errors[] = "La imagen no debe superar los 5MB. Tamaño actual: " . round($filesize / 1024 / 1024, 2) . "MB";
            error_log("Archivo demasiado grande: " . $filesize . " bytes");
            $_SESSION['message'] = '<p class="message error">La imagen no debe superar los 5MB. Tamaño actual: ' . round($filesize / 1024 / 1024, 2) . 'MB</p>';
            $should_process = false;
        }

        if ($should_process) {
            $upload_dir = 'img/';
            $extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
            $file_name = 'receta_' . uniqid() . '.' . $extension;
            $target_file = $upload_dir . $file_name;

            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($extension, $allowed_types)) {
                $errors[] = "Solo se permiten archivos JPG, PNG y GIF.";
                $_SESSION['message'] = '<p class="message error">Solo se permiten archivos JPG, PNG y GIF.</p>';
                $should_process = false;
            } elseif (move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file)) {
                if (!empty($recipe['imagen_url']) && file_exists($recipe['imagen_url'])) {
                    @unlink($recipe['imagen_url']);
                }
                $imagen_url_a_guardar = $target_file;
            } else {
                $errors[] = "Error al subir la imagen.";
                $_SESSION['message'] = '<p class="message error">Error al subir la imagen.</p>';
                $should_process = false;
            }
        }
    }

    // Si no hay errores o son errores que no son de imagen, proceder con la actualización
    if (empty($errors) && $should_process) {
        try {
            $pdo->beginTransaction();

            // Actualiza los datos principales de la receta.
            $stmt = $pdo->prepare("UPDATE recetas SET 
                nombre_receta = ?, 
                ingredientes = ?, 
                preparacion = ?, 
                tiempo_preparacion_minutos = ?, 
                imagen_url = ? 
                WHERE id_receta = ? AND id_usuario = ?");
            
            $stmt->execute([
                $nombre_receta,
                $ingredientes,
                $preparacion,
                $tiempo_preparacion_minutos,
                $imagen_url_a_guardar,
                $id_receta,
                $user_id
            ]);

            // Actualiza las categorías asociadas a la receta.
            $stmt = $pdo->prepare("DELETE FROM recetas_categorias WHERE id_receta = ?");
            $stmt->execute([$id_receta]);

            if (isset($_POST['categorias']) && is_array($_POST['categorias'])) {
                $stmt = $pdo->prepare("INSERT INTO recetas_categorias (id_receta, id_categoria) VALUES (?, ?)");
                foreach ($_POST['categorias'] as $categoria_id) {
                    $stmt->execute([$id_receta, $categoria_id]);
                }
            }

            $pdo->commit();
            $_SESSION['message'] = '<p class="message success">Receta actualizada con éxito.</p>';
            header('Location: dashboard.php');
            exit();
        } catch (\PDOException $e) {
            $pdo->rollBack();
            error_log("Error en la actualización: " . $e->getMessage());
            $_SESSION['message'] = '<p class="message error">Error al actualizar la receta.</p>';
        }
    }

    // Si hay errores, mostrarlos
    if (!empty($errors)) {
        $_SESSION['message'] = '<p class="message error">' . implode('<br>', $errors) . '</p>';
    }
}

show_form:
?>

<div class="form-container">
    <h2>Editar Receta</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <?php echo $_SESSION['message']; ?>
    <?php endif; ?>

    <form action="edit_recipe.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id_receta" value="<?php echo htmlspecialchars($id_receta); ?>">

        <label for="nombre_receta">Nombre de la Receta:</label>
        <input type="text" id="nombre_receta" name="nombre_receta" value="<?php echo htmlspecialchars($nombre_receta); ?>" required>
        <div class="form-group">
<label for="ingredientes">Ingredientes:</label>
        <textarea id="ingredientes" name="ingredientes" required><?php echo htmlspecialchars($ingredientes); ?></textarea>
        </div>
        <div class="form-group">
<label for="preparacion">Preparación:</label>
        <textarea id="preparacion" name="preparacion" required><?php echo htmlspecialchars($preparacion); ?></textarea>
        </div>

        

        

        <label for="tiempo_preparacion_minutos">Tiempo de Preparación (minutos):</label>
        <input type="number" id="tiempo_preparacion_minutos" name="tiempo_preparacion_minutos" value="<?php echo htmlspecialchars($tiempo_preparacion_minutos); ?>" min="0">

        <div class="image-categories-container">
            <div class="image-container">
                <label>Imagen Actual:</label>
                <?php if (!empty($recipe['imagen_url']) && file_exists($recipe['imagen_url'])): ?>
                    <img src="<?php echo htmlspecialchars($recipe['imagen_url']); ?>" 
                         alt="Imagen actual de la receta" 
                         class="current-image">
                <?php else: ?>
                    <img src="img/flor-de-cerezo.png" 
                         alt="Imagen por defecto" 
                         class="current-image">
                <?php endif; ?>

                <label for="imagen">Subir Nueva Imagen (opcional):</label>
                <input type="file" id="imagen" name="imagen" accept="image/jpeg,image/png,image/gif">
            </div>

            <div class="categorias-container">
                <?php foreach ($categorias as $categoria): ?>
                    <div class="categoria-checkbox">
                        <input type="checkbox" 
                               id="categoria_<?php echo $categoria['id_categoria']; ?>" 
                               name="categorias[]" 
                               value="<?php echo $categoria['id_categoria']; ?>"
                               <?php echo in_array($categoria['id_categoria'], $categorias_receta) ? 'checked' : ''; ?>>
                        <label for="categoria_<?php echo $categoria['id_categoria']; ?>">
                            <?php echo htmlspecialchars($categoria['nombre_categoria']); ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-group">
        </div>
        <div id="mostrarErrores"></div>
        <div class="form-actions">
            <button type="submit" class="btn btn-warning">Actualizar Receta</button>
            <a href="dashboard.php" class="btn btn-danger">Cancelar</a>
        </div>
    </form>
</div>

<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const fileInput = document.querySelector('#imagen');
    if (fileInput.files.length > 0) {
        const fileSize = fileInput.files[0].size;
        const maxSize = 5 * 1024 * 1024; // 5MB
        if (fileSize > maxSize) {
            e.preventDefault();
            document.getElementById('mostrarErrores').innerHTML = '<p class="message error">La imagen no debe superar los 5MB. Tamaño actual: ' + Math.round(fileSize/1024/1024 * 100) / 100 + 'MB</p>';
            //alert('La imagen no debe superar los 5MB. Tamaño actual: ' + Math.round(fileSize/1024/1024 * 100) / 100 + 'MB');
        }
    }
});
</script>

<?php
require_once '../includes/footer.php';
?>
