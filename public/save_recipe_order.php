<?php
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