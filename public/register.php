<?php
/**
 * register.php
 *
 * Esta página presenta el formulario de registro para nuevos usuarios.
 *
 * Lógica principal:
 * 1.  Inclusión de Cabecera: Carga el `header.php` para la estructura HTML y la
 *     barra de navegación.
 * 2.  Visualización de Mensajes: Comprueba si existe algún mensaje en la sesión
 *     (por ejemplo, errores de validación del `register_process.php`) y lo muestra
 *     al usuario. Después de mostrarlo, lo elimina para que no vuelva a aparecer.
 * 3.  Formulario de Registro (HTML):
 *     - Presenta un formulario que envía los datos a `register_process.php` vía POST.
 *     - Solicita el nombre de usuario, correo electrónico, contraseña y la confirmación
 *       de la contraseña.
 *     - Incluye una pequeña nota sobre el requisito de longitud de la contraseña.
 *     - Proporciona un enlace a la página de login (`index.php`) para los usuarios
 *       que ya tienen una cuenta.
 * 4.  Inclusión de Pie de Página: Carga el `footer.php` para cerrar el HTML y cargar
 *     los scripts de JavaScript.
 */

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
