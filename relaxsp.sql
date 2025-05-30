-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-05-2025 a las 14:57:03
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
-- Base de datos: `relaxsp`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_turnos`
--

CREATE TABLE `auditoria_turnos` (
  `id` int(11) NOT NULL,
  `turno_id` int(11) DEFAULT NULL,
  `accion` varchar(20) DEFAULT NULL,
  `usuario` varchar(100) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `detalle` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `auditoria_turnos`
--

INSERT INTO `auditoria_turnos` (`id`, `turno_id`, `accion`, `usuario`, `fecha`, `detalle`) VALUES
(1, 12, 'cancelacion_turno', 'admin1', '2025-05-15 21:29:57', 'Turno cancelado: Servicio = Masajes terapéuticos, Fecha = 2025-05-07, Hora = 21:05:00'),
(2, 9, 'cancelacion_turno', 'admin1', '2025-05-15 21:33:31', 'Turno cancelado: Servicio = Masajes terapéuticos, Fecha = 2025-05-15, Hora = 22:23:00'),
(3, 14, 'cancelacion_turno', 'pedro@gmail.com', '2025-05-15 21:45:05', 'Turno cancelado: Servicio = Masajes terapéuticos, Fecha = 2025-05-15, Hora = 22:42:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `usuario_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `email`, `telefono`, `activo`, `usuario_id`) VALUES
(1, 'María Gómez', 'maria@gmail.com', '3624123456', 1, 2),
(2, 'Juan Pérez', 'juanp@gmail.com', '3624987654', 1, 3),
(3, 'Lucía Martínez', 'lucia.martinez@yahoo.com', '3624332211', 1, 4),
(4, 'juana de arco', 'juana78@gmail.com', '', 1, 7),
(5, 'juana de ARcoqq', 'xxxx@gmail.co', '', 1, 8),
(6, 'pedro', 'pedro@gmail.com', '', 1, 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `rol` varchar(50) DEFAULT 'empleado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `nombre`, `email`, `telefono`, `usuario_id`, `rol`) VALUES
(1, 'Laura perez', 'laura@relaxsp.com', '3624111111', 5, 'empleado'),
(2, 'Ana López', 'ana@relaxsp.com', '3624222222', 6, 'empleado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `precio` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id`, `nombre`, `descripcion`, `precio`) VALUES
(1, 'Masajes terapéuticos', 'Relajación muscular profunda', 50.00),
(2, 'Sauna y vapor', 'Desintoxicación corporal en cabina de vapor', 30.00),
(3, 'Masajes terapéuticos', 'Relajación muscular profunda', 50.00),
(4, 'Sauna y vapor', 'Desintoxicación corporal en cabina de vapor', 30.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turnos`
--

CREATE TABLE `turnos` (
  `id` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `id_servicio` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `estado` enum('pendiente','confirmado','cancelado') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `turnos`
--

INSERT INTO `turnos` (`id`, `id_cliente`, `id_empleado`, `id_servicio`, `fecha`, `hora`, `estado`) VALUES
(5, 1, 1, 1, '2025-05-20', '10:00:00', 'pendiente'),
(6, 2, 2, 2, '2025-05-21', '11:30:00', 'pendiente'),
(7, 3, 1, 1, '2025-05-22', '14:00:00', 'confirmado'),
(8, 1, 2, 2, '2025-05-23', '09:00:00', 'cancelado'),
(9, 3, 2, 1, '2025-05-15', '22:23:00', 'cancelado'),
(10, 3, 1, 4, '2025-05-16', '22:41:00', 'pendiente'),
(11, 4, 1, 2, '2025-05-17', '20:52:00', 'pendiente'),
(12, 1, 1, 1, '2025-05-07', '21:05:00', 'cancelado'),
(13, 2, 1, 1, '2025-05-15', '22:03:00', 'pendiente'),
(14, 6, 1, 1, '2025-05-15', '22:42:00', 'cancelado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','cliente','empleado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `password`, `rol`) VALUES
(1, 'admin1', 'admin123', 'admin'),
(2, 'maria', 'maria123', 'cliente'),
(3, 'juanp', 'juan123', 'cliente'),
(4, 'lucia', 'lucia123', 'cliente'),
(5, 'laura@relaxsp.com', 'laura123', 'empleado'),
(6, 'ana@relaxsp.com', 'ana123', 'empleado'),
(7, 'juana78@gmail.com', 'juana1234', 'cliente'),
(8, 'xxxx@gmail.co', 'juana1234', 'cliente'),
(9, 'pedro@gmail.com', 'pedro1234', 'cliente');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `auditoria_turnos`
--
ALTER TABLE `auditoria_turnos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `turno_id` (`turno_id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `turnos`
--
ALTER TABLE `turnos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_empleado` (`id_empleado`),
  ADD KEY `id_servicio` (`id_servicio`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `auditoria_turnos`
--
ALTER TABLE `auditoria_turnos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `turnos`
--
ALTER TABLE `turnos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `auditoria_turnos`
--
ALTER TABLE `auditoria_turnos`
  ADD CONSTRAINT `auditoria_turnos_ibfk_1` FOREIGN KEY (`turno_id`) REFERENCES `turnos` (`id`);

--
-- Filtros para la tabla `turnos`
--
ALTER TABLE `turnos`
  ADD CONSTRAINT `turnos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `turnos_ibfk_2` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`id`),
  ADD CONSTRAINT `turnos_ibfk_3` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
