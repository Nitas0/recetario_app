<?php
/**
 * save_recipe_order.php
 *
 * Este es un endpoint de API que se encarga de guardar el orden personalizado de las
 * recetas de un usuario. Se activa mediante una llamada AJAX (fetch) desde el frontend
 * (específicamente, desde `main.js`) cuando el usuario reordena las recetas en el
 * dashboard mediante la función de arrastrar y soltar (drag and drop).
 *
 * Lógica principal:
 * 1.  Configuración y Seguridad:
 *     - Inicia la sesión y se conecta a la base de datos.
 *     - Establece la cabecera `Content-Type` a `application/json` para que el cliente
 *       interprete la respuesta correctamente.
 *     - Protege el endpoint, verificando que el usuario esté autenticado. Si no lo está,
 *       devuelve una respuesta JSON de error y termina la ejecución.
 * 2.  Recepción y Validación de Datos:
 *     - Lee el cuerpo de la solicitud (`php://input`) y lo decodifica de JSON a un array PHP.
 *     - Espera recibir un array con la clave `order`, que debe contener una lista de los
 *       IDs de las recetas en el nuevo orden.
 *     - Si los datos no son válidos, devuelve un error JSON.
 * 3.  Actualización en la Base de Datos (dentro de una transacción):
 *     - Inicia una transacción para asegurar que todas las actualizaciones se realicen
 *       correctamente o ninguna si ocurre un error.
 *     - Prepara una consulta `UPDATE` para modificar la columna `orden` de una receta.
 *     - Itera sobre el array de IDs de recetas recibido:
 *       - Para cada `recipe_id`, ejecuta la consulta `UPDATE`, asignando la posición
 *         (índice del array + 1) a la columna `orden`.
 *       - La condición `AND id_usuario = ?` es una medida de seguridad para asegurar
 *         que un usuario solo pueda modificar el orden de sus propias recetas.
 *     - Si el bucle se completa sin errores, confirma la transacción (`commit`).
 * 4.  Respuesta JSON:
 *     - Si la transacción es exitosa, devuelve `{'success': true}`.
 *     - Si ocurre una `PDOException`, revierte la transacción (`rollBack`), registra el
 *       error y devuelve `{'success': false}` con un mensaje de error.
 */

session_start();
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

// Protect endpoint
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Authentication required.']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Get data from request
$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['order']) || !is_array($data['order'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid order data.']);
    exit();
}

$order = $data['order'];

// Update the database
$pdo->beginTransaction();

try {
    $stmt = $pdo->prepare("UPDATE recetas SET orden = ? WHERE id_receta = ? AND id_usuario = ?");

    foreach ($order as $index => $recipe_id) {
        // The order is 1-based in the UI, so we use $index + 1
        $stmt->execute([$index + 1, $recipe_id, $user_id]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Order saved successfully.']);

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Error saving recipe order: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'A database error occurred.']);
}
?>