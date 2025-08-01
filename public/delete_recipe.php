<?php
/**
 * delete_recipe.php
 *
 * Este script se encarga de procesar la solicitud para eliminar una receta.
 * No tiene interfaz de usuario; es un endpoint que recibe un ID de receta por POST.
 *
 * Lógica principal:
 * 1.  Inicio de Sesión y Conexión a BD: Inicia la sesión y establece la conexión
 *     con la base de datos.
 * 2.  Control de Acceso: Verifica que el usuario esté autenticado. Si no, lo redirige
 *     al login.
 * 3.  Validación de Entrada: Comprueba que se haya recibido un `id_receta` a través
 *     de POST y que sea un valor numérico.
 * 4.  Eliminación Segura:
 *     a. Prepara una consulta `DELETE` que incluye una condición crucial:
 *        `WHERE id_receta = ? AND id_usuario = ?`.
 *        Esta doble condición asegura que un usuario solo pueda eliminar recetas
 *        que le pertenecen, previniendo que un usuario malintencionado pueda
 *        borrar recetas de otros simplemente cambiando el ID en la solicitud.
 *     b. Ejecuta la consulta con el ID de la receta y el ID del usuario de la sesión.
 * 5.  Verificación del Resultado:
 *     a. Comprueba `$stmt->rowCount()`. Si es mayor que 0, la receta fue encontrada
 *        y eliminada con éxito. Se guarda un mensaje de éxito en la sesión.
 *     b. Si `rowCount()` es 0, significa que no se eliminó ninguna fila. Esto puede
 *        ocurrir si la receta no existe o si no pertenece al usuario actual. Se
 *        guarda un mensaje de error informativo.
 * 6.  Manejo de Errores: Si ocurre una `PDOException`, se registra el error y se
 *     guarda un mensaje genérico en la sesión.
 * 7.  Redirección: Independientemente del resultado, el script siempre redirige al
 *     usuario de vuelta al `dashboard.php`, donde se mostrará el mensaje de
 *     éxito o error correspondiente.
 */

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