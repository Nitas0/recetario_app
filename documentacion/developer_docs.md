# Documentación para Desarrolladores: Mi Recetario Online

Este documento proporciona una guía técnica para desarrolladores que deseen entender, configurar y contribuir al proyecto "Mi Recetario Online".

## 1. Visión General del Proyecto

"Mi Recetario Online" es una aplicación web desarrollada en PHP con MySQL, diseñada para permitir a los usuarios gestionar, organizar y compartir sus recetas de cocina. Ofrece funcionalidades completas para la creación, edición, visualización y eliminación de recetas, un sistema de usuarios robusto, categorización, subida de imágenes, búsqueda avanzada y una interfaz responsive.

**Características Principales:**
- Gestión CRUD completa de recetas.
- Sistema de autenticación y autorización de usuarios.
- Categorización de recetas.
- Subida y gestión de imágenes de recetas.
- Funcionalidades de búsqueda y filtrado.
- Interfaz de usuario adaptable (responsive).
- Funcionalidad de arrastrar y soltar para reordenar recetas.

**Tecnologías Clave:**
- **Backend:** PHP 8+ con PDO para la interacción con la base de datos.
- **Base de Datos:** MySQL/MariaDB.
- **Frontend:** HTML5, CSS3, JavaScript.
- **Librerías:** SortableJS (para drag & drop), Google Fonts.
- **Seguridad:** Contraseñas hasheadas (`password_hash()`), consultas preparadas (PDO), validación de archivos subidos.

## 2. Configuración del Entorno Local

Para poner en marcha el proyecto en tu entorno de desarrollo, sigue estos pasos:

### 2.1. Requisitos del Sistema
- PHP 8.0 o superior.
- MySQL 5.7+ o MariaDB 10.2+.
- Servidor web (Apache, Nginx).
- Extensiones PHP requeridas: `PDO`, `PDO_MySQL`, `GD`.

### 2.2. Clonar el Repositorio
```bash
git clone [URL_DEL_REPOSITORIO] # Reemplaza con la URL real del repositorio
cd recetario_app
```

### 2.3. Configuración de la Base de Datos
1.  Crea una base de datos MySQL/MariaDB llamada `recetario_db`.
2.  Importa el esquema de la base de datos y los datos iniciales utilizando el archivo `includes/recetario_db.sql`:
    ```bash
    mysql -u tu_usuario -p recetario_db < includes/recetario_db.sql
    ```
    (Asegúrate de reemplazar `tu_usuario` con tu usuario de MySQL y se te pedirá la contraseña).

### 2.4. Configuración de la Conexión a la Base de Datos
Edita el archivo `includes/db_connect.php` con tus credenciales de la base de datos local:

```php
<?php
// Configuración para el entorno local
$host = 'localhost';
$db   = 'recetario_db';
$user = 'tu_usuario'; // Tu usuario de MySQL
$pass = 'tu_contraseña'; // Tu contraseña de MySQL

// ... resto del código de conexión ...
?>
```

### 2.5. Configurar Permisos de Escritura
Asegúrate de que el directorio `public/img/` tenga permisos de escritura para que la aplicación pueda subir imágenes:
```bash
chmod 755 public/img/
```

### 2.6. Configuración del Servidor Web

**Apache:**
- Asegúrate de que `mod_rewrite` esté habilitado.
- El `DocumentRoot` de tu Virtual Host debe apuntar a la carpeta `public/` del proyecto.

**Nginx:**
Configura un bloque `server` similar a este (ajusta las rutas y `fastcgi_pass` según tu configuración de PHP-FPM):
```nginx
server {
    listen 80;
    server_name tu-dominio.com; # O localhost
    root /ruta/al/proyecto/public; # Ruta absoluta a la carpeta public
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock; # Ajusta a tu socket PHP-FPM
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 3. Estructura del Proyecto

El proyecto sigue una estructura modular y organizada:

```
recetario_app/
├── .git/                           # Repositorio de Git
├── documentacion/                  # Documentación del proyecto (incluyendo este archivo)
│   └── developer_docs.md
│   └── manual.pdf
├── favicon/                        # Iconos de la aplicación para diferentes dispositivos
├── includes/                       # Archivos de configuración y lógica de backend compartida
│   ├── db_connect.php              # Script de conexión a la base de datos.
│   ├── footer.php                  # Pie de página HTML común para las vistas.
│   ├── header.php                  # Cabecera HTML común para las vistas.
│   └── recetario_db.sql            # Script SQL para la creación y poblamiento de la base de datos.
└── public/                         # Directorio público (DocumentRoot del servidor web)
    ├── css/                        # Hojas de estilo CSS.
    │   └── style.css               # Estilos principales de la aplicación.
    ├── js/                         # Archivos JavaScript.
    │   └── main.js                 # Scripts JavaScript principales.
    ├── img/                        # Imágenes de assets y directorio para imágenes de recetas subidas.
    ├── uploads/                    # Directorio específico para imágenes de recetas subidas por usuarios.
    ├── add_recipe.php              # Lógica y vista para añadir nuevas recetas.
    ├── alter_table.php             # Script de desarrollo para modificaciones de la BD (usar con precaución).
    ├── dashboard.php               # Panel de control principal del usuario.
    ├── delete_recipe.php           # Lógica para eliminar recetas.
    ├── edit_recipe.php             # Lógica y vista para editar recetas existentes.
    ├── explore_recipes.php         # Vista para explorar recetas de otros usuarios.
    ├── index.php                   # Página de inicio y/o login.
    ├── login_process.php           # Lógica de procesamiento del login.
    ├── logout.php                  # Lógica para cerrar la sesión del usuario.
    ├── register.php                # Vista para el registro de nuevos usuarios.
    ├── register_process.php        # Lógica de procesamiento del registro de usuarios.
    ├── save_recipe_order.php       # Lógica para guardar el orden de las recetas (funcionalidad drag & drop).
    ├── test_db.php                 # Script simple para probar la conexión a la base de datos.
    ├── view_recipe.php             # Vista detallada de una receta individual.
    ├── dashboard_error.log         # Log de errores específicos del dashboard.
    └── debug.log                   # Log de depuración general.
```

## 4. Funcionalidades Clave y Flujos

### 4.1. Sistema de Usuarios
-   **Registro:** `register.php` (vista) y `register_process.php` (lógica). Las contraseñas se hashean usando `password_hash()` antes de ser almacenadas.
-   **Login:** `index.php` (vista, o `login.php`) y `login_process.php` (lógica). Utiliza sesiones PHP para la gestión de la autenticación.
-   **Logout:** `logout.php` destruye la sesión del usuario.

### 4.2. Gestión de Recetas (CRUD)
-   **Añadir Receta:** `add_recipe.php` maneja el formulario y la inserción de nuevas recetas en la base de datos, incluyendo la subida de imágenes a `public/uploads/`.
-   **Ver Receta:** `view_recipe.php` muestra los detalles completos de una receta específica.
-   **Editar Receta:** `edit_recipe.php` permite modificar los datos de una receta existente. Se precargan los datos actuales en el formulario.
-   **Eliminar Receta:** `delete_recipe.php` procesa la eliminación de una receta, incluyendo la eliminación de su imagen asociada y las relaciones en `recetas_categorias` (debido a `ON DELETE CASCADE`).
-   **Dashboard:** `dashboard.php` es el panel principal donde los usuarios ven y gestionan sus propias recetas.
-   **Explorar Recetas:** `explore_recipes.php` permite a los usuarios ver recetas de otros usuarios (si la funcionalidad está completamente implementada para mostrar recetas públicas).

### 4.3. Subida de Imágenes
-   Las imágenes se suben al directorio `public/img/` (o `public/uploads/` según la configuración específica del código). Se generan nombres únicos para evitar conflictos.
-   Se realiza validación de tipo y tamaño de archivo para seguridad.

### 4.4. Categorización
-   Las recetas pueden asociarse a múltiples categorías a través de la tabla `recetas_categorias` (tabla pivot).
-   Las categorías predefinidas se insertan inicialmente en la tabla `categorias` mediante `recetario_db.sql`.

### 4.5. Reordenamiento de Recetas
-   La funcionalidad de arrastrar y soltar en el dashboard utiliza SortableJS y la lógica en `save_recipe_order.php` para actualizar el orden de las recetas en la base de datos.

## 5. Esquema de la Base de Datos (Alto Nivel)

La base de datos `recetario_db` consta de las siguientes tablas principales:

-   **`usuarios`**: Almacena la información de los usuarios.
    -   `id_usuario` (PK, AUTO_INCREMENT)
    -   `nombre_usuario` (UNIQUE)
    -   `email` (UNIQUE)
    -   `contrasena` (VARCHAR, hasheada)
    -   `fecha_registro`

-   **`categorias`**: Contiene las categorías predefinidas para las recetas.
    -   `id_categoria` (PK, AUTO_INCREMENT)
    -   `nombre_categoria` (UNIQUE)

-   **`recetas`**: Almacena los detalles de cada receta.
    -   `id_receta` (PK, AUTO_INCREMENT)
    -   `id_usuario` (FK a `usuarios`, ON DELETE CASCADE)
    -   `nombre_receta`
    -   `ingredientes`
    -   `preparacion`
    -   `tiempo_preparacion_minutos`
    -   `fecha_creacion`
    -   `imagen_url` (ruta a la imagen de la receta)

-   **`recetas_categorias`**: Tabla pivot para la relación muchos a muchos entre `recetas` y `categorias`.
    -   `id_receta` (FK a `recetas`, ON DELETE CASCADE)
    -   `id_categoria` (FK a `categorias`, ON DELETE CASCADE)
    -   (PK compuesta: `id_receta`, `id_categoria`)

## 6. Convenciones de Codificación y Seguridad

-   **PHP:**
    -   Uso de PDO para todas las interacciones con la base de datos, previniendo inyecciones SQL mediante consultas preparadas.
    -   Las contraseñas de usuario se almacenan hasheadas con `password_hash()` (algoritmo bcrypt por defecto).
    -   Se utiliza `htmlspecialchars()` para escapar la salida de datos en HTML, mitigando ataques XSS.
    -   Gestión de sesiones PHP para la autenticación de usuarios.
    -   Validación de entradas de usuario y archivos subidos.

-   **Estructura de Archivos:**
    -   Separación de lógica de negocio y presentación (aunque en PHP tradicional, a menudo se mezclan en los archivos `.php` de `public/`).
    -   Archivos comunes (`header.php`, `footer.php`) para mantener la consistencia en la interfaz.

-   **Base de Datos:**
    -   Uso de claves foráneas con `ON DELETE CASCADE` para mantener la integridad referencial y simplificar la eliminación de datos relacionados.
    -   Índices en campos clave para optimizar el rendimiento de las consultas.

## 7. Solución de Problemas Comunes (Desarrollo)

-   **Error de conexión a la base de datos:**
    -   Verifica las credenciales en `includes/db_connect.php`.
    -   Asegúrate de que tu servidor MySQL/MariaDB esté en ejecución.
    -   Confirma que la base de datos `recetario_db` existe y ha sido importada correctamente.

-   **Las imágenes no se suben:**
    -   Verifica los permisos de escritura del directorio `public/img/` (o `public/uploads/`).
    -   Comprueba la configuración de `upload_max_filesize` y `post_max_size` en tu `php.ini`.

-   **Problemas de sesión:**
    -   Asegúrate de que las cookies estén habilitadas en tu navegador.
    -   Revisa la configuración de sesiones en `php.ini`.

## 8. Contribución

Para contribuir al proyecto, por favor, sigue las convenciones de codificación existentes y crea ramas separadas para nuevas funcionalidades o correcciones de errores. Envía Pull Requests para su revisión.

---
*Fin de la Documentación para Desarrolladores*