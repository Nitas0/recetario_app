<?php
/**
 * login_process.php
 *
 * Este script gestiona el proceso de inicio de sesión del usuario. No tiene interfaz
 * de usuario; su única función es recibir los datos del formulario de login, validarlos
 * y, si son correctos, establecer la sesión del usuario.
 *
 * Lógica principal:
 * 1.  Inicio de Sesión y Conexión a BD: Inicia la sesión y establece la conexión
 *     con la base de datos.
 * 2.  Verificación del Método: Comprueba que la solicitud sea de tipo POST. Si no lo es,
 *     redirige al usuario de vuelta al formulario de login (`index.php`).
 * 3.  Recogida y Validación de Datos:
 *     - Obtiene el email y la contraseña del `$_POST`.
 *     - Realiza una validación básica para asegurarse de que ambos campos no estén vacíos.
 * 4.  Consulta a la Base de Datos:
 *     - Prepara una consulta `SELECT` para buscar un usuario por su dirección de email.
 *     - Ejecuta la consulta de forma segura para prevenir inyección SQL.
 *     - Obtiene el resultado con `$stmt->fetch()`.
 * 5.  Verificación de Credenciales:
 *     - Comprueba si se encontró un usuario (`$user` no es falso).
 *     - Si se encontró un usuario, utiliza `password_verify()` para comparar la contraseña
 *       proporcionada con el hash almacenado en la base de datos. Esta es la forma
 *       segura de verificar contraseñas.
 * 6.  Gestión de la Sesión:
 *     - Si las credenciales son correctas, almacena el `id_usuario` y el `nombre_usuario`
 *       en la variable `$_SESSION`. Estos datos identificarán al usuario en las demás
 *       páginas protegidas.
 *     - Guarda un mensaje de bienvenida en la sesión.
 *     - Redirige al usuario a su panel principal (`dashboard.php`).
 * 7.  Manejo de Errores:
 *     - Si el usuario no se encuentra o la contraseña es incorrecta, guarda un mensaje
 *       de error en la sesión y redirige de vuelta al `index.php`.
 *     - Si ocurre una excepción PDO durante la consulta, registra el error y redirige
 *       con un mensaje genérico.
 */

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