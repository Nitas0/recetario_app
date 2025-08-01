<?php
$page_title = "Ver Receta";
require_once '../includes/header.php';

// public/view_recipe.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/db_connect.php';

// Proteger esta página
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = '<p class="message error">Debes iniciar sesión para ver recetas.</p>';
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$recipe = null;
$is_explore_mode = isset($_GET['explore']) && $_GET['explore'] == 1;

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_receta = $_GET['id'];

    try {
        if ($is_explore_mode) {
            // En modo exploración, cualquiera puede ver la receta. Unimos con usuarios para obtener el autor.
            $stmt = $pdo->prepare("SELECT r.*, u.nombre_usuario AS autor FROM recetas r JOIN usuarios u ON r.id_usuario = u.id_usuario WHERE r.id_receta = ?");
            $stmt->execute([$id_receta]);
            $recipe = $stmt->fetch();
        } else {
            // Modo normal: solo el propietario puede ver la receta.
            $stmt = $pdo->prepare("SELECT * FROM recetas WHERE id_receta = ? AND id_usuario = ?");
            $stmt->execute([$id_receta, $user_id]);
            $recipe = $stmt->fetch();
        }

        if (!$recipe) {
            $_SESSION['message'] = '<p class="message error">Receta no encontrada o no tienes permiso para verla.</p>';
            header('Location: dashboard.php');
            exit();
        }
    } catch (\PDOException $e) {
        error_log("Error al cargar detalle de receta: " . $e->getMessage());
        $_SESSION['message'] = '<p class="message error">Error al cargar la receta. Inténtalo de nuevo más tarde.</p>';
        header('Location: dashboard.php');
        exit();
    }
} else {
    $_SESSION['message'] = '<p class="message error">ID de receta no válido.</p>';
    header('Location: dashboard.php');
    exit();
}
?>

<div class="recipe-detail-container">
    <?php if ($recipe): ?>
        <h2><?php echo htmlspecialchars($recipe['nombre_receta']); ?></h2>

        <img src="<?= htmlspecialchars($recipe['imagen_url'] ?? 'img/flor-de-cerezo.png') ?>" 
                 alt="Imagen de <?= htmlspecialchars($recipe['nombre_receta']) ?>" 
                 class="recipe-image-full" 
                 onerror="this.onerror=null;this.src='img/flor-de-cerezo.png';">

        <?php if (isset($recipe['autor'])): ?>
            <p><strong>Autor:</strong> <?php echo htmlspecialchars($recipe['autor']); ?></p>
        <?php endif; ?>

        <p><strong>Tiempo de preparación:</strong> <?php echo htmlspecialchars($recipe['tiempo_preparacion_minutos']); ?> minutos</p>
        <p><strong>Fecha de creación:</strong> <?php echo date('d/m/Y H:i', strtotime($recipe['fecha_creacion'])); ?></p>

        <h3>Ingredientes:</h3>
        <p><?php echo nl2br(htmlspecialchars($recipe['ingredientes'])); ?></p>
        <h3>Preparación:</h3>
        <p><?php echo nl2br(htmlspecialchars($recipe['preparacion'])); ?></p>
        
        <div class="detail-actions">
            <?php
            // Solo mostrar botones de editar/eliminar si el usuario es el propietario Y NO está en modo exploración.
            $is_owner = ($recipe['id_usuario'] == $user_id);
            if ($is_owner && !$is_explore_mode) {
                echo '<a href="edit_recipe.php?id=' . $recipe['id_receta'] . '" class="btn btn-warning">Editar Receta</a>';
                echo '<a href="delete_recipe.php?id=' . $recipe['id_receta'] . '" class="btn btn-danger" onclick="return confirm(\'¿Estás seguro de que quieres eliminar esta receta?\');">Eliminar Receta</a>';
            }

            // El botón de volver cambia dependiendo de la página de origen.
            if ($is_explore_mode) {
                echo '<a href="explore_recipes.php" class="btn btn-primary">Volver a Explorar</a>';
            } else {
                echo '<a href="dashboard.php" class="btn btn-primary">Volver a mis Recetas</a>';
            }
            ?>
        </div>
    <?php endif; ?>
</div>

<?php
require_once '../includes/footer.php';
?>