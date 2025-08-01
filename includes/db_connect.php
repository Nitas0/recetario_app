<?php
// db_connect.php
// Archivo para establecer la conexión a la base de datos usando PDO

// Detectar el entorno (local o producción)
if ($_SERVER['HTTP_HOST'] == 'localhost' || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) {
    // Configuración para el entorno local
    $host = 'localhost';
    $db   = 'recetario_db';
    $user = 'root';
    $pass = '';
} else {
    // Configuración para el servidor de producción
    // POR FAVOR, REEMPLACE CON SUS CREDENCIALES DE PRODUCCIÓN
    $host = 'db5018319902.hosting-data.io';
    $db   = 'dbs14514256';
    $user = 'dbu1206956';
    $pass = 'vvBrNFz3s@vu9q5AIT';
}

$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanza excepciones en caso de errores.
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Devuelve las filas como un array asociativo.
    PDO::ATTR_EMULATE_PREPARES   => false, // Desactiva la emulación de preparaciones para mayor seguridad.
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // En un entorno de producción, es mejor registrar el error que mostrarlo.
    error_log("Error de conexión a la base de datos: " . $e->getMessage());
    // Muestra un mensaje genérico al usuario.
    die("Lo sentimos, estamos experimentando problemas técnicos. Por favor, inténtelo de nuevo más tarde.");
}
?>