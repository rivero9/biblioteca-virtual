-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-06-2025 a las 04:05:27
-- Versión del servidor: 10.1.38-MariaDB
-- Versión de PHP: 7.3.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `databases`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_spanish_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_spanish_ci NOT NULL,
  `correo` varchar(100) COLLATE utf8mb4_spanish_ci NOT NULL,
  `clave` varchar(300) COLLATE utf8mb4_spanish_ci NOT NULL,
  `fecha_registro` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `telefono`, `correo`, `clave`, `fecha_registro`) VALUES
(7, 'Mario Navaz', '23454353', 'mario@gmail.com', '$2y$10$Hzha0g/Y5AO./abkJMAbJes7hCM2pp7EMfP8qGI.q8Sj3MnTG89G6', '2025-05-29 19:04:24'),
(8, 'layka matilda', '2147483647', 'layka@gmail.com', '$2y$10$3e7bVUVp1/8/Xye/.x50POWvDrlmwZaS4FzG5Gq.crcV2QPtAGkIe', '2025-06-15 19:06:12'),
(9, 'layka matilda', '2147483647', 'layka1@gmail.com', '$2y$10$IRx2NaEYrogDCNOgtC/42OEd9FyrOu/PYaYvfA8ji81tPQ23DSb32', '2025-06-15 19:08:16'),
(10, 'layka matilda', '2147483647', 'layka2@gmail.com', '$2y$10$LK98DiXPtSBKnSLe0/lGbO3dMAWtA2aQ0oWLvC3NWkQbh65H0jGUK', '2025-06-15 19:10:19'),
(11, 'felipe', '2147483647', 'f@gmail.com', '$2y$10$aiTQ4D2noihmbHXVpRQPyub/LNoPl4vpIuO6Arb2tqlMg.AvNhTly', '2025-06-15 19:17:40'),
(12, 'mateo jesus', '+584247659284', 'mateo@gmail.com', '$2y$10$y45oEl5MCqMLJlyn/.ZRuOnuzgnSPxPdbKsGj2/wKHIWAmKXVtYH6', '2025-06-15 19:22:20');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
