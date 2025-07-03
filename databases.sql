-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-07-2025 a las 06:01:44
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
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) COLLATE utf8_spanish2_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `expires_at`, `used`, `created_at`) VALUES
(6, 13, 'f148bb19dbe57841ae8bf4419d3ab8aee6cafc45ccd2db35ff3971d1e0e02192', '2025-06-28 04:03:22', 1, '2025-06-28 01:03:22'),
(7, 13, '833488c9966cc63f3b00814f784000a04a796e15c6413307658f51ef3c22213e', '2025-06-28 04:51:04', 0, '2025-06-28 01:51:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_spanish_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_spanish_ci NOT NULL,
  `correo` varchar(100) COLLATE utf8mb4_spanish_ci NOT NULL,
  `cedula` varchar(12) COLLATE utf8mb4_spanish_ci NOT NULL,
  `pnf` varchar(100) COLLATE utf8mb4_spanish_ci NOT NULL,
  `trayecto` varchar(10) COLLATE utf8mb4_spanish_ci NOT NULL,
  `rol` varchar(7) COLLATE utf8mb4_spanish_ci NOT NULL DEFAULT 'usuario',
  `clave` varchar(300) COLLATE utf8mb4_spanish_ci NOT NULL,
  `fecha_registro` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `telefono`, `correo`, `cedula`, `pnf`, `trayecto`, `rol`, `clave`, `fecha_registro`) VALUES
(7, 'Mario Navaz', '23454353', 'mario@gmail.com', '', '', '', 'usuario', '$2y$10$Hzha0g/Y5AO./abkJMAbJes7hCM2pp7EMfP8qGI.q8Sj3MnTG89G6', '2025-05-29 19:04:24'),
(8, 'layka matilda', '2147483647', 'layka@gmail.com', '', '', '', 'usuario', '$2y$10$3e7bVUVp1/8/Xye/.x50POWvDrlmwZaS4FzG5Gq.crcV2QPtAGkIe', '2025-06-15 19:06:12'),
(9, 'layka matilda', '2147483647', 'layka1@gmail.com', '', '', '', 'usuario', '$2y$10$IRx2NaEYrogDCNOgtC/42OEd9FyrOu/PYaYvfA8ji81tPQ23DSb32', '2025-06-15 19:08:16'),
(10, 'layka matilda', '2147483647', 'layka2@gmail.com', '', '', '', 'usuario', '$2y$10$LK98DiXPtSBKnSLe0/lGbO3dMAWtA2aQ0oWLvC3NWkQbh65H0jGUK', '2025-06-15 19:10:19'),
(11, 'felipe', '2147483647', 'f@gmail.com', '', '', '', 'usuario', '$2y$10$aiTQ4D2noihmbHXVpRQPyub/LNoPl4vpIuO6Arb2tqlMg.AvNhTly', '2025-06-15 19:17:40'),
(12, 'mateo', '+584247659284', 'mateo@gmail.com', '32342342', 'Mecanica', '1', 'usuario', '$2y$10$y45oEl5MCqMLJlyn/.ZRuOnuzgnSPxPdbKsGj2/wKHIWAmKXVtYH6', '2025-06-15 19:22:20'),
(13, 'Jose Rivero', '+584129219793', 'riveroviloria2@gmail.com', '3169534998', 'Informatica', '2', 'admin', '$2y$10$y45oEl5MCqMLJlyn/.ZRuOnuzgnSPxPdbKsGj2/wKHIWAmKXVtYH6', '2025-06-18 13:09:58'),
(14, 'bryant moreno', '+584122716725', '30723503', '30723503', 'Informatica', '2', 'usuario', '$2y$10$0emLSnMEPgnpDQAe3G3jn.UVw0EuBKofM.1sYEAB7WzBnal95MxPO', '2025-07-01 11:41:45'),
(15, 'daniel', '+584245689858', '31585421', '31585421', 'Administracion', '4', 'usuario', '$2y$10$X0ajnZf22xXopqeLsWu8JeMb3.vG06TEN5mTpPGAbkr7PcrKgSHaO', '2025-07-01 11:48:49');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
