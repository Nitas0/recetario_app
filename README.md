# Mi Recetario Online 🍳

Una aplicación web para gestionar recetas de cocina personalizadas, desarrollada en PHP con MySQL. Permite a los usuarios crear, editar, organizar y compartir sus recetas favoritas de forma fácil e intuitiva.

## 🌟 Características Principales

- **Gestión completa de recetas**: Crear, editar, visualizar y eliminar recetas
- **Sistema de usuarios**: Registro, login y sesiones seguras
- **Categorización**: Organiza recetas por categorías (Desayunos, Almuerzos, Postres, etc.)
- **Subida de imágenes**: Añade fotos a tus recetas
- **Búsqueda y filtrado**: Encuentra recetas por nombre, ingredientes, preparación o categoría
- **Exploración social**: Descubre recetas de otros usuarios
- **Interfaz responsive**: Funciona perfectamente en dispositivos móviles y escritorio
- **Arrastrar y soltar**: Reordena tus recetas fácilmente

## 🛠️ Tecnologías Utilizadas

- **Backend**: PHP 8+ con PDO
- **Base de datos**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript
- **Librerías**:
  - SortableJS (para funcionalidad drag & drop)
  - Google Fonts (Poppins, Pacifico)
- **Seguridad**: 
  - Contraseñas hasheadas con `password_hash()`
  - Consultas preparadas (PDO)
  - Validación de archivos subidos

## 📋 Requisitos del Sistema

- PHP 8.0 o superior
- MySQL 5.7+ o MariaDB 10.2+
- Servidor web (Apache, Nginx)
- Extensiones PHP requeridas:
  - PDO
  - PDO_MySQL
  - GD (para manejo de imágenes)

## 🚀 Instalación

### 1. Clonar el repositorio
```bash
git clone [URL_DEL_REPOSITORIO]
cd mi-recetario-online
```

### 2. Configurar la base de datos

1. Crear una base de datos MySQL llamada `recetario_db`
2. Importar el archivo SQL de estructura:
```bash
mysql -u tu_usuario -p recetario_db < includes/recetario_db.sql
```

### 3. Configurar la conexión a la base de datos

Editar el archivo `includes/db_connect.php` con tus credenciales:

```php
// Configuración para el entorno local
$host = 'localhost';
$db   = 'recetario_db';
$user = 'tu_usuario';
$pass = 'tu_contraseña';
```

### 4. Configurar permisos

Asegurar que el directorio `public/img/` tenga permisos de escritura:
```bash
chmod 755 public/img/
```

### 5. Configurar servidor web

**Apache (.htaccess ya incluido)**
- Asegurar que `mod_rewrite` esté habilitado
- El DocumentRoot debe apuntar a la carpeta `public/`

**Nginx**
```nginx
server {
    listen 80;
    server_name tu-dominio.com;
    root /ruta/al/proyecto/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 📁 Estructura del Proyecto

```
recetario_app/
├── .git/                           # Repositorio de Git
├── favicon/                        # Iconos de la aplicación
│   ├── apple-touch-icon.png
│   ├── favicon-96x96.png
│   ├── favicon.ico
│   ├── favicon.svg
│   ├── site.webmanifest
│   ├── web-app-manifest-192x192.png
│   └── web-app-manifest-512x512.png
├── includes/                       # Archivos de configuración y backend
│   ├── db_connect.php              # Conexión a la base de datos
│   ├── footer.php                  # Pie de página HTML común
│   ├── header.php                  # Cabecera HTML común

│   └── recetario_db.sql            # Script principal de la base de datos
├── public/                         # Directorio público (DocumentRoot)
│   ├── css/
│   │   └── style.css               # Hoja de estilos principal
│   ├── js/
│   │   └── main.js                 # Scripts de JavaScript
│   ├── img/                        # Imágenes de assets y recetas
│   ├── uploads/                    # Directorio para subida de archivos
│   ├── add_recipe.php              # Formulario para añadir receta
│   ├── alter_table.php             # Script para modificar la BD (desarrollo)
│   ├── dashboard.php               # Panel principal del usuario
│   ├── delete_recipe.php           # Lógica para eliminar receta
│   ├── edit_recipe.php             # Formulario para editar receta
│   ├── explore_recipes.php         # Explorar recetas de otros usuarios
│   ├── index.php                   # Página de inicio/login
│   ├── login_process.php           # Lógica de login
│   ├── logout.php                  # Cierre de sesión
│   ├── register.php                # Página de registro
│   ├── register_process.php        # Lógica de registro
│   ├── save_recipe_order.php       # Guarda el orden de las recetas
│   ├── test_db.php                 # Script de prueba de conexión a BD
│   ├── view_recipe.php             # Vista detallada de una receta
│   ├── dashboard_error.log         # Log de errores del panel
│   └── debug.log                   # Log de depuración general
└── README.md                       # Este archivo
```

## 🎯 Funcionalidades Detalladas

### Sistema de Usuarios
- Registro con validación de email único
- Login seguro con sesiones PHP
- Contraseñas hasheadas con algoritmo bcrypt

### Gestión de Recetas
- **Crear**: Formulario completo con nombre, ingredientes, preparación, tiempo e imagen
- **Editar**: Modificar todos los campos incluyendo categorías
- **Eliminar**: Con confirmación modal para evitar borrados accidentales
- **Visualizar**: Vista detallada con formato legible

### Categorías
Las recetas se pueden clasificar en:
- Desayunos
- Almuerzos  
- Cenas
- Postres
- Bebidas
- Ensaladas
- Sopas
- Vegetariano
- Vegano
- Sin Gluten

### Búsqueda y Filtrado
- Búsqueda por texto en nombre, ingredientes y preparación
- Filtrado por categoría
- Combinación de filtros
- Resultados en tiempo real

### Subida de Imágenes
- Formatos soportados: JPG, PNG, GIF
- Tamaño máximo: 5MB
- Nombres únicos para evitar conflictos
- Imagen por defecto si no se proporciona

## 🔧 Configuración Avanzada

### Configuración de Producción

Para usar en producción, editar `includes/db_connect.php`:

```php
// Configuración para el servidor de producción
$host = 'tu_servidor_db';
$db   = 'nombre_bd_produccion';
$user = 'usuario_produccion';
$pass = 'contraseña_segura';
```

### Variables de Entorno

Para mayor seguridad, considera usar variables de entorno:

```php
$host = $_ENV['DB_HOST'] ?? 'localhost';
$db   = $_ENV['DB_NAME'] ?? 'recetario_db';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';
```

## 🐛 Solución de Problemas Comunes

### Error de conexión a la base de datos
- Verificar credenciales en `db_connect.php`
- Asegurar que MySQL esté corriendo
- Comprobar que la base de datos existe

### Las imágenes no se suben
- Verificar permisos del directorio `public/img/`
- Comprobar límites de subida en `php.ini`:
  ```ini
  upload_max_filesize = 5M
  post_max_size = 6M
  ```

### Problemas de sesión
- Verificar que las cookies estén habilitadas
- Comprobar configuración de sesiones en `php.ini`

## 🔒 Consideraciones de Seguridad

- **Contraseñas**: Hasheadas con `password_hash()`
- **SQL Injection**: Prevenido con consultas preparadas (PDO)
- **XSS**: Datos escapados con `htmlspecialchars()`
- **CSRF**: Validación de sesiones en operaciones críticas
- **Validación de archivos**: Tipo y tamaño verificados antes de subir


## 📝 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 📧 Contacto

Si tienes preguntas o sugerencias, no dudes en abrir un issue en el repositorio.

---

**¡Disfruta cocinando con Mi Recetario Online! 👨‍🍳👩‍🍳**