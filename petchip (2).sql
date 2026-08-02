-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-08-2026 a las 01:57:37
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `petchip`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `animales`
--

CREATE TABLE `animales` (
  `id_animal` int(11) NOT NULL,
  `id_dueno` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `especie` enum('perro','gato','otro') NOT NULL DEFAULT 'perro',
  `raza` varchar(100) DEFAULT NULL,
  `sexo` enum('macho','hembra','desconocido') NOT NULL DEFAULT 'desconocido',
  `edad` int(11) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `color` varchar(80) DEFAULT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `tamano` enum('pequeno','mediano','grande') DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `estado` enum('activo','perdido','encontrado','fallecido','adoptado') NOT NULL DEFAULT 'activo',
  `esterilizado` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_esterilizacion` date DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `animales`
--

INSERT INTO `animales` (`id_animal`, `id_dueno`, `nombre`, `especie`, `raza`, `sexo`, `edad`, `fecha_nacimiento`, `color`, `peso`, `tamano`, `foto`, `estado`, `esterilizado`, `fecha_esterilizacion`, `observaciones`, `fecha_registro`) VALUES
(5, 5, 'Max', 'perro', 'Labrador Retriever', 'macho', 4, '2022-03-15', 'Dorado', 30.50, 'grande', NULL, 'activo', 1, '2023-01-10', 'Vacunas al día', '2026-07-30 21:30:35'),
(6, 5, 'Luna', 'perro', 'Chihuahua', 'hembra', 2, '2024-01-20', 'Café', 2.80, 'pequeno', NULL, 'activo', 0, NULL, '', '2026-07-30 21:30:35'),
(7, 6, 'Michi', 'gato', 'Doméstico', 'hembra', 3, '2023-05-10', 'Gris', 4.20, 'pequeno', NULL, 'activo', 1, '2024-02-15', '', '2026-07-30 21:30:35'),
(8, 7, 'Rocky', 'perro', 'Pastor Alemán', 'macho', 5, '2021-04-18', 'Negro y café', 35.80, 'grande', NULL, 'activo', 1, '2022-07-20', '', '2026-07-30 21:30:35'),
(9, 7, 'Nala', 'perro', 'Golden Retriever', 'hembra', 3, '2023-02-08', 'Dorado', 27.00, 'grande', NULL, 'activo', 0, NULL, '', '2026-07-30 21:30:35'),
(10, 7, 'Simba', 'gato', 'Siamés', 'macho', 2, '2024-01-05', 'Crema', 3.80, 'pequeno', NULL, 'activo', 0, NULL, '', '2026-07-30 21:30:35'),
(11, 8, 'Toby', 'perro', 'Beagle', 'macho', 4, '2022-06-11', 'Tricolor', 12.00, 'mediano', NULL, 'activo', 1, '2023-08-15', '', '2026-07-30 21:30:35'),
(12, 8, 'Pelusa', 'gato', 'Persa', 'hembra', 5, '2021-08-25', 'Blanco', 4.50, 'pequeno', NULL, 'activo', 1, '2022-12-01', '', '2026-07-30 21:30:35'),
(13, 9, 'Bruno', 'perro', 'Mestizo', 'macho', 6, '2020-10-03', 'Café', 18.50, 'mediano', NULL, 'activo', 1, '2021-11-12', '', '2026-07-30 21:30:35'),
(14, 10, 'Kira', 'perro', 'Husky Siberiano', 'hembra', 3, '2023-03-14', 'Blanco y gris', 24.00, 'grande', NULL, 'activo', 0, NULL, '', '2026-07-30 21:30:35'),
(15, 10, 'Zeus', 'perro', 'Rottweiler', 'macho', 5, '2021-05-28', 'Negro', 42.50, 'grande', NULL, 'activo', 1, '2022-06-18', '', '2026-07-30 21:30:35'),
(16, 11, 'Garfield', 'gato', 'Doméstico', 'macho', 4, '2022-04-17', 'Naranja', 5.00, 'pequeno', NULL, 'activo', 1, '2023-02-12', '', '2026-07-30 21:30:35'),
(17, 12, 'Milo', 'gato', 'Maine Coon', 'macho', 3, '2023-07-08', 'Gris', 6.80, 'mediano', NULL, 'activo', 0, NULL, '', '2026-07-30 21:30:35'),
(18, 12, 'Lola', 'gato', 'Doméstico', 'hembra', 2, '2024-02-10', 'Blanco', 3.40, 'pequeno', NULL, 'activo', 0, NULL, '', '2026-07-30 21:30:35'),
(19, 12, 'Coco', 'perro', 'Pug', 'macho', 5, '2021-11-15', 'Beige', 8.50, 'pequeno', NULL, 'activo', 1, '2022-10-20', '', '2026-07-30 21:30:35'),
(20, 13, 'Thor', 'perro', 'Pitbull', 'macho', 4, '2022-08-04', 'Gris', 29.00, 'grande', NULL, 'activo', 1, '2023-09-10', '', '2026-07-30 21:30:35'),
(21, 13, 'Maya', 'perro', 'Border Collie', 'hembra', 2, '2024-01-12', 'Blanco y negro', 18.00, 'mediano', NULL, 'activo', 0, NULL, '', '2026-07-30 21:30:35'),
(22, 14, 'Canela', 'perro', 'French Poodle', 'hembra', 6, '2020-09-30', 'Blanco', 6.20, 'pequeno', NULL, 'activo', 1, '2021-12-05', '', '2026-07-30 21:30:35'),
(23, 15, 'Bolt', 'perro', 'Schnauzer', 'macho', 3, '2023-01-19', 'Gris', 9.80, 'mediano', NULL, 'activo', 0, NULL, '', '2026-07-30 21:30:35'),
(24, 15, 'Misu', 'gato', 'Doméstico', 'hembra', 4, '2022-05-21', 'Negro', 4.00, 'pequeno', NULL, 'activo', 1, '2023-03-09', '', '2026-07-30 21:30:35'),
(25, 16, 'Princesa', 'perro', 'Chihuahua', 'hembra', 5, '2021-06-18', 'Blanco', 2.50, 'pequeno', NULL, 'activo', 1, '2022-05-16', '', '2026-07-30 21:30:35'),
(26, 17, 'Tom', 'gato', 'Doméstico', 'macho', 2, '2024-03-08', 'Atigrado', 3.60, 'pequeno', NULL, 'activo', 0, NULL, '', '2026-07-30 21:30:35'),
(27, 18, 'Rex', 'perro', 'Pastor Alemán', 'macho', 7, '2019-07-11', 'Negro y café', 36.00, 'grande', NULL, 'activo', 1, '2020-09-22', '', '2026-07-30 21:30:35'),
(28, 18, 'Nina', 'perro', 'Mestizo', 'hembra', 3, '2023-04-14', 'Café', 15.00, 'mediano', NULL, 'activo', 1, '2024-01-08', '', '2026-07-30 21:30:35'),
(29, 19, 'Firulais', 'perro', 'Mestizo', 'macho', 8, '2018-12-05', 'Café', 20.00, 'mediano', NULL, 'activo', 1, '2019-11-15', '', '2026-07-30 21:30:35'),
(30, 20, 'Lucky', 'perro', 'Labrador Retriever', 'macho', 2, '2024-02-20', 'Negro', 28.00, 'grande', NULL, 'activo', 0, NULL, '', '2026-07-30 21:30:35'),
(31, 20, 'Kiara', 'perro', 'Golden Retriever', 'hembra', 4, '2022-01-14', 'Dorado', 26.50, 'grande', NULL, 'activo', 1, '2023-04-11', '', '2026-07-30 21:30:35'),
(32, 20, 'Mimi', 'gato', 'Siamés', 'hembra', 1, '2025-02-10', 'Crema', 2.90, 'pequeno', NULL, 'activo', 0, NULL, '', '2026-07-30 21:30:35'),
(33, 21, 'Oso', 'perro', 'Mestizo', 'macho', 5, '2021-09-17', 'Negro', 21.00, 'mediano', NULL, 'activo', 1, '2022-08-18', '', '2026-07-30 21:30:35'),
(34, 22, 'Bobby', 'perro', 'Beagle', 'macho', 3, '2023-05-09', 'Tricolor', 13.00, 'mediano', NULL, 'activo', 0, NULL, '', '2026-07-30 21:30:35'),
(35, 22, 'Moka', 'gato', 'Persa', 'hembra', 4, '2022-02-27', 'Café', 4.10, 'pequeno', NULL, 'activo', 1, '2023-01-19', '', '2026-07-30 21:30:35'),
(36, 23, 'Leo', 'gato', 'Doméstico', 'macho', 6, '2020-06-13', 'Blanco y negro', 4.80, 'pequeno', NULL, 'activo', 1, '2021-07-07', '', '2026-07-30 21:30:35'),
(37, 24, 'Camila', 'perro', 'Labrador Retriever', 'hembra', 3, '2023-03-23', 'Chocolate', 29.50, 'grande', NULL, 'activo', 0, NULL, '', '2026-07-30 21:30:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `animales_encontrados`
--

CREATE TABLE `animales_encontrados` (
  `id_reporte` int(11) NOT NULL,
  `descripcion` text NOT NULL,
  `ubicacion_encontrado` varchar(200) DEFAULT NULL,
  `fecha_hallazgo` date NOT NULL,
  `contacto_hallador` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `id_tag_detectado` varchar(50) DEFAULT NULL,
  `estado` enum('en_resguardo','reunido','cerrado') NOT NULL DEFAULT 'en_resguardo',
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `animales_perdidos`
--

CREATE TABLE `animales_perdidos` (
  `id_reporte` int(11) NOT NULL,
  `id_animal` int(11) DEFAULT NULL,
  `descripcion` text NOT NULL,
  `ultima_ubicacion` varchar(200) DEFAULT NULL,
  `fecha_perdida` date NOT NULL,
  `contacto` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `estado` enum('perdido','encontrado','cerrado') NOT NULL DEFAULT 'perdido',
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `avisos_encontrado`
--

CREATE TABLE `avisos_encontrado` (
  `id_aviso` int(11) NOT NULL,
  `id_perro` int(11) NOT NULL,
  `nombre_reportante` varchar(100) DEFAULT NULL,
  `telefono_reportante` varchar(20) DEFAULT NULL,
  `comentarios` text DEFAULT NULL,
  `lugar` varchar(150) DEFAULT NULL,
  `lat` decimal(10,7) DEFAULT NULL,
  `lng` decimal(10,7) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `avisos_encontrado`
--

INSERT INTO `avisos_encontrado` (`id_aviso`, `id_perro`, `nombre_reportante`, `telefono_reportante`, `comentarios`, `lugar`, `lat`, `lng`, `fecha_registro`) VALUES
(2, 3, 'Aurora', '5534567891', 'El gatito esta bien, lo tengo en mi casa puedes llamar', 'La encontre en calle campo florido', NULL, NULL, '2026-07-31 18:43:10'),
(3, 3, '', '', 'Esta cerca del libramiento, el gatito esta bien.', 'Amecameca', NULL, NULL, '2026-08-01 01:13:52'),
(4, 47, '', '', 'La perrita esta en perfectas condiciones', 'Colonia Alzate (Ozumba): Frente a la Delegación', NULL, NULL, '2026-08-01 14:20:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora`
--

CREATE TABLE `bitacora` (
  `id_bitacora` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `usuario_nombre` varchar(50) DEFAULT NULL,
  `accion` varchar(50) NOT NULL,
  `modulo` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `fecha_hora` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campanas_atendidos`
--

CREATE TABLE `campanas_atendidos` (
  `id_atendido` int(11) NOT NULL,
  `id_campana` int(11) NOT NULL,
  `id_perro` int(11) DEFAULT NULL,
  `nota` varchar(255) DEFAULT NULL,
  `usuario` varchar(50) DEFAULT NULL,
  `fecha_hora` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campanas_esterilizacion`
--

CREATE TABLE `campanas_esterilizacion` (
  `id_campania` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `ubicacion` varchar(150) NOT NULL,
  `meta_animales` int(11) NOT NULL DEFAULT 0,
  `realizadas` int(11) NOT NULL DEFAULT 0,
  `estado` varchar(20) NOT NULL DEFAULT 'Programada'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `campanas_esterilizacion`
--

INSERT INTO `campanas_esterilizacion` (`id_campania`, `nombre`, `fecha_inicio`, `ubicacion`, `meta_animales`, `realizadas`, `estado`) VALUES
(2, 'Campaña gratuita de esterilización', '2026-08-08', 'Colonia Alzate (Ozumba): Frente a la Delegación', 30, 0, 'Programada'),
(3, 'Jornada de vacunacion contra la rabia', '2026-08-12', 'Colonia Alzate (Ozumba): Frente a la Delegación', 40, 0, 'Programada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campanias_esterilizacion`
--

CREATE TABLE `campanias_esterilizacion` (
  `id_campania` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `ubicacion` varchar(200) DEFAULT NULL,
  `meta_animales` int(11) DEFAULT NULL,
  `estado` enum('planeada','en_curso','finalizada') NOT NULL DEFAULT 'planeada',
  `observaciones` text DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `duenos`
--

CREATE TABLE `duenos` (
  `id_dueno` int(11) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `correo` varchar(120) DEFAULT NULL,
  `usuario_portal` varchar(60) DEFAULT NULL,
  `contrasena_portal` varchar(255) DEFAULT NULL,
  `portal_activo` tinyint(1) NOT NULL DEFAULT 1,
  `direccion` varchar(255) DEFAULT NULL,
  `colonia` varchar(120) DEFAULT NULL,
  `municipio` varchar(120) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `codigo_postal` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `duenos`
--

INSERT INTO `duenos` (`id_dueno`, `nombre`, `telefono`, `correo`, `usuario_portal`, `contrasena_portal`, `portal_activo`, `direccion`, `colonia`, `municipio`, `foto`, `fecha_registro`, `codigo_postal`) VALUES
(4, 'Maria Roa', '5551844340', 'maria@gmail.com', 'maria', '$2y$10$vb.rkLSkhZpnrCIZYBJMm.3SboIEf2H45Gi4M0GOgto9IL2Tg0lwS', 1, 'Prolongacion Aldama', 'Amecameca de Juárez', 'Amecameca', NULL, '2026-07-30 14:14:25', '56900'),
(5, 'Gerardo Gomez Torres', '5510000001', 'familia1@demo.com', 'familia1', 'ujOeHwdF', 1, 'Av. Hidalgo 12', 'Centro', 'Ozumba', NULL, '2026-07-30 21:21:46', NULL),
(6, 'Rosa Gomez Mendoza', '5510000002', 'familia2@demo.com', 'familia2', 'cAefAZhn', 1, 'Calle Juárez 45', 'Centro', 'Amecameca', NULL, '2026-07-30 21:21:46', NULL),
(7, 'Arturo Contreras Silva', '5510000003', 'familia3@demo.com', 'familia3', 'M6Jy8c1r', 1, 'Av. Morelos 18', 'Centro', 'Atlautla', NULL, '2026-07-30 21:21:46', NULL),
(8, 'Martha Jimenez Cruz', '5510000004', 'familia4@demo.com', 'familia4', 'ihtYlKNx', 1, 'Calle Independencia 32', 'Centro', 'Tepetlixpa', NULL, '2026-07-30 21:21:46', NULL),
(9, 'Fernando Aguilar Ortiz', '5510000005', 'familia5@demo.com', 'familia5', 'HddmQAtK', 1, 'Calle Zaragoza 15', 'Centro', 'Juchitepec', NULL, '2026-07-30 21:21:46', NULL),
(10, 'Susana Rivera Morales', '5510000006', 'familia6@demo.com', 'familia6', 'CsXRpJG2', 1, 'Av. Benito Juárez 77', 'Centro', 'Ecatzingo', NULL, '2026-07-30 21:21:46', NULL),
(11, 'Salvador Diaz Hernandez', '5510000007', 'familia7@demo.com', 'familia7', 'Tr8hzUjE', 1, 'Calle Hidalgo 21', 'Centro', 'Ayapango', NULL, '2026-07-30 21:21:46', NULL),
(12, 'Diana Aguilar Silva', '5510000008', 'familia8@demo.com', 'familia8', 'cPVJ2tRK', 1, 'Calle Reforma 54', 'Centro', 'Tenango del Aire', NULL, '2026-07-30 21:21:46', NULL),
(13, 'Adrian Gomez Gonzalez', '5510000009', 'familia9@demo.com', 'familia9', 'JC06DPdR', 1, 'Calle Guerrero 8', 'Industrial', 'Ozumba', NULL, '2026-07-30 21:21:46', NULL),
(14, 'Diana Sanchez Cruz', '5510000010', 'familia10@demo.com', 'familia10', 'O9YrxPbC', 1, 'Av. 20 de Noviembre 40', 'Centro', 'Amecameca', NULL, '2026-07-30 21:21:46', NULL),
(15, 'Arturo Gomez Castillo', '5510000011', 'familia11@demo.com', 'familia11', '$2y$10$bsJw9ilITqpOde7tg9y8IubV9UQ6G2yD6uawBDDbxram3E7A4XqV6', 1, 'Calle Galeana 26', 'Centro', 'Atlautla', NULL, '2026-07-30 21:21:46', NULL),
(16, 'Rosa Aguilar Ramirez', '5510000012', 'familia12@demo.com', 'familia12', 'eBI2Y1rz', 1, 'Calle Allende 17', 'Centro', 'Tepetlixpa', NULL, '2026-07-30 21:21:46', NULL),
(17, 'Arturo Gomez Rivera', '5510000013', 'familia13@demo.com', 'familia13', 'w27jkooE', 1, 'Av. Morelos 63', 'Centro', 'Juchitepec', NULL, '2026-07-30 21:21:46', NULL),
(18, 'Sandra Ortiz Guerrero', '5510000014', 'familia14@demo.com', 'familia14', 'KqazwJ7Q', 1, 'Calle Nicolás Bravo 12', 'Centro', 'Ecatzingo', NULL, '2026-07-30 21:21:46', NULL),
(19, 'Roberto Mendoza Diaz', '5510000015', 'familia15@demo.com', 'familia15', 'FMPd3W2X', 1, 'Calle Matamoros 34', 'Centro', 'Ayapango', NULL, '2026-07-30 21:21:46', NULL),
(20, 'Martha Chavez Vazquez', '5510000016', 'familia16@demo.com', 'familia16', 'yygNdemk', 1, 'Calle Juárez 48', 'Centro', 'Tenango del Aire', NULL, '2026-07-30 21:21:46', NULL),
(21, 'Alejandro Garcia Guerrero', '5510000017', 'familia17@demo.com', 'familia17', 'vdajgwb2', 1, 'Av. Hidalgo 15', 'Centro', 'Ozumba', NULL, '2026-07-30 21:21:46', NULL),
(22, 'Diana Sanchez Torres', '5510000018', 'familia18@demo.com', 'familia18', 'Mjpvwh09', 1, 'Calle Independencia 20', 'Centro', 'Amecameca', NULL, '2026-07-30 21:21:46', NULL),
(23, 'Arturo Cortes Martinez', '5510000019', 'familia19@demo.com', 'familia19', 'CDfgvqZk', 1, 'Calle Morelos 50', 'Centro', 'Atlautla', NULL, '2026-07-30 21:21:46', NULL),
(24, 'Araceli Ramos Medina', '5510000020', 'familia20@demo.com', 'familia20', 'b6GjHbG8', 1, 'Calle Zaragoza 9', 'Centro', 'Ozumba', NULL, '2026-07-30 21:21:46', NULL),
(25, 'Emmanuel Alcantara Romero', '5658862442', 'emaa.12@gmail.com', 'Emma', '$2y$10$8P.M0BoVnUbeULWYz1D.fuDF8MxTIh.5WR9hSTclQL9CAb3oagljm', 1, 'Calle Sor Juana Ines de la Cruz', 'Ozumba de Alzate (Cabecera municipal)', 'Ozumba', NULL, '2026-07-31 10:08:13', '56800'),
(26, 'Dueño de Prueba', '5555555555', 'dueno@petchip.demo', 'duenoprueba', '$2b$10$r.1jnxP36Riq6LFxMAm.n.jG9Xgx4uxsi/3mNFH2W.px91Qw5xbCK', 1, 'Calle Demo 123', 'Centro', 'Ozumba', NULL, '2026-07-31 17:47:08', NULL),
(27, 'Mario Nava', '5528964200', 'mario@gmail.com', NULL, NULL, 1, 'Francisco Sarabia', 'Amecameca de Juárez', 'Amecameca', NULL, '2026-08-01 00:44:10', '56900');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `esterilizaciones`
--

CREATE TABLE `esterilizaciones` (
  `id_esterilizacion` int(11) NOT NULL,
  `id_animal` int(11) NOT NULL,
  `id_campania` int(11) DEFAULT NULL,
  `fecha` date NOT NULL,
  `veterinario` varchar(120) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_veterinario`
--

CREATE TABLE `historial_veterinario` (
  `id_consulta` int(11) NOT NULL,
  `id_animal` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `motivo` varchar(200) NOT NULL,
  `diagnostico` text DEFAULT NULL,
  `tratamiento` text DEFAULT NULL,
  `veterinario` varchar(120) DEFAULT NULL,
  `id_veterinario` int(11) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial_veterinario`
--

INSERT INTO `historial_veterinario` (`id_consulta`, `id_animal`, `fecha`, `motivo`, `diagnostico`, `tratamiento`, `veterinario`, `id_veterinario`, `observaciones`, `fecha_registro`) VALUES
(1, 3, '2026-07-31', 'Revision', 'Cabeza y cara: revisión de ojos (sin secreciones), nariz (húmeda y limpia) y oídos (sin ácaros ni mal olor).Boca: revisión de dientes y encías para detectar sarro o dolor.Cuerpo: auscultación de corazón y pulmones, palpación del abdomen y control de peso.Piel y pelaje: búsqueda de pulgas, heridas o caída anormal de pelo.', 'Ninguno', 'Dr. Salvador Lopez', 1, NULL, '2026-07-31 18:55:53'),
(2, 46, '2026-08-04', 'Vacunacio', 'Se le aplico la vacuna que es de 6 a 8 meses de  edad', 'ninguno', 'Dr. Salvador Lopez', 1, NULL, '2026-08-01 00:58:37'),
(3, 47, '2026-07-15', 'Vacunacion', 'ninguno', 'ninguno', '', NULL, NULL, '2026-08-01 14:16:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lecturas_rfid`
--

CREATE TABLE `lecturas_rfid` (
  `id_lectura` int(11) NOT NULL,
  `id_tag` int(11) NOT NULL,
  `fecha_hora` datetime NOT NULL DEFAULT current_timestamp(),
  `ubicacion` varchar(150) DEFAULT NULL,
  `usuario_lector` varchar(50) DEFAULT NULL,
  `resultado` enum('encontrado','no_encontrado') NOT NULL DEFAULT 'encontrado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id_notificacion` int(11) NOT NULL,
  `id_dueno` int(11) NOT NULL,
  `id_perro` int(11) DEFAULT NULL,
  `tipo` varchar(30) NOT NULL DEFAULT 'general',
  `mensaje` varchar(255) NOT NULL,
  `leida` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id_notificacion`, `id_dueno`, `id_perro`, `tipo`, `mensaje`, `leida`, `fecha_creacion`) VALUES
(1, 4, 3, 'aviso_encontrado', 'Aurora reportó haber encontrado a Pancho en: La encontre en calle campo florido', 0, '2026-07-31 18:43:10'),
(2, 4, 3, 'aviso_encontrado', 'Alguien reportó haber encontrado a Pancho en: Amecameca', 0, '2026-08-01 01:13:52'),
(3, 25, 47, 'aviso_encontrado', 'Alguien reportó haber encontrado a Tili en: Colonia Alzate (Ozumba): Frente a la Delegación', 1, '2026-08-01 14:20:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `perros`
--

CREATE TABLE `perros` (
  `id_perro` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `especie` varchar(20) NOT NULL DEFAULT 'Perro',
  `raza` varchar(60) DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `sexo` varchar(10) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `tamano` varchar(20) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `esterilizado` tinyint(1) NOT NULL DEFAULT 0,
  `estado` varchar(20) NOT NULL DEFAULT 'Activo',
  `colonia` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `token_publico` varchar(40) DEFAULT NULL,
  `compartir_info_medica` tinyint(1) NOT NULL DEFAULT 0,
  `id_dueno` int(11) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `perros`
--

INSERT INTO `perros` (`id_perro`, `nombre`, `especie`, `raza`, `edad`, `sexo`, `color`, `peso`, `tamano`, `fecha_nacimiento`, `esterilizado`, `estado`, `colonia`, `foto`, `observaciones`, `token_publico`, `compartir_info_medica`, `id_dueno`, `fecha_registro`) VALUES
(3, 'Pancho', 'Gato', 'Tabby', 2, 'Macho', 'gris atigrado', 4.00, 'Mediano', '2024-05-08', 1, 'Activo', 'Amecameca de Juárez', 'img_6a6be441a0f18.jpeg', '', 'c612586c5731f45e620d766b35f207bb', 1, 4, '2026-07-30 14:19:24'),
(4, 'Chocolate', 'Gato', 'Siamés', 1, 'Macho', 'Dorado / Beige', 1.00, 'Pequeño', '2026-05-04', 0, 'Activo', 'Amecameca de Juárez', 'img_6a6be43037fdc.jpeg', '', '64d7cd51b3e4685e978436807f387cd9', 1, 4, '2026-07-30 14:24:42'),
(5, 'Max', 'Perro', 'Labrador Retriever', 4, 'Macho', 'Dorado', 30.50, 'Grande', '2022-03-15', 1, 'Activo', 'Centro', NULL, 'Vacunas al día', NULL, 0, 5, '2026-07-30 21:35:13'),
(6, 'Luna', 'Perro', 'Chihuahua', 2, 'Hembra', 'Café', 2.80, 'Pequeño', '2024-01-20', 0, 'Activo', 'Centro', NULL, 'Muy juguetona', NULL, 0, 5, '2026-07-30 21:35:13'),
(7, 'Michi', 'Gato', 'Doméstico', 3, 'Hembra', 'Gris', 4.20, 'Pequeño', '2023-05-10', 1, 'Activo', 'Centro', NULL, 'Rescatada', 'b050ca7d23cb2b193870a613427d4357', 0, 6, '2026-07-30 21:35:13'),
(8, 'Rocky', 'Perro', 'Pastor Alemán', 5, 'Macho', 'Negro y café', 35.80, 'Grande', '2021-04-18', 1, 'Activo', 'Centro', NULL, 'Protector', NULL, 0, 7, '2026-07-30 21:35:13'),
(9, 'Nala', 'Perro', 'Golden Retriever', 3, 'Hembra', 'Dorado', 27.00, 'Grande', '2023-02-08', 0, 'Activo', 'Centro', NULL, 'Muy sociable', NULL, 0, 7, '2026-07-30 21:35:13'),
(10, 'Simba', 'Gato', 'Siamés', 2, 'Macho', 'Crema', 3.80, 'Pequeño', '2024-01-05', 0, 'Activo', 'Centro', NULL, 'Le gusta salir al patio', NULL, 0, 7, '2026-07-30 21:35:13'),
(11, 'Toby', 'Perro', 'Beagle', 4, 'Macho', 'Tricolor', 12.00, 'Mediano', '2022-06-11', 1, 'Activo', 'Centro', NULL, 'Muy activo', NULL, 0, 8, '2026-07-30 21:35:13'),
(12, 'Pelusa', 'Gato', 'Persa', 5, 'Hembra', 'Blanco', 4.50, 'Pequeño', '2021-08-25', 1, 'Activo', 'Centro', NULL, 'Necesita cepillado frecuente', NULL, 0, 8, '2026-07-30 21:35:13'),
(13, 'Bruno', 'Perro', 'Mestizo', 6, 'Macho', 'Café', 18.50, 'Mediano', '2020-10-03', 1, 'Activo', 'Centro', NULL, 'Rescatado', NULL, 0, 9, '2026-07-30 21:35:13'),
(14, 'Kira', 'Perro', 'Husky Siberiano', 3, 'Hembra', 'Blanco y gris', 24.00, 'Grande', '2023-03-14', 0, 'Activo', 'Centro', NULL, 'Muy energética', NULL, 0, 10, '2026-07-30 21:35:13'),
(15, 'Zeus', 'Perro', 'Rottweiler', 5, 'Macho', 'Negro', 42.50, 'Grande', '2021-05-28', 1, 'Activo', 'Centro', NULL, 'Buen guardián', NULL, 0, 10, '2026-07-30 21:35:13'),
(16, 'Garfield', 'Gato', 'Doméstico', 4, 'Macho', 'Naranja', 5.00, 'Pequeño', '2022-04-17', 1, 'Activo', 'Centro', NULL, 'Muy tranquilo', NULL, 0, 11, '2026-07-30 21:35:13'),
(17, 'Milo', 'Gato', 'Maine Coon', 3, 'Macho', 'Gris', 6.80, 'Mediano', '2023-07-08', 0, 'Activo', 'Centro', NULL, 'Cariñoso', NULL, 0, 12, '2026-07-30 21:35:13'),
(18, 'Lola', 'Gato', 'Doméstico', 2, 'Hembra', 'Blanco', 3.40, 'Pequeño', '2024-02-10', 0, 'Activo', 'Centro', NULL, 'Juguetona', NULL, 0, 12, '2026-07-30 21:35:13'),
(19, 'Coco', 'Perro', 'Pug', 5, 'Macho', 'Beige', 8.50, 'Pequeño', '2021-11-15', 1, 'Activo', 'Centro', NULL, 'Le gusta pasear', NULL, 0, 12, '2026-07-30 21:35:13'),
(20, 'Thor', 'Perro', 'Pitbull', 4, 'Macho', 'Gris', 29.00, 'Grande', '2022-08-04', 1, 'Activo', 'Industrial', NULL, 'Obediente', NULL, 0, 13, '2026-07-30 21:35:13'),
(21, 'Maya', 'Perro', 'Border Collie', 2, 'Hembra', 'Blanco y negro', 18.00, 'Mediano', '2024-01-12', 0, 'Activo', 'Industrial', NULL, 'Muy inteligente', NULL, 0, 13, '2026-07-30 21:35:13'),
(22, 'Canela', 'Perro', 'French Poodle', 6, 'Hembra', 'Blanco', 6.20, 'Pequeño', '2020-09-30', 1, 'Activo', 'Centro', NULL, 'Necesita corte de pelo frecuente', NULL, 0, 14, '2026-07-30 21:35:13'),
(23, 'Bolt', 'Perro', 'Schnauzer', 3, 'Macho', 'Gris', 9.80, 'Mediano', '2023-01-19', 0, 'Activo', 'Centro', NULL, 'Muy obediente', NULL, 0, 15, '2026-07-30 21:35:34'),
(24, 'Misu', 'Gato', 'Doméstico', 4, 'Hembra', 'Negro', 4.00, 'Pequeño', '2022-05-21', 1, 'Activo', 'Centro', NULL, 'Le gusta dormir al sol', NULL, 0, 15, '2026-07-30 21:35:34'),
(25, 'Princesa', 'Perro', 'Chihuahua', 5, 'Hembra', 'Blanco', 2.50, 'Pequeño', '2021-06-18', 1, 'Activo', 'Centro', NULL, 'Muy cariñosa', NULL, 0, 16, '2026-07-30 21:35:34'),
(26, 'Leo', 'Gato', 'Siamés', 2, 'Macho', 'Crema', 3.70, 'Pequeño', '2024-01-16', 0, 'Activo', 'Centro', NULL, 'Muy curioso', NULL, 0, 16, '2026-07-30 21:35:34'),
(27, 'Tom', 'Gato', 'Doméstico', 2, 'Macho', 'Atigrado', 3.60, 'Pequeño', '2024-03-08', 0, 'Activo', 'Centro', NULL, 'Juguetón', NULL, 0, 17, '2026-07-30 21:35:34'),
(28, 'Zeus', 'Perro', 'Border Collie', 3, 'Macho', 'Blanco y negro', 19.20, 'Mediano', '2023-02-11', 1, 'Activo', 'Centro', NULL, 'Muy inteligente', NULL, 0, 17, '2026-07-30 21:35:34'),
(29, 'Rex', 'Perro', 'Pastor Alemán', 7, 'Macho', 'Negro y café', 36.00, 'Grande', '2019-07-11', 1, 'Activo', 'Centro', NULL, 'Entrenado para obediencia', NULL, 0, 18, '2026-07-30 21:35:34'),
(30, 'Nina', 'Perro', 'Mestizo', 3, 'Hembra', 'Café', 15.00, 'Mediano', '2023-04-14', 1, 'Activo', 'Centro', NULL, 'Muy amigable', NULL, 0, 18, '2026-07-30 21:35:34'),
(31, 'Firulais', 'Perro', 'Mestizo', 8, 'Macho', 'Café', 20.00, 'Mediano', '2018-12-05', 1, 'Activo', 'Centro', NULL, 'Rescatado', NULL, 0, 19, '2026-07-30 21:35:34'),
(32, 'Luna', 'Gato', 'Persa', 4, 'Hembra', 'Blanco', 4.30, 'Pequeño', '2022-07-20', 1, 'Activo', 'Centro', NULL, 'Pelaje abundante', NULL, 0, 19, '2026-07-30 21:35:34'),
(33, 'Lucky', 'Perro', 'Labrador Retriever', 2, 'Macho', 'Negro', 28.00, 'Grande', '2024-02-20', 0, 'Activo', 'Centro', NULL, 'Muy juguetón', NULL, 0, 20, '2026-07-30 21:35:34'),
(34, 'Kiara', 'Perro', 'Golden Retriever', 4, 'Hembra', 'Dorado', 26.50, 'Grande', '2022-01-14', 1, 'Activo', 'Centro', NULL, 'Excelente con niños', NULL, 0, 20, '2026-07-30 21:35:34'),
(35, 'Mimi', 'Gato', 'Siamés', 1, 'Hembra', 'Crema', 2.90, 'Pequeño', '2025-02-10', 0, 'Activo', 'Centro', NULL, 'Muy tranquila', NULL, 0, 20, '2026-07-30 21:35:34'),
(36, 'Oso', 'Perro', 'Mestizo', 5, 'Macho', 'Negro', 21.00, 'Mediano', '2021-09-17', 1, 'Activo', 'Centro', NULL, 'Buen guardián', NULL, 0, 21, '2026-07-30 21:35:34'),
(37, 'Nube', 'Gato', 'Doméstico', 2, 'Hembra', 'Gris', 3.50, 'Pequeño', '2024-04-02', 0, 'Activo', 'Centro', NULL, 'Muy juguetona', NULL, 0, 21, '2026-07-30 21:35:34'),
(38, 'Bobby', 'Perro', 'Beagle', 3, 'Macho', 'Tricolor', 13.00, 'Mediano', '2023-05-09', 0, 'Activo', 'Centro', NULL, 'Le encantan los paseos', NULL, 0, 22, '2026-07-30 21:35:34'),
(39, 'Moka', 'Gato', 'Persa', 4, 'Hembra', 'Café', 4.10, 'Pequeño', '2022-02-27', 1, 'Activo', 'Centro', NULL, 'Muy dócil', NULL, 0, 22, '2026-07-30 21:35:34'),
(40, 'Leo', 'Gato', 'Doméstico', 6, 'Macho', 'Blanco y negro', 4.80, 'Pequeño', '2020-06-13', 1, 'Activo', 'Centro', NULL, 'Muy independiente', NULL, 0, 23, '2026-07-30 21:35:34'),
(41, 'Rocky', 'Perro', 'Pitbull', 5, 'Macho', 'Gris', 30.20, 'Grande', '2021-05-04', 1, 'Activo', 'Centro', NULL, 'Muy protector', NULL, 0, 23, '2026-07-30 21:35:34'),
(42, 'Camila', 'Perro', 'Labrador Retriever', 3, 'Hembra', 'Chocolate', 29.50, 'Grande', '2023-03-23', 0, 'Activo', 'Centro', NULL, 'Vacunas al día', NULL, 0, 24, '2026-07-30 21:35:34'),
(43, 'Simba', 'Gato', 'Doméstico', 2, 'Macho', 'Naranja', 4.00, 'Pequeño', '2024-01-28', 0, 'Activo', 'Centro', NULL, 'Le gusta trepar árboles', NULL, 0, 24, '2026-07-30 21:35:34'),
(44, 'Dona', 'Perro', 'Mestizo / Criollo', 8, 'Hembra', 'Manchado (blanco y negro)', 30.00, 'Pequeño', '2018-02-08', 0, 'Activo', 'Sor Juana Ines de la Cruz', 'Un poco maltratada', NULL, 'e739d6a10ed6a2232fade12d791af792', 0, 14, '2026-07-31 09:56:04'),
(45, 'Firulais', 'Perro', 'Mestizo / Criollo', 3, 'Macho', 'Café', 12.50, 'Mediano', NULL, 1, 'Activo', 'Centro', NULL, NULL, '8013bf40856b3e0f5713a9e3d7d3dd4f', 0, 26, '2026-07-31 17:47:08'),
(46, 'Manchitas', 'Gato', 'Angora', 2, 'Macho', 'Manchado (blanco y negro)', 10.00, 'Grande', '2025-04-02', 0, 'Activo', 'Amecameca', 'img_6a6d97f02e86c.png', '', '895968e5d31b70de78de625136c8814d', 0, 27, '2026-08-01 00:52:46'),
(47, 'Tili', 'Perro', 'Mestizo / Criollo', 2, 'Hembra', 'Cafe claro', 11.80, 'Mediano', '2024-05-12', 0, 'Activo', 'Ozumba de Alzate', 'img_6a6e5316a3138.jpg', 'img_6a6e53064da90.jpg', 'ef8106e0e56a83ddd3ddcd3436ae138c', 0, 25, '2026-08-01 14:11:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportes_extravio`
--

CREATE TABLE `reportes_extravio` (
  `id_reporte` int(11) NOT NULL,
  `tipo` varchar(15) NOT NULL,
  `id_perro` int(11) DEFAULT NULL,
  `nombre_animal` varchar(100) DEFAULT NULL,
  `especie` varchar(20) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `lugar` varchar(150) DEFAULT NULL,
  `lat` decimal(10,7) DEFAULT NULL,
  `lng` decimal(10,7) DEFAULT NULL,
  `fecha` date NOT NULL,
  `contacto` varchar(100) NOT NULL,
  `recompensa` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'Activo',
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reportes_extravio`
--

INSERT INTO `reportes_extravio` (`id_reporte`, `tipo`, `id_perro`, `nombre_animal`, `especie`, `descripcion`, `lugar`, `lat`, `lng`, `fecha`, `contacto`, `recompensa`, `foto`, `estado`, `fecha_registro`) VALUES
(1, 'Perdido', NULL, 'Pitter', 'Perro', 'Blanco con manchas cafes', 'Terreno en zoyatzingo', 19.0942132, -98.7883130, '2026-07-29', '211545154', NULL, 'img_6a6bbf6512abe.webp', 'Resuelto', '2026-07-30 15:17:25'),
(2, 'Perdido', NULL, 'Michi', 'Gato', 'Es una gatita responde al nombre de Michi', 'Colonia Alzate (Ozumba)', 19.0386925, -98.7956411, '2026-08-01', '545454561', '500', NULL, 'Activo', '2026-07-31 17:38:08'),
(3, 'Perdido', 3, 'Pancho', 'Gato', 'Tiene un collar color azul es un gato atrigrado gris es tranquilo', 'Prolongacion aldama', NULL, NULL, '2026-08-01', '5551844340', NULL, 'img_6a6be441a0f18.jpeg', 'Resuelto', '2026-07-31 18:19:22'),
(4, 'Perdido', NULL, 'Firulais', 'Perro', 'Raza\r\nMestizo / Criollo\r\nSexo: Macho\r\nEdad: 3 años\r\nColor: Café', 'Tepetlixpa', 19.0205845, -98.8092449, '2026-08-01', '5511512254', '1000', NULL, 'Activo', '2026-07-31 18:40:50'),
(5, 'Perdido', 47, 'Tili', 'Perro', 'Es una perrita mestiza color cafe claro de 2 años de edad es tamaño mediana', 'Colonia Alzate (Ozumba)', NULL, NULL, '2026-08-01', '5658862442', NULL, 'img_6a6e5316a3138.jpg', 'Resuelto', '2026-08-01 14:19:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tags_rfid`
--

CREATE TABLE `tags_rfid` (
  `id_tag` int(11) NOT NULL,
  `codigo_tag` varchar(50) NOT NULL,
  `id_animal` int(11) NOT NULL,
  `fecha_asignacion` date NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(20) NOT NULL DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tags_rfid`
--

INSERT INTO `tags_rfid` (`id_tag`, `codigo_tag`, `id_animal`, `fecha_asignacion`, `activo`, `fecha_registro`, `estado`) VALUES
(10, 'RFID005', 7, '2026-08-01', 1, '2026-07-31 17:36:23', 'Activo'),
(12, 'RFID001', 3, '2026-08-06', 1, '2026-07-31 18:25:46', 'Activo'),
(13, 'RFID002', 4, '2026-08-01', 1, '2026-07-31 18:26:14', 'Activo'),
(14, 'RFID003', 38, '2026-08-01', 1, '2026-07-31 18:26:31', 'Activo'),
(15, 'RFID004', 23, '2026-08-01', 1, '2026-07-31 18:26:41', 'Activo'),
(16, 'RFID006', 13, '2026-08-01', 1, '2026-07-31 18:26:51', 'Activo'),
(17, 'RFID007', 35, '2026-08-01', 1, '2026-07-31 18:27:04', 'Activo'),
(18, 'RFID008', 39, '2026-08-01', 1, '2026-07-31 18:27:44', 'Activo'),
(19, 'RFID009', 24, '2026-08-01', 1, '2026-07-31 18:27:59', 'Activo'),
(20, 'RFID0010', 42, '2026-08-01', 1, '2026-07-31 18:28:13', 'Activo'),
(21, 'RFID0011', 37, '2026-08-01', 1, '2026-07-31 18:28:23', 'Activo'),
(22, 'RFID0012', 30, '2026-08-01', 1, '2026-07-31 18:28:33', 'Activo'),
(23, 'RFID0013', 15, '2026-08-01', 1, '2026-07-31 18:28:50', 'Activo'),
(24, 'RFID0014', 25, '2026-08-01', 1, '2026-07-31 18:29:04', 'Activo'),
(25, 'RFID0015', 12, '2026-08-01', 1, '2026-07-31 18:29:20', 'Activo'),
(26, 'RFID0016', 9, '2026-08-01', 1, '2026-07-31 18:29:31', 'Activo'),
(27, 'RFID0017', 36, '2026-08-01', 1, '2026-07-31 18:29:40', 'Activo'),
(28, 'RFID0018', 41, '2026-08-01', 1, '2026-07-31 18:29:50', 'Activo'),
(29, 'RFID0019', 43, '2026-08-01', 1, '2026-07-31 18:29:59', 'Activo'),
(30, 'RFID0020', 10, '2026-08-01', 1, '2026-07-31 18:30:08', 'Activo'),
(31, 'RFID0021', 19, '2026-08-01', 1, '2026-07-31 18:30:26', 'Activo'),
(32, 'RFID0022', 20, '2026-08-01', 1, '2026-07-31 18:30:39', 'Activo'),
(33, 'RFID0023', 28, '2026-08-01', 1, '2026-07-31 18:31:04', 'Activo'),
(34, 'RFID0024', 11, '2026-08-01', 1, '2026-07-31 18:31:17', 'Activo'),
(35, 'RFID0025', 27, '2026-08-01', 1, '2026-07-31 18:31:25', 'Activo'),
(36, 'RFID0026', 8, '2026-08-01', 1, '2026-07-31 18:31:34', 'Activo'),
(37, 'RFID0027', 17, '2026-08-01', 1, '2026-07-31 18:31:43', 'Activo'),
(38, 'RFID0028', 29, '2026-08-01', 1, '2026-07-31 18:31:54', 'Activo'),
(39, 'RFID0029', 21, '2026-08-01', 1, '2026-07-31 18:32:02', 'Activo'),
(40, 'RFID0030', 5, '2026-08-01', 1, '2026-07-31 18:32:14', 'Activo'),
(41, 'RFID0031', 6, '2026-08-01', 1, '2026-07-31 18:32:23', 'Activo'),
(42, 'RFID0032', 32, '2026-08-01', 1, '2026-07-31 18:32:31', 'Activo'),
(43, 'RFID0033', 33, '2026-08-01', 1, '2026-07-31 18:32:42', 'Activo'),
(44, 'RFID0034', 40, '2026-08-01', 1, '2026-07-31 18:32:53', 'Activo'),
(45, 'RFID0035', 18, '2026-08-01', 1, '2026-07-31 18:33:01', 'Activo'),
(46, 'RFID0036', 14, '2026-08-01', 1, '2026-07-31 18:33:10', 'Activo'),
(47, 'RFID0037', 16, '2026-08-01', 1, '2026-07-31 18:33:21', 'Activo'),
(48, 'RFID0038', 26, '2026-08-01', 1, '2026-07-31 18:33:30', 'Activo'),
(49, 'RFID0039', 22, '2026-08-01', 1, '2026-07-31 18:33:39', 'Activo'),
(50, 'RFID0040', 44, '2026-08-01', 1, '2026-07-31 18:33:47', 'Activo'),
(51, 'RFID0041', 31, '2026-08-01', 1, '2026-07-31 18:33:55', 'Activo'),
(52, 'RFID0042', 45, '2026-08-01', 1, '2026-07-31 18:34:04', 'Activo'),
(53, 'RFID0043', 34, '2026-08-01', 1, '2026-07-31 18:34:37', 'Activo'),
(54, 'RFID0044', 46, '2026-08-01', 1, '2026-08-01 00:54:07', 'Activo'),
(55, 'RFID0045', 47, '2026-08-01', 1, '2026-08-01 14:13:39', 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `nombre_completo` varchar(120) NOT NULL,
  `correo` varchar(120) DEFAULT NULL,
  `rol` enum('administrador','veterinario','autoridad','operador','dueno') NOT NULL DEFAULT 'dueno',
  `id_dueno` int(11) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `ultimo_acceso` datetime DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `usuario`, `contrasena`, `nombre_completo`, `correo`, `rol`, `id_dueno`, `activo`, `ultimo_acceso`, `fecha_creacion`) VALUES
(4, 'admin', '$2y$10$IH.HPvANo/yzJhgTCuX6xeY9IrAA7hWKFb62jNVfEQNJFz0p06AJ6', 'Administrador', 'admin@petchip.com', 'administrador', NULL, 1, NULL, '2026-07-30 19:50:06'),
(5, 'encargado', '$2y$10$c8BxUkMTIYcavdKT1dtDBOGEFrL/UKkJjOE85Onu.PQlMUP3ZlAFq', 'Encargado', 'encargado@petchip.com', 'autoridad', NULL, 1, NULL, '2026-07-30 19:50:06'),
(6, 'veterinario', '$2y$10$rAzNp03GrF0FQEOxKExGfef.3Jmj8vbrwwYUpAvjo09DwXZC.n/GK', 'Veterinario', 'veterinario@petchip.com', 'veterinario', NULL, 1, NULL, '2026-07-30 19:50:06'),
(7, 'duenoprueba', '$2y$10$bCHi3vkuJvjjI3o7eXNNK.8X9wXNTD5cXb8rx066QgzAGYRhluqy2', 'Dueño de mascota', 'dueno@petchip.com', 'dueno', NULL, 1, NULL, '2026-07-30 19:50:06'),
(8, 'maria', 'may123', 'Maria Roa', 'maria@gmail.com', 'dueno', 4, 1, NULL, '2026-07-31 10:17:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vacunas`
--

CREATE TABLE `vacunas` (
  `id_vacuna` int(11) NOT NULL,
  `id_animal` int(11) NOT NULL,
  `nombre_vacuna` varchar(120) NOT NULL,
  `fecha_aplicacion` date NOT NULL,
  `proxima_dosis` date DEFAULT NULL,
  `veterinario` varchar(120) DEFAULT NULL,
  `id_veterinario` int(11) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vacunas`
--

INSERT INTO `vacunas` (`id_vacuna`, `id_animal`, `nombre_vacuna`, `fecha_aplicacion`, `proxima_dosis`, `veterinario`, `id_veterinario`, `observaciones`, `fecha_registro`) VALUES
(6, 4, 'Triple felina (FVRCP)', '2026-08-01', '2026-10-01', 'Dr. Salvador Lopez', 1, NULL, '2026-07-31 18:52:28'),
(7, 4, 'leucemia felina', '2026-07-22', NULL, 'Dr. Salvador Lopez', 1, NULL, '2026-07-31 18:53:10'),
(8, 3, 'Triple felina (FVRCP)', '2026-07-14', '2026-09-14', 'Dr. Salvador Lopez', 1, NULL, '2026-07-31 18:54:06'),
(9, 3, 'leucemia felina', '2026-07-10', '2026-07-17', 'Dr. Salvador Lopez', 1, NULL, '2026-07-31 18:54:24'),
(10, 46, 'Triple felina (FVRCP)', '2026-08-04', NULL, 'Dr. Salvador Lopez', 1, NULL, '2026-08-01 00:56:38'),
(11, 47, 'Antirrabica', '2026-07-15', NULL, 'Dr. Jorge Gomez', 3, NULL, '2026-08-01 14:15:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `veterinarios`
--

CREATE TABLE `veterinarios` (
  `id_veterinario` int(11) NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `cedula_profesional` varchar(30) DEFAULT NULL,
  `especialidad` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `veterinarios`
--

INSERT INTO `veterinarios` (`id_veterinario`, `nombre`, `cedula_profesional`, `especialidad`, `telefono`, `correo`, `id_usuario`, `activo`, `fecha_registro`) VALUES
(1, 'Dr. Salvador Lopez', 'CP100001', 'Medicina general', '5511111111', 'vet1@petchip.com', NULL, 1, '2026-07-30 21:13:35'),
(2, 'Dra. Martha Rojas', 'CP100002', 'Cirugia', '5522222222', 'vet2@petchip.com', NULL, 1, '2026-07-30 21:13:35'),
(3, 'Dr. Jorge Gomez', 'CP100003', 'Medicina interna', '5533333333', 'vet3@petchip.com', NULL, 1, '2026-07-30 21:13:35'),
(4, 'Dra. Maria Guadalupe Cortes', 'CP100004', 'Dermatologia', '5544444444', 'vet4@petchip.com', NULL, 1, '2026-07-30 21:13:35'),
(5, 'Dr. Adrian Torres', 'CP100005', 'Cardiologia', '5555555555', 'vet5@petchip.com', NULL, 1, '2026-07-30 21:13:35'),
(6, 'Dra. Carmen Jimenez', 'CP100006', 'Odontologia veterinaria', '5566666666', 'vet6@petchip.com', NULL, 1, '2026-07-30 21:13:35'),
(7, 'Dr. Cesar Ramos', 'CP100007', 'Oftalmologia', '5577777777', 'vet7@petchip.com', NULL, 1, '2026-07-30 21:13:35'),
(8, 'Dra. Adriana Medina', 'CP100008', 'Ortopedia y traumatologia', '5588888888', 'vet8@petchip.com', NULL, 1, '2026-07-30 21:13:35'),
(9, 'Dr. Omar Vazquez', 'CP100009', 'Oncologia', '5599999999', 'vet9@petchip.com', NULL, 1, '2026-07-30 21:13:35'),
(10, 'Dra. Gabriela Morales', 'CP100010', 'Nutricion y dietetica', '5500000000', 'vet10@petchip.com', NULL, 1, '2026-07-30 21:13:35');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `animales`
--
ALTER TABLE `animales`
  ADD PRIMARY KEY (`id_animal`),
  ADD KEY `fk_animales_dueno` (`id_dueno`);

--
-- Indices de la tabla `animales_encontrados`
--
ALTER TABLE `animales_encontrados`
  ADD PRIMARY KEY (`id_reporte`);

--
-- Indices de la tabla `animales_perdidos`
--
ALTER TABLE `animales_perdidos`
  ADD PRIMARY KEY (`id_reporte`),
  ADD KEY `fk_perdidos_animal` (`id_animal`);

--
-- Indices de la tabla `avisos_encontrado`
--
ALTER TABLE `avisos_encontrado`
  ADD PRIMARY KEY (`id_aviso`),
  ADD KEY `fk_aviso_perro` (`id_perro`);

--
-- Indices de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD PRIMARY KEY (`id_bitacora`);

--
-- Indices de la tabla `campanas_atendidos`
--
ALTER TABLE `campanas_atendidos`
  ADD PRIMARY KEY (`id_atendido`),
  ADD KEY `fk_atendido_campana` (`id_campana`),
  ADD KEY `fk_atendido_perro` (`id_perro`);

--
-- Indices de la tabla `campanas_esterilizacion`
--
ALTER TABLE `campanas_esterilizacion`
  ADD PRIMARY KEY (`id_campania`);

--
-- Indices de la tabla `campanias_esterilizacion`
--
ALTER TABLE `campanias_esterilizacion`
  ADD PRIMARY KEY (`id_campania`);

--
-- Indices de la tabla `duenos`
--
ALTER TABLE `duenos`
  ADD PRIMARY KEY (`id_dueno`),
  ADD UNIQUE KEY `usuario_portal` (`usuario_portal`);

--
-- Indices de la tabla `esterilizaciones`
--
ALTER TABLE `esterilizaciones`
  ADD PRIMARY KEY (`id_esterilizacion`),
  ADD KEY `fk_ester_animal` (`id_animal`),
  ADD KEY `fk_ester_campania` (`id_campania`);

--
-- Indices de la tabla `historial_veterinario`
--
ALTER TABLE `historial_veterinario`
  ADD PRIMARY KEY (`id_consulta`),
  ADD KEY `fk_historial_animal` (`id_animal`),
  ADD KEY `fk_historial_veterinario` (`id_veterinario`);

--
-- Indices de la tabla `lecturas_rfid`
--
ALTER TABLE `lecturas_rfid`
  ADD PRIMARY KEY (`id_lectura`),
  ADD KEY `fk_lecturas_tag` (`id_tag`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id_notificacion`),
  ADD KEY `fk_notif_dueno` (`id_dueno`),
  ADD KEY `fk_notif_perro` (`id_perro`);

--
-- Indices de la tabla `perros`
--
ALTER TABLE `perros`
  ADD PRIMARY KEY (`id_perro`),
  ADD UNIQUE KEY `token_publico` (`token_publico`),
  ADD KEY `fk_perro_dueno` (`id_dueno`);

--
-- Indices de la tabla `reportes_extravio`
--
ALTER TABLE `reportes_extravio`
  ADD PRIMARY KEY (`id_reporte`),
  ADD KEY `fk_reporte_perro` (`id_perro`);

--
-- Indices de la tabla `tags_rfid`
--
ALTER TABLE `tags_rfid`
  ADD PRIMARY KEY (`id_tag`),
  ADD UNIQUE KEY `codigo_tag` (`codigo_tag`),
  ADD KEY `fk_tags_animal` (`id_animal`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD KEY `fk_usuarios_dueno` (`id_dueno`);

--
-- Indices de la tabla `vacunas`
--
ALTER TABLE `vacunas`
  ADD PRIMARY KEY (`id_vacuna`),
  ADD KEY `fk_vacunas_animal` (`id_animal`),
  ADD KEY `fk_vacuna_veterinario` (`id_veterinario`);

--
-- Indices de la tabla `veterinarios`
--
ALTER TABLE `veterinarios`
  ADD PRIMARY KEY (`id_veterinario`),
  ADD KEY `fk_veterinario_usuario` (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `animales`
--
ALTER TABLE `animales`
  MODIFY `id_animal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `animales_encontrados`
--
ALTER TABLE `animales_encontrados`
  MODIFY `id_reporte` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `animales_perdidos`
--
ALTER TABLE `animales_perdidos`
  MODIFY `id_reporte` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `avisos_encontrado`
--
ALTER TABLE `avisos_encontrado`
  MODIFY `id_aviso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  MODIFY `id_bitacora` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `campanas_atendidos`
--
ALTER TABLE `campanas_atendidos`
  MODIFY `id_atendido` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `campanas_esterilizacion`
--
ALTER TABLE `campanas_esterilizacion`
  MODIFY `id_campania` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `campanias_esterilizacion`
--
ALTER TABLE `campanias_esterilizacion`
  MODIFY `id_campania` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `duenos`
--
ALTER TABLE `duenos`
  MODIFY `id_dueno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `esterilizaciones`
--
ALTER TABLE `esterilizaciones`
  MODIFY `id_esterilizacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `historial_veterinario`
--
ALTER TABLE `historial_veterinario`
  MODIFY `id_consulta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `lecturas_rfid`
--
ALTER TABLE `lecturas_rfid`
  MODIFY `id_lectura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id_notificacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `perros`
--
ALTER TABLE `perros`
  MODIFY `id_perro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT de la tabla `reportes_extravio`
--
ALTER TABLE `reportes_extravio`
  MODIFY `id_reporte` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tags_rfid`
--
ALTER TABLE `tags_rfid`
  MODIFY `id_tag` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `vacunas`
--
ALTER TABLE `vacunas`
  MODIFY `id_vacuna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `veterinarios`
--
ALTER TABLE `veterinarios`
  MODIFY `id_veterinario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `animales`
--
ALTER TABLE `animales`
  ADD CONSTRAINT `fk_animales_dueno` FOREIGN KEY (`id_dueno`) REFERENCES `duenos` (`id_dueno`) ON DELETE CASCADE;

--
-- Filtros para la tabla `animales_perdidos`
--
ALTER TABLE `animales_perdidos`
  ADD CONSTRAINT `fk_perdidos_animal` FOREIGN KEY (`id_animal`) REFERENCES `animales` (`id_animal`) ON DELETE SET NULL;

--
-- Filtros para la tabla `avisos_encontrado`
--
ALTER TABLE `avisos_encontrado`
  ADD CONSTRAINT `fk_aviso_perro` FOREIGN KEY (`id_perro`) REFERENCES `perros` (`id_perro`) ON DELETE CASCADE;

--
-- Filtros para la tabla `campanas_atendidos`
--
ALTER TABLE `campanas_atendidos`
  ADD CONSTRAINT `fk_atendido_campana` FOREIGN KEY (`id_campana`) REFERENCES `campanas_esterilizacion` (`id_campania`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_atendido_perro` FOREIGN KEY (`id_perro`) REFERENCES `perros` (`id_perro`) ON DELETE SET NULL;

--
-- Filtros para la tabla `esterilizaciones`
--
ALTER TABLE `esterilizaciones`
  ADD CONSTRAINT `fk_ester_animal` FOREIGN KEY (`id_animal`) REFERENCES `animales` (`id_animal`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ester_campania` FOREIGN KEY (`id_campania`) REFERENCES `campanias_esterilizacion` (`id_campania`) ON DELETE SET NULL;

--
-- Filtros para la tabla `historial_veterinario`
--
ALTER TABLE `historial_veterinario`
  ADD CONSTRAINT `fk_historial_animal` FOREIGN KEY (`id_animal`) REFERENCES `perros` (`id_perro`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_historial_veterinario` FOREIGN KEY (`id_veterinario`) REFERENCES `veterinarios` (`id_veterinario`) ON DELETE SET NULL;

--
-- Filtros para la tabla `lecturas_rfid`
--
ALTER TABLE `lecturas_rfid`
  ADD CONSTRAINT `fk_lecturas_tag` FOREIGN KEY (`id_tag`) REFERENCES `tags_rfid` (`id_tag`) ON DELETE CASCADE;

--
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `fk_notif_dueno` FOREIGN KEY (`id_dueno`) REFERENCES `duenos` (`id_dueno`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_notif_perro` FOREIGN KEY (`id_perro`) REFERENCES `perros` (`id_perro`) ON DELETE SET NULL;

--
-- Filtros para la tabla `perros`
--
ALTER TABLE `perros`
  ADD CONSTRAINT `fk_perro_dueno` FOREIGN KEY (`id_dueno`) REFERENCES `duenos` (`id_dueno`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reportes_extravio`
--
ALTER TABLE `reportes_extravio`
  ADD CONSTRAINT `fk_reporte_perro` FOREIGN KEY (`id_perro`) REFERENCES `perros` (`id_perro`) ON DELETE SET NULL;

--
-- Filtros para la tabla `tags_rfid`
--
ALTER TABLE `tags_rfid`
  ADD CONSTRAINT `fk_tags_animal` FOREIGN KEY (`id_animal`) REFERENCES `perros` (`id_perro`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_dueno` FOREIGN KEY (`id_dueno`) REFERENCES `duenos` (`id_dueno`) ON DELETE SET NULL;

--
-- Filtros para la tabla `vacunas`
--
ALTER TABLE `vacunas`
  ADD CONSTRAINT `fk_vacuna_veterinario` FOREIGN KEY (`id_veterinario`) REFERENCES `veterinarios` (`id_veterinario`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_vacunas_animal` FOREIGN KEY (`id_animal`) REFERENCES `perros` (`id_perro`) ON DELETE CASCADE;

--
-- Filtros para la tabla `veterinarios`
--
ALTER TABLE `veterinarios`
  ADD CONSTRAINT `fk_veterinario_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
