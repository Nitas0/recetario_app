<?php
/**
 * index.php
 *
 * Esta es la página de inicio de la aplicación y funciona como el portal de acceso (login).
 * Su comportamiento cambia dependiendo de si el usuario ya ha iniciado sesión o no.
 *
 * Lógica principal:
 * 1.  Inicio de Sesión: Se asegura de que una sesión PHP esté activa.
 * 2.  Redirección de Usuarios Autenticados:
 *     - Comprueba si `$_SESSION['user_id']` ya está definido.
 *     - Si es así, significa que el usuario ya ha iniciado sesión. En este caso, lo
 *       redirige inmediatamente a su panel principal (`dashboard.php`) para evitar
 *       que vea la página de login de nuevo.
 * 3.  Inclusión de Cabecera: Carga el archivo `header.php` para mostrar la estructura
 *     HTML y la barra de navegación (que para usuarios no autenticados mostrará
 *     opciones de "Login" y "Registrarse").
 * 4.  Visualización del Formulario de Login (HTML):
 *     - Muestra un título de bienvenida.
 *     - Si hay algún mensaje en la sesión (por ejemplo, un error de login anterior o
 *       un mensaje de éxito tras el registro), lo muestra y luego lo elimina de la
 *       sesión para que no aparezca de nuevo.
 *     - Presenta un formulario que envía los datos a `login_process.php` vía POST.
 *     - El formulario solicita el email y la contraseña del usuario.
 *     - Incluye un enlace a `register.php` para que los nuevos usuarios puedan crear
 *       una cuenta.
 * 5.  Inclusión de Pie de Página: Carga `footer.php` para cerrar las etiquetas HTML
 *     y cargar los scripts de JavaScript.
 */

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
