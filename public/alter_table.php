<?php
// public/alter_table.php

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