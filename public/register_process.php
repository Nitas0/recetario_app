<?php
/**
 * register_process.php
 *
 * Este script maneja la lógica de registro de un nuevo usuario. No tiene interfaz
 * de usuario y se encarga de validar los datos recibidos del formulario de registro,
 * comprobar si el usuario ya existe y, si todo es correcto, crearlo en la base de datos.
 *
 * Lógica principal:
 * 1.  Inicio de Sesión y Conexión a BD: Inicia la sesión y establece la conexión
 *     con la base de datos.
 * 2.  Verificación del Método: Asegura que la solicitud sea de tipo POST.
 * 3.  Recogida de Datos: Obtiene el nombre de usuario, email y contraseñas del `$_POST`.
 * 4.  Validación del Lado del Servidor:
 *     - Comprueba que todos los campos obligatorios estén completos.
 *     - Valida que el email tenga un formato correcto usando `filter_var`.
 *     - Verifica que la contraseña tenga la longitud mínima requerida (8 caracteres).
 *     - Confirma que las dos contraseñas introducidas coincidan.
 *     - Si hay algún error de validación, se almacena en un array y se redirige
 *       de vuelta al formulario de registro para mostrar los errores.
 * 5.  Verificación de Duplicados en la Base de Datos:
 *     - Realiza una consulta para contar cuántos usuarios existen con el mismo nombre de usuario.
 *     - Realiza otra consulta para contar cuántos usuarios existen con el mismo email.
 *     - Si se encuentra algún duplicado, se añade el error correspondiente y se redirige.
 * 6.  Creación del Usuario:
 *     - Si todas las validaciones son correctas, hashea la contraseña con `password_hash()`
 *       y `PASSWORD_DEFAULT`. Esto es crucial para la seguridad, nunca se deben guardar
 *       contraseñas en texto plano.
 *     - Prepara una consulta `INSERT` para añadir el nuevo usuario a la tabla `usuarios`.
 *     - Ejecuta la consulta con los datos validados y la contraseña hasheada.
 * 7.  Redirección Final:
 *     - Si el usuario se crea con éxito, guarda un mensaje de éxito en la sesión y
 *       redirige al usuario a la página de login (`index.php`) para que pueda iniciar sesión.
 * 8.  Manejo de Errores: Si ocurre una excepción PDO, se registra el error y se redirige
 *     al formulario de registro con un mensaje de error genérico.
 */

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