<?php
// public/register_process.php
session_start();
require_once '../includes/db_connect.php'; // Incluye la conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // --- Validación del lado del servidor (PHP) ---
    $errors = [];

    if (empty($username)) {
        $errors[] = "El nombre de usuario es obligatorio.";
    }
    if (empty($email)) {
        $errors[] = "El correo electrónico es obligatorio.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El formato del correo electrónico es inválido.";
    }
    if (empty($password)) {
        $errors[] = "La contraseña es obligatoria.";
    } elseif (strlen($password) < 8) {
        $errors[] = "La contraseña debe tener al menos 8 caracteres.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Las contraseñas no coinciden.";
    }

    // Si hay errores, guardarlos en la sesión y redirigir
    if (!empty($errors)) {
        $_SESSION['message'] = '<p class="message error">' . implode('<br>', $errors) . '</p>';
        header('Location: register.php');
        exit();
    }

    // Verificar si el usuario o email ya existen
    try {
        // Verificar nombre de usuario
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE nombre_usuario = ?");
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = "El nombre de usuario ya está en uso.";
        }

        // Verificar email
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = "El correo electrónico ya está registrado.";
        }

        if (!empty($errors)) {
            $_SESSION['message'] = '<p class="message error">' . implode('<br>', $errors) . '</p>';
            header('Location: register.php');
            exit();
        }

        // Hashear la contraseña antes de guardarla
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insertar nuevo usuario en la base de datos
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre_usuario, email, contrasena) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password]);

        $_SESSION['message'] = '<p class="message">¡Registro exitoso! Ahora puedes iniciar sesión.</p>';
        header('Location: index.php'); // Redirigir a la página de login
        exit();

    } catch (\PDOException $e) {
        // Manejar errores de base de datos
        error_log("Error de registro: " . $e->getMessage()); // Registrar el error para depuración
        $_SESSION['message'] = '<p class="message error">Ocurrió un error al registrar el usuario. Por favor, inténtalo de nuevo más tarde.</p>';
        header('Location: register.php');
        exit();
    }
} else {
    // Si no es una petición POST, redirigir al formulario de registro
    header('Location: register.php');
    exit();
}
?>