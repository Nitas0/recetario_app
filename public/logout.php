<?php
/**
 * logout.php
 *
 * Este script se encarga de cerrar la sesión de un usuario. No tiene interfaz de usuario.
 * Su función es limpiar y destruir la sesión activa y luego redirigir al usuario
 * a la página de inicio.
 *
 * Lógica principal:
 * 1.  Inicio de Sesión: Inicia la sesión existente para poder manipularla.
 * 2.  Limpieza de Variables de Sesión:
 *     - `$_SESSION = array();` elimina todas las variables almacenadas en la sesión
 *       actual (como `user_id` y `username`), desvinculando al usuario.
 * 3.  Destrucción de la Cookie de Sesión (Paso Opcional pero Recomendado):
 *     - Comprueba si se están usando cookies para la sesión.
 *     - Obtiene los parámetros de la cookie de sesión.
 *     - Llama a `setcookie()` con el nombre de la sesión, un valor vacío y una fecha
 *       de expiración en el pasado. Esto le indica al navegador que elimine la cookie,
 *       haciendo que la sesión sea completamente inválida en el lado del cliente.
 * 4.  Destrucción de la Sesión en el Servidor:
 *     - `session_destroy()` elimina el archivo de sesión del servidor, completando
 *       el proceso de cierre de sesión.
 * 5.  Redirección:
 *     - Establece un mensaje de confirmación en `$_SESSION['message']`. Aunque la sesión
 *       se destruye, este mensaje se puede pasar a la siguiente página antes de la
 *       redirección final.
 *     - Redirige al usuario a la página de inicio (`index.php`), donde verá el
 *       mensaje de "Has cerrado sesión correctamente".
 */

session_start();

// Destruir todas las variables de sesión
$_SESSION = array();

// Si se desea destruir la cookie de sesión, también se debe borrar.
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
session_start(); // Se necesita iniciar una nueva sesión para pasar el mensaje
$_SESSION['message'] = '<p class="message">Has cerrado sesión correctamente.</p>';
header('Location: index.php');
exit();
?>