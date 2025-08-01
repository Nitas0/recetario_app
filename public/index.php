<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Si el usuario ya está logueado, redirigir al dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
$page_title = "Bienvenido a tu Recetario Online";
require_once '../includes/header.php';


?>

<div class="container">
    <h2>Iniciar Sesión</h2>

    <?php
    // Mostrar mensajes de error o éxito
    if (isset($_SESSION['message'])) {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
    ?>

    <form action="login_process.php" method="POST">
        <label for="login_email">Correo Electrónico:</label>
        <input type="email" id="login_email" name="email" required>

        <label for="login_password">Contraseña:</label>
        <input type="password" id="login_password" name="password" required>

        <button type="submit">Iniciar Sesión</button>
    </form>

    <p>¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a></p>
</div>

<?php
require_once '../includes/footer.php';
?>
