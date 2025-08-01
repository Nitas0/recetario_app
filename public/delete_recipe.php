<?php
// public/delete_recipe.php
// Procesa la eliminación de una receta, asegurándose de que el usuario sea el propietario.

session_start();
require_once '../includes/db_connect.php';

// Proteger esta página
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = '<p class="message error">Debes iniciar sesión para eliminar recetas.</p>';
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['id_receta']) && is_numeric($_POST['id_receta'])) {
    $id_receta = $_POST['id_receta'];

    try {
        // Eliminar la receta, asegurándose de que pertenezca al usuario logueado
        $stmt = $pdo->prepare("DELETE FROM recetas WHERE id_receta = ? AND id_usuario = ?");
        $stmt->execute([$id_receta, $user_id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = '<p class="message success">Receta eliminada con éxito.</p>';
        } else {
            $_SESSION['message'] = '<p class="message error">No se pudo eliminar la receta (posiblemente no existe o no te pertenece).</p>';
        }
    } catch (\PDOException $e) {
        error_log("Error al eliminar receta: " . $e->getMessage());
        $_SESSION['message'] = '<p class="message error">Ocurrió un error al eliminar la receta. Por favor, inténtalo de nuevo más tarde.</p>';
    }
} else {
    $_SESSION['message'] = '<p class="message error">ID de receta no válido para eliminar.</p>';
}

header('Location: dashboard.php'); // Redirigir siempre de vuelta al dashboard
exit();
?>