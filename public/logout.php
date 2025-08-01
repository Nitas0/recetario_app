<?php
// public/logout.php
session_start();

// Destruir todas las variables de sesión
$_SESSION = array();

// Si se desea destruir la cookie de sesión, también se debe borrar.
// Nota: Esto destruirá la sesión, y no solo los datos de sesión.
// Esto es útil para asegurarse de que la sesión no pueda ser reanudada.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruir la sesión
session_destroy();

// Redirigir al usuario a la página de inicio/login
$_SESSION['message'] = '<p class="message">Has cerrado sesión correctamente.</p>';
header('Location: index.php');
exit();
?>