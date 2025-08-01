<?php
/**
 * db_connect.php
 *
 * Este archivo se encarga de establecer la conexión con la base de datos MySQL
 * utilizando la extensión PDO (PHP Data Objects).
 *
 * Lógica principal:
 * 1. Detección del entorno: Comprueba si el servidor es local ('localhost' o '127.0.0.1')
 *    o de producción para cargar las credenciales correspondientes.
 * 2. Configuración de credenciales: Define las variables $host, $db, $user y $pass
 *    según el entorno detectado.
 * 3. Definición del DSN (Data Source Name): Construye la cadena de conexión para PDO.
 * 4. Opciones de PDO: Configura atributos para el manejo de errores, el modo de obtención
 *    de resultados (fetch mode) y la desactivación de la emulación de consultas preparadas
 *    para mayor seguridad.
 * 5. Intento de conexión: Utiliza un bloque try-catch para instanciar un nuevo objeto PDO.
 * 6. Manejo de errores: Si la conexión falla, captura la PDOException, registra el error
 *    en el log del servidor y muestra un mensaje genérico al usuario para no exponer
 *    detalles sensibles de la conexión.
 */

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