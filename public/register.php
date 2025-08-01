<?php
$page_title = "Registro - Recetario Online";
require_once '../includes/header.php';
?>

<div class="container">
    <h2>Registro de Usuario</h2>

    <?php
    // Mostrar mensajes de error o éxito
    if (isset($_SESSION['message'])) {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
    ?>

    <form action="register_process.php" method="POST">
        <label for="username">Nombre de Usuario:</label>
        <input type="text" id="username" name="username" required>

        <label for="email">Correo Electrónico:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
        <small>La contraseña debe tener al menos 8 caracteres.</small>

        <label for="confirm_password">Confirmar Contraseña:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit">Registrarse</button>
    </form>

    <p>¿Ya tienes una cuenta? <a href="index.php">Inicia Sesión aquí</a></p>
</div>

<?php
require_once '../includes/footer.php';
?>
