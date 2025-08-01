<?php
// includes/header.php
// Inicia la sesión si no está ya iniciada, para poder acceder a las variables de sesión.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Verifica si el usuario ha iniciado sesión para mostrar la navegación correcta.
$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'Mi Recetario') ?></title>
    <link rel="apple-touch-icon" sizes="180x180" href="../favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="96x96" href="../favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../favicon/favicon-16x16.png">
    <link rel="manifest" href="../favicon/site.webmanifest">
    <link rel="mask-icon" href="../favicon/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Pacifico&display=swap" rel="stylesheet">
</head>
<body>
    <header class="main-header">
        <div class="header-container">
            <a href="index.php" class="logo">
                <img src="img/flor-de-cerezo.png" alt="Logo Recetario" class="logo-img">
                <span class="logo-text">Mi Recetario</span>
            </a>
            <nav class="main-nav">
                <ul>
                    <?php if ($is_logged_in): ?>
                        <li><a href="dashboard.php">Mis Recetas</a></li>
                        <li><a href="explore_recipes.php">Explorar</a></li>
                        <li><a href="add_recipe.php">+ Añadir Receta</a></li>
                        <li><a href="logout.php">Cerrar Sesión</a></li>
                    <?php else: ?>
                        <li><a href="index.php">Login</a></li>
                        <li><a href="register.php">Registrarse</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="main-content">
