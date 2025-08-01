/**
 * recetario_db.sql
 *
 * Este archivo SQL contiene el volcado completo de la base de datos `recetario_db`.
 * Se utiliza para la configuración inicial del sistema, creando las tablas necesarias,
 * insertando datos iniciales (como las categorías) y definiendo las relaciones
 * entre las tablas.
 *
 * --- ESTRUCTURA DE LA BASE DE DATOS ---
 *
 * 1.  Tabla `usuarios`:
 *     - Almacena la información de los usuarios registrados.
 *     - Campos: `id_usuario` (PK), `nombre_usuario` (único), `email` (único), `contrasena` (hasheada), `fecha_registro`.
 *
 * 2.  Tabla `categorias`:
 *     - Almacena las diferentes categorías a las que puede pertenecer una receta.
 *     - Campos: `id_categoria` (PK), `nombre_categoria` (único).
 *     - Contiene datos iniciales como "Desayunos", "Almuerzos", "Postres", etc.
 *
 * 3.  Tabla `recetas`:
 *     - Es la tabla principal, almacena los detalles de cada receta.
 *     - Campos: `id_receta` (PK), `id_usuario` (FK a `usuarios`), `nombre_receta`,
 *       `ingredientes`, `preparacion`, `tiempo_preparacion_minutos`, `fecha_creacion`,
 *       `imagen_url` (ruta a la imagen).
 *     - Tiene una clave foránea (`id_usuario`) que se relaciona con la tabla `usuarios`.
 *       La opción `ON DELETE CASCADE` asegura que si un usuario es eliminado, todas sus
 *       recetas también se eliminarán automáticamente.
 *
 * 4.  Tabla `recetas_categorias` (Tabla Pivot):
 *     - Gestiona la relación de muchos a muchos entre recetas y categorías. Una receta
 *       puede tener múltiples categorías y una categoría puede tener múltiples recetas.
 *     - Campos: `id_receta` (FK a `recetas`), `id_categoria` (FK a `categorias`).
 *     - La clave primaria es compuesta (`id_receta`, `id_categoria`) para asegurar que
 *       una receta solo pueda estar asociada a una categoría una vez.
 *     - También utiliza `ON DELETE CASCADE` para que las asociaciones se eliminen si
 *       se borra una receta o una categoría.
 *
 * --- ÍNDICES ---
 * Se definen claves primarias (PRIMARY KEY) para la identificación única de filas y
 * claves únicas (UNIQUE KEY) para campos como `nombre_usuario` y `email` para evitar
 * duplicados. También se crean índices en las claves foráneas para optimizar el
 * rendimiento de las consultas que involucran `JOINs`.
 *
 * --- AUTO_INCREMENT ---
 * Se configura `AUTO_INCREMENT` para las claves primarias, de modo que la base de datos
 * asigne automáticamente un nuevo ID a cada nuevo registro.
 */
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 30-07-2025 a las 21:54:41
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `recetario_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre_categoria` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre_categoria`) VALUES
(2, 'Almuerzos'),
(5, 'Bebidas'),
(3, 'Cenas'),
(1, 'Desayunos'),
(10, 'Ensaladas'),
(4, 'Postres'),
(8, 'Sin Gluten'),
(9, 'Sopas'),
(7, 'Vegano'),
(6, 'Vegetariano');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recetas`
--

CREATE TABLE `recetas` (
  `id_receta` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `nombre_receta` varchar(100) NOT NULL,
  `ingredientes` text NOT NULL,
  `preparacion` text NOT NULL,
  `tiempo_preparacion_minutos` int(11) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `imagen_url` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `recetas`
--

INSERT INTO `recetas` (`id_receta`, `id_usuario`, `nombre_receta`, `ingredientes`, `preparacion`, `tiempo_preparacion_minutos`, `fecha_creacion`, `imagen_url`) VALUES
(2, 1, 'Ensalada', 'tomates, lechuga, aguacates, AOVE, todo lo que quieras', 'Corta todo en trozos, riega con AOVE.', 5, '2025-07-22 11:06:26', 'img/receta_688a779edbeec.png'),
(4, 1, 'FLAN DE MASCARPONE EN AIRFRYER DE 1500 VATIOS', '100 gr mascarpone \r\n100 ml leche entera\r\n30 gr eritritol /azúcar \r\n2 huevos\r\n1 Cucharadita de sirope de agave/ vainilla/ otro aromatizante.', 'Mezcla bien todos los ingredientes, ponlo en un molde y mételo en la Airfyer.\r\n170º  25 minutos (en molde de aluminio)\r\n20 minutos (molde de cerámica)\r\nTiempos aproximados, cada Airfryer es un mundo', 10, '2025-07-22 12:36:06', ''),
(5, 1, 'ENSALADA DE CALABACÍN CON QUESO Y PIÑONES', 'CALABACÍN, QUESO AZUL O ROQUEFORT, PIÑONES, AOVE', 'Se parte un calabacín tierno en rodajas finitas (verde o blanco) y se colocan extendidas en un plato como se ve en la foto. Encima de ellas se coloca una cuña de queso azul o roquefort en pequeños trozos. Después le añadimos  por encima unos piñones que previamente hemos dorado en un poco de aceite, que también añadimos.', 20, '2025-07-22 12:39:48', 'img/receta_6889f86164c52.jpeg'),
(6, 1, 'ENSALADA DE PIMIENTO, TOMATE Y CEBOLLA ASADOS', 'PIMIENTO, TOMATE, CEBOLLA,', 'A', 25, '2025-07-22 12:42:40', 'img/receta_6889f89d58664.jpeg'),
(7, 1, 'BACALAO DORADO', 'BACALAO\r\nPATATAS FAJA\r\nCEBOLLA\r\nAOVE', 'MMMMMMMM MMMMMMM MMMMMMMM MMMM MMMMMMMMMMMM MMMMM MMMMM MMMMM MMMM MMMM MMMMMMMM', 25, '2025-07-22 12:46:54', ''),
(8, 1, 'TARTA DE QUESO DE MANGO', 'MMMMMMM\r\nMMMMMMM\r\nMMMMMM\r\nMMMMMM\r\nMMMMMM\r\nMMMMMMM\r\nMMMMMMM\r\nMMMMMMM', 'MMMMMMMMMM MMMMMMMM MMMMMMMMM MMMM MMMMMMM MMMMMMM MMMMMM MMMMMM MMMMMMMMM MMMMMM MMMMM MMMMMMMM MMMMMMMM MMMMMM MMMMMMMM MMMMMMM MMMMMMMMMMMM MMMMM NMMMMMMM MMMMMMM MMMMM MMMM MMMMM', 25, '2025-07-22 12:49:35', ''),
(9, 1, 'CHOTO AL AJILLO', 'CHOTO, AJO, AOVE, VINO BLANCO, PIMIENTOS ROJOS SECOS, SAL, PIMIENTA', 'Para medio choto se fríe una cabeza de ajo. Los dientes partidos por el centro se ponen a freír y cuando empiecen a dorarse se añade el choto hecho trocitos pequeños a fuego lento y se va removiendo de vez en cuando hasta que esté dorada la carne; entonces se le añade media botella de vino blanco y cuando empiece a hervir se vuelve a poner a fuego bajo. Salpimentar al gusto. Cuando la carne esté tierna se le añaden cuatro o cinco pimientos rojos secos (previamente lavados, pipas quitadas y dejados 5 minutos en remojo) para que hiervan junto a la carne unos 5 minutos.', 30, '2025-07-22 12:52:08', 'img/receta_6889f657cbd4e.jpeg'),
(10, 1, 'CARRILLADA DE TERNERA', 'CARILLADA DE TERNERA, ZANAHORIA, PUERRO, PIMIENTO ROJO, AJO, VINO, SAL, AOVE', 'Un kilo de carrillada de ternera hecha rodajas. Se enharina y se fríe poquito, simplemente que se sellen ambas caras y se aparta a una fuente. A continuación se parte en trocitos una zanahorias, 1 puerro, un pimiento rojo y una cabeza de ajo pelada (puede ser entera) porque todo esto se sofríe en el mismo aceite de haber sellado la carne. Una vez está el sofrito hecho, se le añade la carne, se le da unas vueltas durante un par de minutos y se le echa media botella de vino tinto o blanco y el agua suficiente hasta cubrir la carne (ahí se añade una pastilla de avecrem o se salpimentarlos al gusto) y se deja hirviendo a fuego suave, y cuando está tierna se saca la carrillada. La verdura o se deja como quede o se tritura y se echa encima de la carne. Se sirve con patatas fritas, patatas al horno o ensalada.', 60, '2025-07-28 11:45:03', 'img/receta_688a7063eafcd.png'),
(11, 2, 'patatas fritas con huevo', 'patata\r\nhuevo\r\nsal', 'hola', 20, '2025-07-28 11:52:56', 'img/receta_6887487817d6b2.37543678-flor.png'),
(12, 1, 'ESPAGUETTI A LA CARBONARA', '500 gr ESPAGUETTI, 200 gr PANCETA, 100 gr PARMESANO, 4 HUEVOS, SAL, PIMIENTA', 'Mientras preparas la pasta, pon en una sartén la panceta para que se vaya haciendo. En un plato hondo pon los huevos con un poco de sal, pimienta y bate con un tenedor; cuando esté bien mezclado échale parte del parmesano y sigue dándole con el tenedor. Cuando la pasta esté lista, ponla encima de la sartén en la que has preparado la panceta y vuelca encima el plato en el que estuviste batiendo los huevos; revuelve todo unos segundos y sirve.', 20, '2025-07-30 10:59:44', 'img/receta_688a677a966c0.png'),
(13, 1, 'PAN DE TRIGO SARRACENO AL HORNO (versión sin gluten, sin levadura)', '360 gramos de granos de trigo sarraceno\r\n360 ml de agua\r\n1 cucharadita de sal', 'Paso 1: Remojar los granos\r\nRemojo rápido: Coloca los granos de trigo sarraceno en un recipiente amplio y cúbrelos con agua caliente (50–60°C). Déjalos en remojo durante 2–3 horas.\r\nEnjuagar y escurrir: Pasado ese tiempo, enjuágalos con agua fría y escúrrelos bien.\r\nPaso 2: Preparar la masa\r\nProcesar: Coloca los granos escurridos en una licuadora o procesador de alimentos, añade los 360 ml de agua y la sal. Tritura hasta obtener una masa espesa y homogénea.\r\nReposo opcional para fermentar: Si deseas una miga más desarrollada y un sabor ligeramente ácido, puedes dejar reposar la mezcla tapada durante 12–24 horas a temperatura ambiente. (Merece la pena este paso)\r\nPaso 3: Preparar los moldes\r\nEngrasar los moldes: Prepara 4 moldes de cristal aptos para horno de aproximadamente 12 × 8 cm cada uno. Engrásalos con una mezcla de mantequilla, aceite y harina para evitar que el pan se pegue.\r\nDividir la masa: Reparte la masa equitativamente entre los 4 moldes.\r\nPaso 4: Hornear\r\nPrecalentar el horno: Precalienta el horno a 200°C con la función de calor superior e inferior + calor circular (símbolo: 🔲 con dos líneas, una arriba y otra abajo, y un ventilador en el centro).\r\nHornear: Coloca los moldes en la bandeja del medio del horno y hornea durante 1 hora a 200°C, sin necesidad de sacar el pan del molde ni darle la vuelta.\r\nPaso 5: Enfriar\r\nDejar enfriar: Una vez transcurrida la hora, retira los moldes del horno. Espera unos minutos, desmóldalos con cuidado y deja que los panes se enfríen completamente sobre una rejilla antes de cortarlos.\r\nNotas adicionales:\r\nRemojo eficiente: El uso de agua caliente permite reducir el tiempo de remojo de 8 horas a solo 2–3 horas.\r\nTextura y formato: Hacer el pan en moldes pequeños garantiza una cocción uniforme y un resultado compacto, ideal para rebanar.\r\nHorneado sin manipulación: Esta función de horno favorece una cocción homogénea en todo el pan sin necesidad de intervenir durante el horneado.\r\nCon esta versión al horno, obtendrás cuatro panes pequeños de trigo sarraceno, con corteza crujiente y miga densa. Perfectos para congelar, tostar o comer directamente.', 20, '2025-07-30 11:02:39', 'img/receta_688a701aec602.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recetas_categorias`
--

CREATE TABLE `recetas_categorias` (
  `id_receta` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `recetas_categorias`
--

INSERT INTO `recetas_categorias` (`id_receta`, `id_categoria`) VALUES
(2, 10),
(4, 4),
(5, 10),
(6, 10),
(9, 2),
(10, 2),
(11, 2),
(12, 2),
(13, 1),
(13, 6),
(13, 7),
(13, 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre_usuario`, `email`, `contrasena`, `fecha_registro`) VALUES
(1, 'a', 'a@gmail.com', '$2y$10$cujDpC7Gk70etPNPcQ73I.pJ0OgNbQYhsw1EZL6GftHwvD0n1kJti', '2025-07-22 10:21:12'),
(2, 'b', 'b@gmail.com', '$2y$10$0lxhOto4pV/ojJ1RtgTvNu/Yu7L4AkcExCojkuFxFoB806lbfXz6m', '2025-07-22 10:34:09'),
(3, 'c', 'c@gmail.com', '$2y$10$Wfxn6P3rOQ.EAouidxvF5.gsBf5ZOAx0BfWdSz8pm/qpqgZLmp5.q', '2025-07-22 10:34:55');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `nombre_categoria` (`nombre_categoria`);

--
-- Indices de la tabla `recetas`
--
ALTER TABLE `recetas`
  ADD PRIMARY KEY (`id_receta`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `recetas_categorias`
--
ALTER TABLE `recetas_categorias`
  ADD PRIMARY KEY (`id_receta`,`id_categoria`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `nombre_usuario` (`nombre_usuario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `recetas`
--
ALTER TABLE `recetas`
  MODIFY `id_receta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `recetas`
--
ALTER TABLE `recetas`
  ADD CONSTRAINT `recetas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `recetas_categorias`
--
ALTER TABLE `recetas_categorias`
  ADD CONSTRAINT `recetas_categorias_ibfk_1` FOREIGN KEY (`id_receta`) REFERENCES `recetas` (`id_receta`) ON DELETE CASCADE,
  ADD CONSTRAINT `recetas_categorias_ibfk_2` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
