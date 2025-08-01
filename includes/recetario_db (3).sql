-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 30-07-2025 a las 08:09:27
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.3.14

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

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE IF NOT EXISTS `categorias` (
  `id_categoria` int NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_categoria`),
  UNIQUE KEY `nombre_categoria` (`nombre_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

DROP TABLE IF EXISTS `recetas`;
CREATE TABLE IF NOT EXISTS `recetas` (
  `id_receta` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `nombre_receta` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ingredientes` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `preparacion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tiempo_preparacion_minutos` int DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `imagen_url` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_receta`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `recetas`
--

INSERT INTO `recetas` (`id_receta`, `id_usuario`, `nombre_receta`, `ingredientes`, `preparacion`, `tiempo_preparacion_minutos`, `fecha_creacion`, `imagen_url`) VALUES
(2, 1, 'Ensalada', 'tomates, lechuga, aguacates, AOVE, todo lo que quieras', 'Corta todo en trozos, riega con AOVE.', 5, '2025-07-22 11:06:26', ''),
(4, 1, 'FLAN', 'LECHE\r\nERITRITOL\r\nHUEVOS\r\nPATATA', 'MEZCLA TODOS LOS INGREDIENTES EN UN MOLDE Y MÉTELO EN LA AIRFRYER A 180º 20 MINUTOS.', 5, '2025-07-22 12:36:06', ''),
(5, 1, 'ENSALADA DE CALABACÍN CON QUESO Y PIÑONES', 'CALABACÍN, QUESO AZUL O ROQUEFORT, PIÑONES, AOVE', 'Se parte un calabacín tierno en rodajas finitas (verde o blanco) y se colocan extendidas en un plato como se ve en la foto. Encima de ellas se coloca una cuña de queso azul o roquefort en pequeños trozos. Después le añadimos  por encima unos piñones que previamente hemos dorado en un poco de aceite, que también añadimos.', 20, '2025-07-22 12:39:48', 'img/receta_6889228bcd184.jpg'),
(6, 1, 'ENSALADA DE PIMIENTO, TOMATE Y CEBOLLA ASADOS', 'PIMIENTO, TOMATE, CEBOLLA,', 'A', 25, '2025-07-22 12:42:40', 'img/receta_688926972a87f.jpg'),
(7, 1, 'BACALAO DORADO', 'BACALAO\r\nPATATAS FAJA\r\nCEBOLLA\r\nAOVE', 'MMMMMMMM MMMMMMM MMMMMMMM MMMM MMMMMMMMMMMM MMMMM MMMMM MMMMM MMMM MMMM MMMMMMMM', 25, '2025-07-22 12:46:54', ''),
(8, 1, 'TARTA DE QUESO DE MANGO', 'MMMMMMM\r\nMMMMMMM\r\nMMMMMM\r\nMMMMMM\r\nMMMMMM\r\nMMMMMMM\r\nMMMMMMM\r\nMMMMMMM', 'MMMMMMMMMM MMMMMMMM MMMMMMMMM MMMM MMMMMMM MMMMMMM MMMMMM MMMMMM MMMMMMMMM MMMMMM MMMMM MMMMMMMM MMMMMMMM MMMMMM MMMMMMMM MMMMMMM MMMMMMMMMMMM MMMMM NMMMMMMM MMMMMMM MMMMM MMMM MMMMM', 25, '2025-07-22 12:49:35', ''),
(9, 1, 'CHOTO AL AJILLO', 'CHOTO, AJO, AOVE, VINO BLANCO, PIMIENTOS ROJOS SECOS, SAL, PIMIENTA', 'Para medio choto se fríe una cabeza de ajo. Los dientes partidos por el centro se ponen a freír y cuando empiecen a dorarse se añade el choto hecho trocitos pequeños a fuego lento y se va removiendo de vez en cuando hasta que esté dorada la carne; entonces se le añade media botella de vino blanco y cuando empiece a hervir se vuelve a poner a fuego bajo. Salpimentar al gusto. Cuando la carne esté tierna se le añaden cuatro o cinco pimientos rojos secos (previamente lavados, pipas quitadas y dejados 5 minutos en remojo) para que hiervan junto a la carne unos 5 minutos.', 30, '2025-07-22 12:52:08', 'img/receta_68891f5ddedc0.jpg'),
(10, 1, 'CARRILLADA DE TERNERA', 'CARILLADA DE TERNERA, ZANAHORIA, PUERRO, PIMIENTO ROJO, AJO, VINO, SAL, AOVE', 'Un kilo de carrillada de ternera hecha rodajas. Se enharina y se fríe poquito, simplemente que se sellen ambas caras y se aparta a una fuente. A continuación se parte en trocitos una zanahorias, 1 puerro, un pimiento rojo y una cabeza de ajo pelada (puede ser entera) porque todo esto se sofríe en el mismo aceite de haber sellado la carne. Una vez está el sofrito hecho, se le añade la carne, se le da unas vueltas durante un par de minutos y se le echa media botella de vino tinto o blanco y el agua suficiente hasta cubrir la carne (ahí se añade una pastilla de avecrem o se salpimentarlos al gusto) y se deja hirviendo a fuego suave, y cuando está tierna se saca la carrillada. La verdura o se deja como quede o se tritura y se echa encima de la carne. Se sirve con patatas fritas, patatas al horno o ensalada.', 60, '2025-07-28 11:45:03', 'img/receta_6887469f112bc4.64682856-flor-de-cerezo.png'),
(11, 2, 'patatas fritas con huevo', 'patata\r\nhuevo\r\nsal', 'hola', 20, '2025-07-28 11:52:56', 'img/receta_6887487817d6b2.37543678-flor.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recetas_categorias`
--

DROP TABLE IF EXISTS `recetas_categorias`;
CREATE TABLE IF NOT EXISTS `recetas_categorias` (
  `id_receta` int NOT NULL,
  `id_categoria` int NOT NULL,
  PRIMARY KEY (`id_receta`,`id_categoria`),
  KEY `id_categoria` (`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `recetas_categorias`
--

INSERT INTO `recetas_categorias` (`id_receta`, `id_categoria`) VALUES
(9, 2),
(10, 2),
(11, 2),
(2, 10),
(5, 10),
(6, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nombre_usuario` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contrasena` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `nombre_usuario` (`nombre_usuario`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre_usuario`, `email`, `contrasena`, `fecha_registro`) VALUES
(1, 'a', 'a@gmail.com', '$2y$10$cujDpC7Gk70etPNPcQ73I.pJ0OgNbQYhsw1EZL6GftHwvD0n1kJti', '2025-07-22 10:21:12'),
(2, 'b', 'b@gmail.com', '$2y$10$0lxhOto4pV/ojJ1RtgTvNu/Yu7L4AkcExCojkuFxFoB806lbfXz6m', '2025-07-22 10:34:09'),
(3, 'c', 'c@gmail.com', '$2y$10$Wfxn6P3rOQ.EAouidxvF5.gsBf5ZOAx0BfWdSz8pm/qpqgZLmp5.q', '2025-07-22 10:34:55');

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
