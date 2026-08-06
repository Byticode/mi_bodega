-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 06, 2026 at 06:28 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mi_bodega`
--

-- --------------------------------------------------------

--
-- Table structure for table `categorias`
--

CREATE TABLE `categorias` (
  `categorias_id` int(11) NOT NULL,
  `categorias_nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categorias`
--

INSERT INTO `categorias` (`categorias_id`, `categorias_nombre`) VALUES
(6, 'Bebidas'),
(2, 'Carnes'),
(4, 'Dulces'),
(3, 'Lacteos'),
(5, 'Otra cosa'),
(7, 'Qweqweqweqw'),
(1, 'Viveres');

-- --------------------------------------------------------

--
-- Table structure for table `clientes`
--

CREATE TABLE `clientes` (
  `cliente_id` int(11) NOT NULL,
  `cliente_nombre` varchar(100) NOT NULL,
  `cliente_apellido` varchar(100) NOT NULL,
  `cliente_cedula` varchar(50) NOT NULL,
  `cliente_telefono` varchar(20) DEFAULT NULL,
  `cliente_correo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clientes`
--

INSERT INTO `clientes` (`cliente_id`, `cliente_nombre`, `cliente_apellido`, `cliente_cedula`, `cliente_telefono`, `cliente_correo`) VALUES
(1, 'Werwer', 'Werwer', '12345670', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `productos`
--

CREATE TABLE `productos` (
  `producto_id` int(11) NOT NULL,
  `producto_codigo` varchar(50) DEFAULT NULL,
  `producto_nombre` varchar(255) NOT NULL,
  `producto_peso` decimal(10,2) DEFAULT NULL,
  `categoria_id` int(11) NOT NULL,
  `unidad_id` int(11) NOT NULL,
  `producto_precio_venta` decimal(12,2) NOT NULL,
  `producto_stock` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `productos`
--

INSERT INTO `productos` (`producto_id`, `producto_codigo`, `producto_nombre`, `producto_peso`, `categoria_id`, `unidad_id`, `producto_precio_venta`, `producto_stock`) VALUES
(1, NULL, 'Refresco Fanta Toronja 2LT', 2.00, 6, 4, 15.00, 0.00),
(2, NULL, 'Harina PAN 1KG', 1.00, 1, 2, 12.00, 0.00),
(3, NULL, 'Refresco Fanta Toronja 1lt', 1.00, 6, 4, 10.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `proveedores`
--

CREATE TABLE `proveedores` (
  `proveedor_id` int(11) NOT NULL,
  `proveedor_nombre` varchar(255) NOT NULL,
  `proveedor_telefono` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `proveedores`
--

INSERT INTO `proveedores` (`proveedor_id`, `proveedor_nombre`, `proveedor_telefono`) VALUES
(1, 'Hermanos queso', '04221234567');

-- --------------------------------------------------------

--
-- Table structure for table `surtidos`
--

CREATE TABLE `surtidos` (
  `surtido_id` int(11) NOT NULL,
  `proveedor_id` int(11) NOT NULL,
  `surtido_costo_total` decimal(12,2) NOT NULL,
  `surtido_fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `surtido_detalles`
--

CREATE TABLE `surtido_detalles` (
  `detalle_id` int(11) NOT NULL,
  `surtido_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `detalle_cantidad` decimal(12,2) NOT NULL,
  `detalle_precio_costo` decimal(12,2) NOT NULL,
  `detalle_subtotal` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tasa_moneda`
--

CREATE TABLE `tasa_moneda` (
  `tasa_id` int(11) NOT NULL,
  `moneda` varchar(50) NOT NULL DEFAULT 'Bs',
  `tasa_usd` decimal(12,6) DEFAULT NULL,
  `tasa_euro` decimal(12,6) DEFAULT NULL,
  `tasa_paralelo` decimal(12,6) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasa_moneda`
--

INSERT INTO `tasa_moneda` (`tasa_id`, `moneda`, `tasa_usd`, `tasa_euro`, `tasa_paralelo`, `created_at`, `updated_at`) VALUES
(1, 'Bs', 65.000000, 70.000000, 68.000000, '2026-08-06 04:00:00', '2026-08-06 04:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `unidades`
--

CREATE TABLE `unidades` (
  `unidad_id` int(11) NOT NULL,
  `unidad_nombre` varchar(50) NOT NULL,
  `unidad_abreviatura` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `unidades`
--

INSERT INTO `unidades` (`unidad_id`, `unidad_nombre`, `unidad_abreviatura`) VALUES
(1, 'Unidad', 'Ud'),
(2, 'Kilogramo', 'Kg'),
(3, 'Gramo', 'g'),
(4, 'Litro', 'L'),
(5, 'Mililitro', 'mL'),
(6, 'Caja', 'Cj');

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `usuario_id` int(11) NOT NULL,
  `usuario_nombre` varchar(100) NOT NULL,
  `usuario_username` varchar(50) NOT NULL,
  `usuario_clave` varchar(255) NOT NULL,
  `usuario_rol` enum('admin','vendedor') NOT NULL DEFAULT 'vendedor',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`usuario_id`, `usuario_nombre`, `usuario_username`, `usuario_clave`, `usuario_rol`, `created_at`, `updated_at`) VALUES
(1, 'Administrador', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2026-08-06 04:00:00', '2026-08-06 04:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `ventas`
--

CREATE TABLE `ventas` (
  `venta_id` int(11) NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `tasa_id` int(11) NOT NULL,
  `venta_total` decimal(12,2) NOT NULL,
  `venta_numero_pago` varchar(50) DEFAULT NULL,
  `venta_metodo_pago` enum('efectivo','transferencia','pago_movil','biopago','cashea') DEFAULT NULL,
  `venta_estado` enum('completada','pendiente','cancelada') NOT NULL DEFAULT 'completada',
  `venta_fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `venta_detalles`
--

CREATE TABLE `venta_detalles` (
  `detalle_id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `detalle_cantidad` decimal(12,2) NOT NULL,
  `detalle_precio_unitario` decimal(12,2) NOT NULL,
  `detalle_subtotal` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`categorias_id`),
  ADD UNIQUE KEY `categorias_nombre` (`categorias_nombre`);

--
-- Indexes for table `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`cliente_id`),
  ADD UNIQUE KEY `cliente_cedula` (`cliente_cedula`),
  ADD UNIQUE KEY `cliente_correo` (`cliente_correo`);

--
-- Indexes for table `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`producto_id`),
  ADD UNIQUE KEY `producto_nombre` (`producto_nombre`),
  ADD UNIQUE KEY `producto_codigo` (`producto_codigo`),
  ADD KEY `categoria_id` (`categoria_id`),
  ADD KEY `unidad_id` (`unidad_id`);

--
-- Indexes for table `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`proveedor_id`),
  ADD UNIQUE KEY `proveedor_nombre` (`proveedor_nombre`);

--
-- Indexes for table `surtidos`
--
ALTER TABLE `surtidos`
  ADD PRIMARY KEY (`surtido_id`),
  ADD KEY `proveedor_id` (`proveedor_id`);

--
-- Indexes for table `surtido_detalles`
--
ALTER TABLE `surtido_detalles`
  ADD PRIMARY KEY (`detalle_id`),
  ADD KEY `surtido_id` (`surtido_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indexes for table `tasa_moneda`
--
ALTER TABLE `tasa_moneda`
  ADD PRIMARY KEY (`tasa_id`);

--
-- Indexes for table `unidades`
--
ALTER TABLE `unidades`
  ADD PRIMARY KEY (`unidad_id`),
  ADD UNIQUE KEY `unidad_nombre` (`unidad_nombre`),
  ADD UNIQUE KEY `unidad_abreviatura` (`unidad_abreviatura`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`usuario_id`),
  ADD UNIQUE KEY `usuario_username` (`usuario_username`);

--
-- Indexes for table `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`venta_id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `tasa_id` (`tasa_id`);

--
-- Indexes for table `venta_detalles`
--
ALTER TABLE `venta_detalles`
  ADD PRIMARY KEY (`detalle_id`),
  ADD KEY `venta_id` (`venta_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categorias`
--
ALTER TABLE `categorias`
  MODIFY `categorias_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `clientes`
--
ALTER TABLE `clientes`
  MODIFY `cliente_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `productos`
--
ALTER TABLE `productos`
  MODIFY `producto_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `proveedor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `surtidos`
--
ALTER TABLE `surtidos`
  MODIFY `surtido_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `surtido_detalles`
--
ALTER TABLE `surtido_detalles`
  MODIFY `detalle_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tasa_moneda`
--
ALTER TABLE `tasa_moneda`
  MODIFY `tasa_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `unidades`
--
ALTER TABLE `unidades`
  MODIFY `unidad_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `usuario_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ventas`
--
ALTER TABLE `ventas`
  MODIFY `venta_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `venta_detalles`
--
ALTER TABLE `venta_detalles`
  MODIFY `detalle_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`categorias_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`unidad_id`) REFERENCES `unidades` (`unidad_id`) ON DELETE CASCADE;

--
-- Constraints for table `surtidos`
--
ALTER TABLE `surtidos`
  ADD CONSTRAINT `surtidos_ibfk_1` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`proveedor_id`) ON DELETE CASCADE;

--
-- Constraints for table `surtido_detalles`
--
ALTER TABLE `surtido_detalles`
  ADD CONSTRAINT `surtido_detalles_ibfk_1` FOREIGN KEY (`surtido_id`) REFERENCES `surtidos` (`surtido_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `surtido_detalles_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`producto_id`) ON DELETE CASCADE;

--
-- Constraints for table `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`cliente_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ventas_ibfk_3` FOREIGN KEY (`tasa_id`) REFERENCES `tasa_moneda` (`tasa_id`) ON DELETE CASCADE;

--
-- Constraints for table `venta_detalles`
--
ALTER TABLE `venta_detalles`
  ADD CONSTRAINT `venta_detalles_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`venta_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `venta_detalles_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`producto_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
