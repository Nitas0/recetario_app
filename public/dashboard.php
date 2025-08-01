<?php
/**
 * dashboard.php
 *
 * Esta es la página principal para un usuario autenticado. Muestra una lista de sus
 * recetas personales, permite buscarlas, filtrarlas por categoría y proporciona
 * acciones para ver, editar o eliminar cada receta.
 *
 * Lógica principal:
 * 1.  Protección de la Página: Verifica si el `user_id` existe en la sesión. Si no,
 *     redirige al usuario a la página de login (`index.php`).
 * 2.  Carga de Categorías: Realiza una consulta a la base de datos para obtener todas
 *     las categorías disponibles. Estas se usarán para poblar el menú desplegable
 *     de filtro en el formulario.
 * 3.  Lógica de Búsqueda y Filtrado:
 *     a. Recoge los parámetros `search` y `category` de la URL (método GET).
 *     b. Construye una consulta SQL dinámica. La base de la consulta selecciona las
 *        recetas del usuario actual (`WHERE r.id_usuario = ?`).
 *     c. Si se proporciona un `category_id`, añade un `JOIN` con la tabla
 *        `recetas_categorias` y una condición `AND` para filtrar por esa categoría.
 *     d. Si se proporciona un `search_query`, añade condiciones `AND` con `LIKE` para
 *        buscar el término en el nombre, los ingredientes y la preparación.
 *     e. Utiliza consultas preparadas con un array de parámetros (`$params`) para
 *        prevenir inyección SQL, añadiendo los valores de forma segura.
 *     f. Ejecuta la consulta y obtiene las recetas en el array `$recetas`.
 * 4.  Visualización (HTML):
 *     a. Muestra un mensaje de bienvenida personalizado con el nombre de usuario.
 *     b. Presenta un formulario de búsqueda y filtrado. Los valores de búsqueda y
 *        categoría actuales se mantienen en los campos para una mejor UX.
 *     c. Si no se encuentran recetas, muestra un mensaje informativo.
 *     d. Si hay recetas, las muestra en una cuadrícula (`recipes-grid`).
 *        - Cada receta es una "tarjeta" (`recipe-card`) que se puede arrastrar y soltar
 *          (la funcionalidad de drag-and-drop se activa en `main.js`).
 *        - Muestra la imagen de la receta (o una por defecto si no existe), el nombre
 *          y el tiempo de preparación.
 *        - Proporciona botones para "Ver", "Editar" y "Eliminar".
 * 5.  Modal de Confirmación de Eliminación:
 *     - Incluye un modal HTML (inicialmente oculto) para confirmar la eliminación.
 *     - El botón "Eliminar" de cada receta activa este modal vía JavaScript (`main.js`)
 *       pasándole el ID y el nombre de la receta.
 */

$page_title = "Mis Recetas";
require_once '../includes/header.php';

// --- 1. Protección de la página ---
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = '<p class="message error">Debes iniciar sesión para acceder a tus recetas.</p>';
    header('Location: index.php');
    exit();
}

// Incluye el archivo de conexión a la base de datos.
require_once '../includes/db_connect.php';

// Obtiene datos del usuario de la sesión.
$user_id = $_SESSION['user_id'];
$username = htmlspecialchars($_SESSION['username']);

// --- 2. Cargar Categorías para el Filtro ---
$categorias = [];
try {
    // Prepara y ejecuta la consulta para obtener todas las categorías.
    $stmt_cat = $pdo->query("SELECT id_categoria, nombre_categoria FROM categorias ORDER BY nombre_categoria ASC");
    $categorias = $stmt_cat->fetchAll();
} catch (\PDOException $e) {
    // Si hay un error, lo registra. No es un error fatal, la página puede continuar.
    error_log("Error al cargar categorías: " . $e->getMessage());
}

// --- 3. Lógica de Búsqueda y Filtrado ---
$recetas = []; // Inicializa el array de recetas.

// Obtiene los parámetros de búsqueda y categoría de la URL.
$search_query = trim($_GET['search'] ?? '');
$category_id = trim($_GET['category'] ?? '');

// Construye la consulta SQL dinámicamente basándose en los filtros proporcionados.
$sql = "SELECT DISTINCT r.id_receta, r.nombre_receta, r.tiempo_preparacion_minutos, r.imagen_url 
        FROM recetas r";
$params = []; // Inicializa el array de parámetros para la consulta preparada.

if (!empty($category_id)) {
    $sql .= " JOIN recetas_categorias rc ON r.id_receta = rc.id_receta";
}

$sql .= " WHERE r.id_usuario = ?";
$params[] = $user_id;

if (!empty($category_id)) {
    $sql .= " AND rc.id_categoria = ?";
    $params[] = $category_id;
}

if (!empty($search_query)) {
    $sql .= " AND (r.nombre_receta LIKE ? OR r.ingredientes LIKE ? OR r.preparacion LIKE ?)";
    $like_param = '%' . $search_query . '%';
    $params[] = $like_param;
    $params[] = $like_param;
    $params[] = $like_param;
}

$sql .= " ORDER BY r.fecha_creacion DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $recetas = $stmt->fetchAll();
} catch (\PDOException $e) {
    error_log("Error al cargar recetas con filtros: " . $e->getMessage());
    $_SESSION['message'] = '<p class="message error">Error al cargar tus recetas. Por favor, inténtalo de nuevo.</p>';
}
?>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h2>¡Hola, <?= $username ?>!</h2>
    </div>

    <?php
    if (isset($_SESSION['message'])) {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
    ?>

    <div class="search-filter-section">
        <form action="dashboard.php" method="GET">
            <input type="text" name="search" placeholder="Buscar por nombre, ingredientes..." value="<?= htmlspecialchars($search_query) ?>">
            
            <select name="category">
                <option value="">Todas las categorías</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= $categoria['id_categoria'] ?>" <?= ($category_id == $categoria['id_categoria']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($categoria['nombre_categoria']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn">Buscar</button>
            
            <?php if (!empty($search_query) || !empty($category_id)): ?>
                <a href="dashboard.php" class="btn btn-cancel">Limpiar Filtros</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (empty($recetas)): ?>
        <div class="message">
            <p>No se encontraron recetas que coincidan con tus filtros.</p>
        </div>
    <?php else: ?>
        <div class="recipes-grid" id="recipes-grid">
            <?php foreach ($recetas as $receta): ?>
                <div class="recipe-card" data-id="<?= $receta['id_receta'] ?>">
                    <img src="<?= htmlspecialchars($receta['imagen_url'] ?: 'img/flor-de-cerezo.png') ?>" alt="Imagen de <?= htmlspecialchars($receta['nombre_receta']) ?>" class="recipe-card-image" onerror="this.onerror=null;this.src='img/flor-de-cerezo.png';">
                    <div class="recipe-card-content">
                        <h3><?= htmlspecialchars($receta['nombre_receta']) ?></h3>
                        <p>Tiempo: <?= htmlspecialchars($receta['tiempo_preparacion_minutos'] ?? 'N/A') ?> min</p>
                        <div class="recipe-actions">
                            <a href="view_recipe.php?id=<?= $receta['id_receta'] ?>" class="btn btn-info">Ver</a>
                            <a href="edit_recipe.php?id=<?= $receta['id_receta'] ?>" class="btn btn-warning">Editar</a>
                            <button type="button" class="btn btn-danger btn-delete" 
                                    data-id="<?= $receta['id_receta'] ?>" 
                                    data-name="<?= htmlspecialchars($receta['nombre_receta']) ?>">
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Modal de Confirmación de Eliminación -->
    <div id="delete-confirm-modal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Confirmar Eliminación</h2>
            <p>¿Estás seguro de que quieres eliminar la receta "<span id="modal-recipe-name"></span>"?</p>
            <form id="delete-form" action="delete_recipe.php" method="POST">
                <input type="hidden" name="id_receta" id="modal-recipe-id">
                <div class="modal-actions">
                    <button type="button" class="btn btn-cancel" id="cancel-delete">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>
