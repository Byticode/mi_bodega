-- ========================================================
-- Base de Datos: `mi_bodega`
-- Sistema de Gestión de Inventario, Multimoneda y Punto de Venta (POS)
-- ========================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- 1. TABLAS CATÁLOGO / BASE
-- --------------------------------------------------------

--
-- Estructura de tabla para `categorias`
--
CREATE TABLE IF NOT EXISTS `categorias` (
  `categorias_id` int(11) NOT NULL AUTO_INCREMENT,
  `categorias_nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`categorias_id`),
  UNIQUE KEY `categorias_nombre` (`categorias_nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Datos para la tabla `categorias`
--
INSERT INTO `categorias` (`categorias_id`, `categorias_nombre`) VALUES
(1, 'Viveres'),
(2, 'Carnes'),
(3, 'Lacteos'),
(4, 'Dulces'),
(6, 'Bebidas'),
(8, 'Embutidos')
ON DUPLICATE KEY UPDATE `categorias_nombre` = VALUES(`categorias_nombre`);

-- --------------------------------------------------------

--
-- Estructura de tabla para `unidades`
--
CREATE TABLE IF NOT EXISTS `unidades` (
  `unidad_id` int(11) NOT NULL AUTO_INCREMENT,
  `unidad_nombre` varchar(50) NOT NULL,
  `unidad_abreviatura` varchar(10) NOT NULL,
  PRIMARY KEY (`unidad_id`),
  UNIQUE KEY `unidad_nombre` (`unidad_nombre`),
  UNIQUE KEY `unidad_abreviatura` (`unidad_abreviatura`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Datos para la tabla `unidades`
--
INSERT INTO `unidades` (`unidad_id`, `unidad_nombre`, `unidad_abreviatura`) VALUES
(1, 'Unidad', 'Ud'),
(2, 'Kilogramo', 'Kg'),
(3, 'Gramo', 'g'),
(4, 'Litro', 'L'),
(5, 'Mililitro', 'mL'),
(6, 'Caja', 'Cj')
ON DUPLICATE KEY UPDATE `unidad_nombre` = VALUES(`unidad_nombre`), `unidad_abreviatura` = VALUES(`unidad_abreviatura`);

-- --------------------------------------------------------

--
-- Estructura de tabla para `proveedores`
--
CREATE TABLE IF NOT EXISTS `proveedores` (
  `proveedor_id` int(11) NOT NULL AUTO_INCREMENT,
  `proveedor_nombre` varchar(255) NOT NULL,
  `proveedor_telefono` varchar(20) NOT NULL,
  PRIMARY KEY (`proveedor_id`),
  UNIQUE KEY `proveedor_nombre` (`proveedor_nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Datos para la tabla `proveedores`
--
INSERT INTO `proveedores` (`proveedor_id`, `proveedor_nombre`, `proveedor_telefono`) VALUES
(1, 'Ebenezer', '04221234567'),
(2, 'Distribuidora quesos', '0412000000'),
(3, 'Empresas polar', '02540000000')
ON DUPLICATE KEY UPDATE `proveedor_nombre` = VALUES(`proveedor_nombre`);

-- --------------------------------------------------------

--
-- Estructura de tabla para `clientes`
--
CREATE TABLE IF NOT EXISTS `clientes` (
  `cliente_id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_nombre` varchar(100) NOT NULL,
  `cliente_apellido` varchar(100) NOT NULL,
  `cliente_cedula` varchar(50) NOT NULL,
  `cliente_telefono` varchar(20) DEFAULT NULL,
  `cliente_correo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`cliente_id`),
  UNIQUE KEY `cliente_cedula` (`cliente_cedula`),
  UNIQUE KEY `cliente_correo` (`cliente_correo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Datos para la tabla `clientes`
--
INSERT INTO `clientes` (`cliente_id`, `cliente_nombre`, `cliente_apellido`, `cliente_cedula`, `cliente_telefono`, `cliente_correo`) VALUES
(1, 'Consumidor', 'General', '12345670', NULL, NULL)
ON DUPLICATE KEY UPDATE `cliente_nombre` = VALUES(`cliente_nombre`);

-- --------------------------------------------------------

--
-- Estructura de tabla para `usuarios`
--
CREATE TABLE IF NOT EXISTS `usuarios` (
  `usuario_id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_nombre` varchar(100) NOT NULL,
  `usuario_username` varchar(50) NOT NULL,
  `usuario_clave` varchar(255) NOT NULL,
  `usuario_rol` enum('admin','vendedor') NOT NULL DEFAULT 'vendedor',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`usuario_id`),
  UNIQUE KEY `usuario_username` (`usuario_username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Datos para la tabla `usuarios`
--
INSERT INTO `usuarios` (`usuario_id`, `usuario_nombre`, `usuario_username`, `usuario_clave`, `usuario_rol`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, '2026-08-06 04:00:00', '2026-08-06 04:00:00')
ON DUPLICATE KEY UPDATE `usuario_username` = VALUES(`usuario_username`);

-- --------------------------------------------------------

--
-- Estructura de tabla para `tasa_moneda`
--
CREATE TABLE IF NOT EXISTS `tasa_moneda` (
  `tasa_id` int(11) NOT NULL AUTO_INCREMENT,
  `moneda` varchar(50) NOT NULL DEFAULT 'Bs',
  `tasa_usd` decimal(12,6) DEFAULT NULL,
  `tasa_euro` decimal(12,6) DEFAULT NULL,
  `tasa_paralelo` decimal(12,6) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`tasa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Datos para la tabla `tasa_moneda`
--
INSERT INTO `tasa_moneda` (`tasa_id`, `moneda`, `tasa_usd`, `tasa_euro`, `tasa_paralelo`, `created_at`, `updated_at`) VALUES
(1, 'Bs', 65.000000, 70.000000, 68.000000, '2026-08-06 04:00:00', '2026-08-06 04:00:00')
ON DUPLICATE KEY UPDATE `tasa_usd` = VALUES(`tasa_usd`);

-- --------------------------------------------------------
-- 2. TABLA PRINCIPAL DE PRODUCTOS E INVENTARIO
-- --------------------------------------------------------

--
-- Estructura de tabla para `productos`
--
CREATE TABLE IF NOT EXISTS `productos` (
  `producto_id` int(11) NOT NULL AUTO_INCREMENT,
  `producto_codigo` varchar(50) DEFAULT NULL,
  `producto_nombre` varchar(255) NOT NULL,
  `producto_peso` decimal(10,2) DEFAULT NULL,
  `categoria_id` int(11) NOT NULL,
  `unidad_id` int(11) NOT NULL,
  `producto_precio_costo` decimal(12,2) NOT NULL DEFAULT 0.00,
  `producto_ganancia` decimal(5,2) NOT NULL DEFAULT 30.00,
  `producto_iva` decimal(5,2) NOT NULL DEFAULT 16.00,
  `producto_precio_venta` decimal(12,2) NOT NULL,
  `producto_stock` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`producto_id`),
  UNIQUE KEY `producto_nombre` (`producto_nombre`),
  UNIQUE KEY `producto_codigo` (`producto_codigo`),
  KEY `categoria_id` (`categoria_id`),
  KEY `unidad_id` (`unidad_id`),
  CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`categorias_id`) ON DELETE CASCADE,
  CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`unidad_id`) REFERENCES `unidades` (`unidad_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Datos para la tabla `productos`
--
INSERT INTO `productos` (`producto_id`, `producto_codigo`, `producto_nombre`, `producto_peso`, `categoria_id`, `unidad_id`, `producto_precio_costo`, `producto_ganancia`, `producto_iva`, `producto_precio_venta`, `producto_stock`) VALUES
(1, '123123', 'Refresco Fanta Toronja 2L', 2.00, 6, 4, 3.32, 30.00, 16.00, 5.00, 9),
(2, NULL, 'Mortadela De Pollo 1Kg', 1.00, 8, 2, 1.99, 30.00, 16.00, 3.00, 0)
ON DUPLICATE KEY UPDATE `producto_precio_venta` = VALUES(`producto_precio_venta`);

-- --------------------------------------------------------
-- 3. TRANSACCIONES: SURTIDO Y COMPRAS
-- --------------------------------------------------------

--
-- Estructura de tabla para `surtidos`
--
CREATE TABLE IF NOT EXISTS `surtidos` (
  `surtido_id` int(11) NOT NULL AUTO_INCREMENT,
  `proveedor_id` int(11) NOT NULL,
  `surtido_costo_total` decimal(12,2) NOT NULL,
  `surtido_fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`surtido_id`),
  KEY `proveedor_id` (`proveedor_id`),
  CONSTRAINT `surtidos_ibfk_1` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`proveedor_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Datos para la tabla `surtidos`
--
INSERT INTO `surtidos` (`surtido_id`, `proveedor_id`, `surtido_costo_total`, `surtido_fecha`, `created_at`) VALUES
(1, 3, 10.00, '2026-08-06 05:54:57', '2026-08-06 05:54:57'),
(2, 1, 5.00, '2026-08-06 06:28:15', '2026-08-06 06:28:15')
ON DUPLICATE KEY UPDATE `surtido_costo_total` = VALUES(`surtido_costo_total`);

--
-- Estructura de tabla para `surtido_detalles`
--
CREATE TABLE IF NOT EXISTS `surtido_detalles` (
  `detalle_id` int(11) NOT NULL AUTO_INCREMENT,
  `surtido_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `detalle_cantidad` int(11) NOT NULL,
  `detalle_precio_costo` decimal(12,2) NOT NULL,
  `detalle_subtotal` decimal(12,2) NOT NULL,
  PRIMARY KEY (`detalle_id`),
  KEY `surtido_id` (`surtido_id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `surtido_detalles_ibfk_1` FOREIGN KEY (`surtido_id`) REFERENCES `surtidos` (`surtido_id`) ON DELETE CASCADE,
  CONSTRAINT `surtido_detalles_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`producto_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Datos para la tabla `surtido_detalles`
--
INSERT INTO `surtido_detalles` (`detalle_id`, `surtido_id`, `producto_id`, `detalle_cantidad`, `detalle_precio_costo`, `detalle_subtotal`) VALUES
(1, 1, 1, 10, 1.00, 10.00),
(2, 2, 2, 5, 1.00, 5.00)
ON DUPLICATE KEY UPDATE `detalle_subtotal` = VALUES(`detalle_subtotal`);

-- --------------------------------------------------------
-- 4. TRANSACCIONES: VENTAS Y POS
-- --------------------------------------------------------

--
-- Estructura de tabla para `ventas`
--
CREATE TABLE IF NOT EXISTS `ventas` (
  `venta_id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `tasa_id` int(11) NOT NULL,
  `venta_total` decimal(12,2) NOT NULL,
  `venta_numero_pago` varchar(50) DEFAULT NULL,
  `venta_metodo_pago` enum('efectivo','transferencia','pago_movil','biopago','cashea') DEFAULT NULL,
  `venta_estado` enum('completada','pendiente','cancelada') NOT NULL DEFAULT 'completada',
  `venta_fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`venta_id`),
  KEY `cliente_id` (`cliente_id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `tasa_id` (`tasa_id`),
  CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`cliente_id`) ON DELETE SET NULL,
  CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE CASCADE,
  CONSTRAINT `ventas_ibfk_3` FOREIGN KEY (`tasa_id`) REFERENCES `tasa_moneda` (`tasa_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Datos para la tabla `ventas`
--
INSERT INTO `ventas` (`venta_id`, `cliente_id`, `usuario_id`, `tasa_id`, `venta_total`, `venta_numero_pago`, `venta_metodo_pago`, `venta_estado`, `venta_fecha`, `created_at`) VALUES
(1, NULL, 1, 1, 12.00, '', 'biopago', 'completada', '2026-08-06 06:30:01', '2026-08-06 06:30:01'),
(2, NULL, 1, 1, 3.00, '', 'efectivo', 'completada', '2026-08-06 06:30:58', '2026-08-06 06:30:58')
ON DUPLICATE KEY UPDATE `venta_total` = VALUES(`venta_total`);

--
-- Estructura de tabla para `venta_detalles`
--
CREATE TABLE IF NOT EXISTS `venta_detalles` (
  `detalle_id` int(11) NOT NULL AUTO_INCREMENT,
  `venta_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `detalle_cantidad` int(11) NOT NULL,
  `detalle_precio_unitario` decimal(12,2) NOT NULL,
  `detalle_subtotal` decimal(12,2) NOT NULL,
  PRIMARY KEY (`detalle_id`),
  KEY `venta_id` (`venta_id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `venta_detalles_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`venta_id`) ON DELETE CASCADE,
  CONSTRAINT `venta_detalles_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`producto_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Datos para la tabla `venta_detalles`
--
INSERT INTO `venta_detalles` (`detalle_id`, `venta_id`, `producto_id`, `detalle_cantidad`, `detalle_precio_unitario`, `detalle_subtotal`) VALUES
(1, 1, 2, 4, 3.00, 12.00),
(2, 2, 2, 1, 3.00, 3.00)
ON DUPLICATE KEY UPDATE `detalle_subtotal` = VALUES(`detalle_subtotal`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
