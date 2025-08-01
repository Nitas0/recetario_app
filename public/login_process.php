<?php
// public/login_process.php
session_start();
require_once '../includes/db_connect.php'; // Incluye la conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validación básica
    if (empty($email) || empty($password)) {
        $_SESSION['message'] = '<p class="message error">Por favor, ingresa tu correo y contraseña.</p>';
        header('Location: index.php');
        exit();
    }

    try {
        // Buscar el usuario por email
        $stmt = $pdo->prepare("SELECT id_usuario, nombre_usuario, contrasena FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['contrasena'])) {
            // Contraseña correcta, iniciar sesión
            $_SESSION['user_id'] = $user['id_usuario'];
            $_SESSION['username'] = $user['nombre_usuario'];
            $_SESSION['message'] = '<p class="message">¡Bienvenido de nuevo, ' . htmlspecialchars($user['nombre_usuario']) . '!</p>';

            header('Location: dashboard.php'); // Redirigir a la página principal de recetas
            exit();
        } else {
            // Credenciales incorrectas
            $_SESSION['message'] = '<p class="message error">Correo o contraseña incorrectos.</p>';
            header('Location: index.php');
            exit();
        }

    } catch (\PDOException $e) {
        error_log("Error de login: " . $e->getMessage());
        $_SESSION['message'] = '<p class="message error">Ocurrió un error al intentar iniciar sesión. Inténtalo de nuevo más tarde.</p>';
        header('Location: index.php');
        exit();
    }
} else {
    // Si no es una petición POST, redirigir al formulario de login
    header('Location: index.php');
    exit();
}
?>