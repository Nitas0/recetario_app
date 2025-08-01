<?php
/**
 * explore_recipes.php
 *
 * Esta página permite a los usuarios autenticados descubrir y explorar recetas creadas
 * por otros miembros de la comunidad. Excluye las recetas del propio usuario.
 *
 * Lógica principal:
 * 1.  Protección de la Página: Al igual que el dashboard, verifica que el usuario
 *     haya iniciado sesión. Si no, lo redirige al login.
 * 2.  Carga de Categorías: Obtiene todas las categorías de la base de datos para
 *     poblar el menú de filtro, permitiendo a los usuarios buscar por tipo de plato.
 * 3.  Lógica de Búsqueda y Filtrado:
 *     a. Recoge los parámetros `search` y `category` de la URL (GET).
 *     b. Construye una consulta SQL dinámica para buscar recetas.
 *     c. La consulta base hace un `JOIN` entre `recetas` y `usuarios` para poder
 *        mostrar el nombre del autor de cada receta.
 *     d. La condición `WHERE r.id_usuario != ?` es fundamental: excluye las recetas
 *        del usuario que está realizando la búsqueda, para que solo vea las de otros.
 *     e. Si se selecciona una categoría, se añade un `JOIN` y una condición `AND` para
 *        filtrar por `id_categoria`.
 *     f. Si se introduce un término de búsqueda, la consulta busca coincidencias en
 *        el nombre de la receta, ingredientes, preparación y también en el nombre
 *        del autor (`u.nombre_usuario`).
 *     g. Se utilizan consultas preparadas para garantizar la seguridad.
 * 4.  Visualización (HTML):
 *     a. Muestra un formulario de búsqueda y filtro similar al del dashboard.
 *     b. Si no se encuentran recetas, muestra un mensaje indicándolo.
 *     c. Si hay recetas, las presenta en una cuadrícula (`recipes-grid`).
 *        - Cada tarjeta de receta (`recipe-card`) muestra la imagen, el nombre de la
 *          receta, el tiempo de preparación y, de forma destacada, el autor.
 *        - A diferencia del dashboard, la única acción disponible es "Ver Receta".
 *        - El enlace para ver la receta incluye un parámetro `&explore=1` para que la
 *          página `view_recipe.php` sepa que el usuario viene desde la sección de
 *          exploración y pueda mostrar un botón de "Volver a Explorar".
 */

$page_title = "Explorar Recetas";
require_once '../includes/header.php';

// --- 1. Protección de la página ---
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = '<p class="message error">Debes iniciar sesión para explorar recetas.</p>';
    header('Location: index.php');
    exit();
}

// Incluye el archivo de conexión a la base de datos.
require_once '../includes/db_connect.php';

// Obtiene datos del usuario de la sesión.
$user_id = $_SESSION['user_id'];

// --- 2. Cargar Categorías para el Filtro ---
$categorias = [];
try {
    $stmt_cat = $pdo->query("SELECT id_categoria, nombre_categoria FROM categorias ORDER BY nombre_categoria ASC");
    $categorias = $stmt_cat->fetchAll();
} catch (\PDOException $e) {
    error_log("Error al cargar categorías: " . $e->getMessage());
}

// --- 3. Lógica de Búsqueda y Filtrado ---
$recetas = [];

$search_query = trim($_GET['search'] ?? '');
$category_id = trim($_GET['category'] ?? '');

$sql = "
    SELECT DISTINCT
        r.id_receta, 
        r.nombre_receta, 
        r.tiempo_preparacion_minutos, 
        r.imagen_url, 
        u.nombre_usuario AS autor
    FROM recetas r
    JOIN usuarios u ON r.id_usuario = u.id_usuario";

if (!empty($category_id)) {
    $sql .= " JOIN recetas_categorias rc ON r.id_receta = rc.id_receta";
}

$sql .= " WHERE r.id_usuario != ?";
$params = [$user_id];

if (!empty($category_id)) {
    $sql .= " AND rc.id_categoria = ?";
    $params[] = $category_id;
}

if (!empty($search_query)) {
    $sql .= " AND (r.nombre_receta LIKE ? OR r.ingredientes LIKE ? OR r.preparacion LIKE ? OR u.nombre_usuario LIKE ?)";
    $search_param = '%' . $search_query . '%';
    array_push($params, $search_param, $search_param, $search_param, $search_param);
}

$sql .= " ORDER BY r.fecha_creacion DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $recetas = $stmt->fetchAll();
} catch (\PDOException $e) {
    error_log("Error al cargar recetas para explorar: " . $e->getMessage());
    $_SESSION['message'] = '<p class="message error">Error al cargar las recetas. Inténtalo de nuevo.</p>';
}
?>

<div class="explore-container">
    <h2>Explorar Recetas de Otros Usuarios</h2>

    <?php
    if (isset($_SESSION['message'])) {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
    ?>

    <div class="search-filter-section">
        <form action="explore_recipes.php" method="GET">
            <input type="text" name="search" placeholder="Buscar por receta, autor, ingredientes..." value="<?= htmlspecialchars($search_query) ?>">
            
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
                <a href="explore_recipes.php" class="btn btn-cancel">Limpiar Filtros</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (empty($recetas)): ?>
        <div class="message">
            <p>No se encontraron recetas que coincidan con tus filtros.</p>
        </div>
    <?php else: ?>
        <div class="recipes-grid">
            <?php foreach ($recetas as $receta): ?>
                <div class="recipe-card">
                    <img src="<?= htmlspecialchars($receta['imagen_url'] ?? 'img/flor-de-cerezo.png') ?>" alt="Imagen de <?= htmlspecialchars($receta['nombre_receta']) ?>" class="recipe-card-image" onerror="this.onerror=null;this.src='img/flor-de-cerezo.png';">
                    <div class="recipe-card-content">
                        <h3><?= htmlspecialchars($receta['nombre_receta']) ?></h3>
                        <p class="author-info">Por: <?= htmlspecialchars($receta['autor']) ?></p>
                        <p>Tiempo: <?= htmlspecialchars($receta['tiempo_preparacion_minutos'] ?? 'N/A') ?> min</p>
                        <div class="recipe-actions">
                            <a href="view_recipe.php?id=<?= $receta['id_receta'] ?>&explore=1" class="btn btn-info">Ver Receta</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
require_once '../includes/footer.php';
?>
