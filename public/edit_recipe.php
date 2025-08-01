<?php
/**
 * edit_recipe.php
 *
 * Esta página permite a un usuario modificar una de sus propias recetas. Se encarga tanto
 * de mostrar el formulario con los datos existentes de la receta como de procesar la
 * actualización cuando el usuario envía los cambios.
 *
 * Lógica principal:
 * 1.  Configuración y Protección:
 *     - Se activan los informes de errores para depuración.
 *     - Se incluyen los ficheros `header.php` y `db_connect.php`.
 *     - Se comprueba si el usuario está autenticado; si no, se le redirige al login.
 * 2.  Carga de Datos de la Receta:
 *     - Obtiene el ID de la receta de la URL (`$_GET['id']`) o del formulario (`$_POST['id_receta']`).
 *     - Realiza una consulta `SELECT` para obtener los datos de la receta, asegurándose
 *       de que el `id_usuario` coincida con el de la sesión. Esto es una medida de
 *       seguridad clave para que un usuario no pueda editar recetas de otros.
 *     - Si la receta no se encuentra o no pertenece al usuario, se le redirige al dashboard.
 *     - Carga todas las categorías disponibles y las categorías ya asociadas a esta receta.
 * 3.  Procesamiento del Formulario (cuando el método es POST):
 *     a. Recoge y limpia los datos del formulario.
 *     b. Realiza validaciones (campos no vacíos, tiempo de preparación numérico).
 *     c. Gestión de la Imagen:
 *        - Si se sube una nueva imagen, valida su tamaño y tipo.
 *        - Si la validación es correcta, mueve el nuevo archivo a `img/`.
 *        - Si la receta ya tenía una imagen, el archivo antiguo se elimina del servidor (`unlink`).
 *        - La ruta de la nueva imagen se guarda para la actualización.
 *     d. Actualización en la Base de Datos (dentro de una transacción):
 *        - Inicia una transacción (`$pdo->beginTransaction()`) para asegurar la integridad
 *          de los datos. Si algo falla, se puede revertir todo.
 *        - Ejecuta un `UPDATE` en la tabla `recetas` con los nuevos datos.
 *        - Elimina todas las asociaciones de categorías existentes para esa receta.
 *        - Inserta las nuevas asociaciones de categorías seleccionadas en el formulario.
 *        - Si todo es correcto, confirma la transacción (`$pdo->commit()`).
 *     e. Manejo de Errores: Si ocurre una excepción, se revierte la transacción (`$pdo->rollBack()`)
 *        y se muestra un mensaje de error.
 *     f. Redirección: Tras una actualización exitosa, redirige al dashboard.
 * 4.  Visualización del Formulario (HTML):
 *     - Muestra un formulario pre-rellenado con los datos de la receta cargados.
 *     - Muestra la imagen actual de la receta.
 *     - Presenta una lista de checkboxes para las categorías, marcando las que ya están asociadas.
 * 5.  Validación del Lado del Cliente (JavaScript):
 *     - Un pequeño script de JS previene el envío del formulario si el archivo de imagen
 *       seleccionado supera el tamaño máximo permitido (5MB), proporcionando feedback inmediato.
 */

$page_title = "Editar Receta";
require_once '../includes/header.php';

// --- Configuración de Errores ---
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
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_receta = $_GET['id'];
} elseif (isset($_POST['id_receta']) && is_numeric($_POST['id_receta'])) {
    $id_receta = $_POST['id_receta'];
} else {
    $_SESSION['message'] = '<p class="message error">ID de receta no válido para editar.</p>';
    header('Location: dashboard.php');
    exit();
}

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

    // Validaciones básicas
    if (empty($nombre_receta)) $errors[] = "El nombre de la receta es obligatorio.";
    if (empty($ingredientes)) $errors[] = "Los ingredientes son obligatorios.";
    if (empty($preparacion)) $errors[] = "La preparación es obligatoria.";
    if ($tiempo_preparacion_minutos === false || $tiempo_preparacion_minutos < 0) {
        $errors[] = "El tiempo de preparación debe ser un número entero positivo.";
    }

    // Procesar imagen si se ha subido una nueva
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'img/';
        $extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        $file_name = 'receta_' . uniqid() . '.' . $extension;
        $target_file = $upload_dir . $file_name;

        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($extension, $allowed_types)) {
            $errors[] = "Solo se permiten archivos JPG, PNG y GIF.";
        } elseif (move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file)) {
            if (!empty($recipe['imagen_url']) && file_exists($recipe['imagen_url'])) {
                @unlink($recipe['imagen_url']);
            }
            $imagen_url_a_guardar = $target_file;
        } else {
            $errors[] = "Error al subir la imagen.";
        }
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("UPDATE recetas SET nombre_receta = ?, ingredientes = ?, preparacion = ?, tiempo_preparacion_minutos = ?, imagen_url = ? WHERE id_receta = ? AND id_usuario = ?");
            $stmt->execute([$nombre_receta, $ingredientes, $preparacion, $tiempo_preparacion_minutos, $imagen_url_a_guardar, $id_receta, $user_id]);

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

    if (!empty($errors)) {
        $_SESSION['message'] = '<p class="message error">' . implode('<br>', $errors) . '</p>';
    }
}
?>

<div class="form-container">
    <h2>Editar Receta</h2>

    <?php if (isset($_SESSION['message'])) {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    } ?>

    <form action="edit_recipe.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id_receta" value="<?= htmlspecialchars($id_receta); ?>">

        <label for="nombre_receta">Nombre de la Receta:</label>
        <input type="text" id="nombre_receta" name="nombre_receta" value="<?= htmlspecialchars($nombre_receta); ?>" required>
        
        <label for="ingredientes">Ingredientes:</label>
        <textarea id="ingredientes" name="ingredientes" required><?= htmlspecialchars($ingredientes); ?></textarea>
        
        <label for="preparacion">Preparación:</label>
        <textarea id="preparacion" name="preparacion" required><?= htmlspecialchars($preparacion); ?></textarea>

        <label for="tiempo_preparacion_minutos">Tiempo de Preparación (minutos):</label>
        <input type="number" id="tiempo_preparacion_minutos" name="tiempo_preparacion_minutos" value="<?= htmlspecialchars($tiempo_preparacion_minutos); ?>" min="0">

        <div class="image-categories-container">
            <div class="image-container">
                <label>Imagen Actual:</label>
                <img src="<?= htmlspecialchars($recipe['imagen_url'] ?: 'img/flor-de-cerezo.png'); ?>" alt="Imagen actual de la receta" class="current-image">

                <label for="imagen">Subir Nueva Imagen (opcional):</label>
                <input type="file" id="imagen" name="imagen" accept="image/jpeg,image/png,image/gif">
            </div>

            <div class="categorias-container">
                <?php foreach ($categorias as $categoria): ?>
                    <div class="categoria-checkbox">
                        <input type="checkbox" id="categoria_<?= $categoria['id_categoria']; ?>" name="categorias[]" value="<?= $categoria['id_categoria']; ?>" <?= in_array($categoria['id_categoria'], $categorias_receta) ? 'checked' : ''; ?>>
                        <label for="categoria_<?= $categoria['id_categoria']; ?>"><?= htmlspecialchars($categoria['nombre_categoria']); ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
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
        }
    }
});
</script>

<?php
require_once '../includes/footer.php';
?>
