<?php
/**
 * test_db.php
 *
 * Este es un script de diagnóstico simple utilizado durante el desarrollo para
 * verificar si la conexión a la base de datos se establece correctamente.
 *
 * IMPORTANTE: Este archivo debe ser eliminado del servidor en un entorno de
 * producción, ya que puede exponer información sobre la configuración y el
 * estado de la conexión.
 *
 * Lógica principal:
 * 1.  Inclusión de Conexión: Incluye el archivo `db_connect.php`.
 * 2.  Mensaje de Éxito: Si la inclusión de `db_connect.php` no produce un error
 *     fatal (lo que significaría que la conexión falló y el script se detendría),
 *     este script simplemente imprimirá un mensaje de "¡Conexión exitosa!".
 *     Esto confirma que el objeto PDO se instanció correctamente.
 */

include '../includes/db_connect.php'; // Incluye el archivo de conexión
echo "¡Conexión exitosa al recetario!";
?>