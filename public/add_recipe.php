<?php
// public/add_recipe.php
// Permite a los usuarios añadir nuevas recetas a la base de datos.

$page_title = "Añadir Nueva Receta";
require_once '../includes/header.php';
require_once '../includes/db_connect.php';

// Proteger esta página: solo los usuarios autenticados pueden acceder.
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = '<p class="message error">Debes iniciar sesión para añadir recetas.</p>';
    header('Location: index.php');
    exit();
}

// Inicializar variables para mantener los valores en el formulario si hay un error de validación.
$nombre_receta = '';
$ingredientes = '';
$preparacion = '';
$tiempo_preparacion_minutos = '';

// --- Procesamiento del Formulario ---
// Se ejecuta cuando el usuario envía el formulario para crear una nueva receta.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_receta = trim($_POST['nombre_receta']);
    $ingredientes = trim($_POST['ingredientes']);
    $preparacion = trim($_POST['preparacion']);
    $tiempo_preparacion_minutos = filter_var($_POST['tiempo_preparacion_minutos'], FILTER_VALIDATE_INT);
    $user_id = $_SESSION['user_id'];
    $errors = [];
    $imagen_url = null; // Inicializar la URL de la imagen como nula

    // Validación de campos de texto
    if (empty($nombre_receta)) $errors[] = "El nombre de la receta es obligatorio.";
    if (empty($ingredientes)) $errors[] = "Los ingredientes son obligatorios.";
    if (empty($preparacion)) $errors[] = "La preparación es obligatoria.";
    if ($tiempo_preparacion_minutos === false || $tiempo_preparacion_minutos < 0) {
        $errors[] = "El tiempo de preparación debe ser un número entero positivo.";
    }

    // --- Lógica de Subida de Imagen ---
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'img/'; // Directorio de subida
        // Crear un nombre de archivo único para evitar sobreescribir archivos
        $file_name = uniqid('receta_', true) . '-' . basename($_FILES['imagen']['name']);
        $target_file = $upload_dir . $file_name;
        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validar tipo de archivo (solo imágenes)
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($image_file_type, $allowed_types)) {
            $errors[] = "Error: Solo se permiten archivos de imagen (JPG, JPEG, PNG, GIF).";
        }

        // Validar tamaño del archivo (ej. máximo 5MB)
        if ($_FILES['imagen']['size'] > 5 * 1024 * 1024) {
            $errors[] = "Error: El archivo es demasiado grande. El tamaño máximo es de 5MB.";
        }

        // Si no hay errores de validación de la imagen, mover el archivo
        if (empty($errors)) {
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file)) {
                $imagen_url = $target_file; // Guardar la ruta relativa en la variable
            } else {
                $errors[] = "Hubo un error al subir tu archivo.";
            }
        }
    }

    // Si hay errores de validación, se muestran al usuario.
    if (!empty($errors)) {
        $_SESSION['message'] = '<p class="message error">' . implode('<br>', $errors) . '</p>';
    } else {
        // Si no hay errores, se inserta la receta en la base de datos.
        try {
            $sql = "INSERT INTO recetas (id_usuario, nombre_receta, ingredientes, preparacion, tiempo_preparacion_minutos, imagen_url) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user_id, $nombre_receta, $ingredientes, $preparacion, $tiempo_preparacion_minutos, $imagen_url]);

            $_SESSION['message'] = '<p class="message success">Receta "' . htmlspecialchars($nombre_receta) . '" añadida con éxito.</p>';
            header('Location: dashboard.php');
            exit();
        } catch (\PDOException $e) {
            error_log("Error al añadir receta: " . $e->getMessage());
            $_SESSION['message'] = '<p class="message error">Ocurrió un error al guardar la receta. Por favor, inténtalo de nuevo.</p>';
        }
    }
}
?>

<div class="form-container">
    <h2>Añadir Nueva Receta</h2>

    <?php
    if (isset($_SESSION['message'])) {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
    ?>

    <form action="add_recipe.php" method="POST" enctype="multipart/form-data">
        <label for="nombre_receta">Nombre de la Receta:</label>
        <input type="text" id="nombre_receta" name="nombre_receta" value="<?= htmlspecialchars($nombre_receta); ?>" required>

        <label for="ingredientes">Ingredientes (uno por línea):</label>
        <textarea id="ingredientes" name="ingredientes" required><?= htmlspecialchars($ingredientes); ?></textarea>

        <label for="preparacion">Preparación (paso a paso):</label>
        <textarea id="preparacion" name="preparacion" required><?= htmlspecialchars($preparacion); ?></textarea>

        <label for="tiempo_preparacion_minutos">Tiempo de Preparación (minutos):</label>
        <input type="number" id="tiempo_preparacion_minutos" name="tiempo_preparacion_minutos" value="<?= htmlspecialchars($tiempo_preparacion_minutos); ?>" min="0">

        <label for="imagen">Imagen de la Receta (opcional):</label>
        <input type="file" id="imagen" name="imagen" accept="image/png, image/jpeg, image/gif">

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar Receta</button>
            <a href="dashboard.php" class="btn btn-cancel">Cancelar</a>
        </div>
    </form>
</div>

<?php
require_once '../includes/footer.php';
?>
