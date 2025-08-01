<?php
/**
 * alter_table.php
 *
 * Este es un script de mantenimiento de uso único, diseñado para modificar la
 * estructura de la tabla `recetas` en la base de datos. Su propósito específico
 * es añadir la columna `imagen_url` para almacenar la ruta de las imágenes
 * asociadas a cada receta.
 *
 * IMPORTANTE: Este archivo debe ser eliminado del servidor inmediatamente después
 * de su ejecución para evitar riesgos de seguridad.
 *
 * Lógica principal:
 * 1.  Inclusión de Conexión: Incluye `db_connect.php` para establecer la conexión
 *     con la base de datos.
 * 2.  Ejecución de Consulta SQL:
 *     a. Define una consulta `ALTER TABLE` para añadir la columna `imagen_url` de tipo
 *        VARCHAR(255), permitiendo valores nulos (NULL).
 *     b. Ejecuta la consulta usando `$pdo->exec()`.
 * 3.  Manejo de Errores (try-catch):
 *     a. Si la consulta se ejecuta con éxito, muestra un mensaje de confirmación.
 *     b. Si se produce una `PDOException`, comprueba el código de error:
 *        - Si el código es '42S21', significa que la columna ya existe. En este caso,
 *          informa al usuario que no se realizaron cambios.
 *        - Para cualquier otro error, muestra un mensaje de error genérico y registra
 *          el error real en el log del servidor para depuración.
 * 4.  Mensaje de Seguridad: Al final, muestra un mensaje destacado recordando al
 *     administrador que elimine el archivo por razones de seguridad.
 */

// --- SCRIPT DE USO ÚNICO PARA MODIFICAR LA BASE DE DATOS ---

// Incluir la conexión a la base de datos
require_once '../includes/db_connect.php';

echo "<p>Iniciando script de actualización de la base de datos...</p>";

try {
    // Definir la consulta SQL para añadir la nueva columna
    // Se añade `imagen_url` para guardar la ruta de la imagen de la receta.
    // Se coloca después de `preparacion` para mantener un orden lógico.
    $sql = "ALTER TABLE recetas ADD COLUMN imagen_url VARCHAR(255) NULL DEFAULT NULL AFTER preparacion";

    // Ejecutar la consulta
    $pdo->exec($sql);

    echo "<p style='color: green;'>¡Éxito! La tabla 'recetas' ha sido actualizada correctamente con la columna 'imagen_url'.</p>";

} catch (PDOException $e) {
    // Manejar errores específicos de la base de datos
    // El código de error '42S21' corresponde a "Column already exists"
    if ($e->getCode() == '42S21') {
        echo "<p style='color: orange;'>La columna 'imagen_url' ya existe en la tabla 'recetas'. No se realizaron cambios.</p>";
    } else {
        // Para cualquier otro error, muestra un mensaje genérico y registra el detalle
        echo "<p style='color: red;'>Error al actualizar la tabla 'recetas'.</p>";
        // Es una buena práctica registrar el error real en un log del servidor en un entorno de producción
        error_log("Error en alter_table.php: " . $e->getMessage());
    }
}

echo "<p>Script finalizado. <strong>Por seguridad, elimina este archivo ('alter_table.php') del servidor de inmediato.</strong></p>";

?>