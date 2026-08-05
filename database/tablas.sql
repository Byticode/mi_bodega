-- ==============================================================================
-- SISTEMA DE INVENTARIO Y VENTAS POS "MI BODEGA" (VENEZUELA)
-- Esquema Optimizado con Entradas de Lotes por Proveedor y Kardex
-- ==============================================================================

CREATE DATABASE IF NOT EXISTS mibodega_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mibodega_db;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS ventas_pagos;
DROP TABLE IF EXISTS ventas_detalles;
DROP TABLE IF EXISTS ventas;
DROP TABLE IF EXISTS kardex;
DROP TABLE IF EXISTS lotes;
DROP TABLE IF EXISTS compras;
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS metodos_pago;
DROP TABLE IF EXISTS tasas_cambio;
DROP TABLE IF EXISTS proveedores;
DROP TABLE IF EXISTS clientes;
DROP TABLE IF EXISTS usuarios;

SET FOREIGN_KEY_CHECKS = 1;

-- ==============================================================================
-- 1. USUARIOS Y ROLES
-- ==============================================================================
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(250) NOT NULL UNIQUE,
    clave VARCHAR(255) NOT NULL,
    activo BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================================================
-- 2. CLIENTES Y PROVEEDORES
-- ==============================================================================
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cedula_rif VARCHAR(20) NOT NULL UNIQUE, 
    nombre VARCHAR(150) NOT NULL,
    telefono VARCHAR(30) NULL,
    email VARCHAR(100) NULL,
    descripcion TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rif VARCHAR(20) NOT NULL UNIQUE, 
    nombre VARCHAR(150) NOT NULL,
    telefono VARCHAR(100) NULL,
    email VARCHAR(100) NULL,
    descripcion TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================================================
-- 3. TASAS DE CAMBIO (DÓLAR BCV, PARALELO, EURO)
-- ==============================================================================
CREATE TABLE tasas_cambio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bcv DECIMAL(10,4) NOT NULL,      -- Dólar Oficial Banco Central de Venezuela
    paralelo DECIMAL(10,4) NOT NULL, -- Dólar Paralelo
    euro DECIMAL(10,4) NOT NULL,     -- Euro
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_fecha (fecha DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================================================
-- 4. CATEGORÍAS Y PRODUCTOS
-- ==============================================================================
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE, 
    nombre VARCHAR(150) NOT NULL,
    categoria_id INT NOT NULL,
    unidad VARCHAR(20) NOT NULL DEFAULT 'Unidad', 
    costo_usd DECIMAL(10,4) NOT NULL DEFAULT 0.0000,          
    porcentaje_ganancia DECIMAL(5,2) NOT NULL DEFAULT 30.00,  
    precio_usd DECIMAL(10,2) GENERATED ALWAYS AS (ROUND(costo_usd * (1 + (porcentaje_ganancia / 100)), 2)) STORED, 
    iva_porcentaje DECIMAL(5,2) NOT NULL DEFAULT 16.00,        
    stock_actual DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock_minimo DECIMAL(10,2) NOT NULL DEFAULT 5.00,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id),
    INDEX idx_prod_codigo (codigo),
    INDEX idx_prod_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================================================
-- 5. COMPRAS / ENTRADAS DE MERCANCÍA (PROVEEDORES)
-- Encabezado del documento de entrada de mercancía
-- ==============================================================================
CREATE TABLE compras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    proveedor_id INT NOT NULL,
    numero_factura VARCHAR(50) NOT NULL, 
    fecha_compra DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    tasa_bcv DECIMAL(10,4) NOT NULL,
    total_usd DECIMAL(10,2) NOT NULL,
    usuario_id INT NOT NULL,
    observacion TEXT NULL,
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================================================
-- 6. LOTES DE PRODUCTOS (Generados en cada entrada/compra de proveedor)
-- Permite saber exactamente de qué proveedor y compra proviene cada lote
-- ==============================================================================
CREATE TABLE lotes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    compra_id INT NOT NULL,         
    proveedor_id INT NOT NULL,      
    producto_id INT NOT NULL,       
    numero_lote VARCHAR(50) NOT NULL, 
    fecha_vencimiento DATE NULL,    
    cantidad_ingresada DECIMAL(10,2) NOT NULL, 
    stock_actual DECIMAL(10,2) NOT NULL,       
    costo_usd DECIMAL(10,4) NOT NULL,        
    fecha_ingreso DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    FOREIGN KEY (compra_id) REFERENCES compras(id) ON DELETE CASCADE,
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    INDEX idx_lote_prod (producto_id),
    INDEX idx_lote_vencimiento (fecha_vencimiento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================================================
-- 7. KARDEX (HISTORIAL UNIFICADO DE MOVIMIENTOS DE INVENTARIO)
-- Registra cada entrada por compra de lote o salida por venta de lote
-- ==============================================================================
CREATE TABLE kardex (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    lote_id INT NULL, 
    tipo ENUM('ENTRADA_COMPRA', 'SALIDA_VENTA', 'AJUSTE_MAS', 'AJUSTE_MENOS') NOT NULL,
    cantidad DECIMAL(10,2) NOT NULL,
    costo_usd DECIMAL(10,4) NOT NULL,
    stock_anterior DECIMAL(10,2) NOT NULL,
    stock_nuevo DECIMAL(10,2) NOT NULL,
    referencia VARCHAR(100) NULL, 
    observacion VARCHAR(255) NULL,
    usuario_id INT NULL,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id),
    FOREIGN KEY (lote_id) REFERENCES lotes(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_kardex_prod (producto_id, fecha DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================================================
-- 8. VENTAS POS (FACTURACIÓN MULTIMONEDA CON DESPACHO POR LOTE)
-- ==============================================================================
CREATE TABLE metodos_pago (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL, 
    moneda ENUM('USD', 'VES', 'EUR') NOT NULL DEFAULT 'VES',
    requiere_referencia BOOLEAN NOT NULL DEFAULT FALSE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    correlativo VARCHAR(30) NOT NULL UNIQUE, 
    cliente_id INT NOT NULL,
    usuario_id INT NOT NULL,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    tasa_bcv DECIMAL(10,4) NOT NULL,
    tasa_paralelo DECIMAL(10,4) NULL,
    subtotal_usd DECIMAL(10,2) NOT NULL,
    iva_usd DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_usd DECIMAL(10,2) NOT NULL,
    total_bs DECIMAL(12,2) GENERATED ALWAYS AS (ROUND(total_usd * tasa_bcv, 2)) STORED,
    estado ENUM('COMPLETADA', 'ANULADA') NOT NULL DEFAULT 'COMPLETADA',
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    INDEX idx_ventas_fecha (fecha DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE ventas_detalles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT NOT NULL,
    producto_id INT NOT NULL,
    lote_id INT NOT NULL, 
    cantidad DECIMAL(10,2) NOT NULL,
    precio_usd DECIMAL(10,2) NOT NULL,
    iva_porcentaje DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    subtotal_usd DECIMAL(10,2) GENERATED ALWAYS AS (ROUND(cantidad * precio_usd, 2)) STORED,
    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id),
    FOREIGN KEY (lote_id) REFERENCES lotes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE ventas_pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT NOT NULL,
    metodo_pago_id INT NOT NULL,
    monto_origen DECIMAL(12,2) NOT NULL, 
    tasa_usada DECIMAL(10,4) NOT NULL,
    monto_usd DECIMAL(10,2) NOT NULL,   
    referencia VARCHAR(100) NULL,
    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE,
    FOREIGN KEY (metodo_pago_id) REFERENCES metodos_pago(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================================================
-- 9. VISTA DEL CATÁLOGO DE PRODUCTOS Y SUS LOTES DISPONIBLES POR PROVEEDOR
-- ==============================================================================
CREATE OR REPLACE VIEW vw_lotes_disponibles AS
SELECT 
    l.id AS lote_id,
    l.numero_lote,
    p.codigo AS producto_codigo,
    p.nombre AS producto,
    pr.razon_social AS proveedor,
    l.fecha_vencimiento,
    l.stock_actual AS stock_lote,
    l.costo_usd AS costo_lote_usd,
    p.precio_usd AS precio_base_usd,
    ROUND(p.precio_usd * (1 + (p.iva_porcentaje / 100)), 2) AS precio_pvp_usd,
    COALESCE((SELECT bcv FROM tasas_cambio ORDER BY fecha DESC LIMIT 1), 0) AS tasa_bcv_actual,
    ROUND(
        (p.precio_usd * (1 + (p.iva_porcentaje / 100))) * 
        COALESCE((SELECT bcv FROM tasas_cambio ORDER BY fecha DESC LIMIT 1), 0), 2
    ) AS precio_pvp_bs
FROM lotes l
JOIN productos p ON l.producto_id = p.id
JOIN proveedores pr ON l.proveedor_id = pr.id
WHERE l.stock_actual > 0 AND l.activo = TRUE
ORDER BY l.fecha_vencimiento ASC;

-- ==============================================================================
-- 10. DATOS INICIALES MÍNIMOS (SEED DATA)
-- ==============================================================================
INSERT INTO usuarios (nombre, usuario, clave, rol) VALUES
('Administrador', 'admin', '$2b$10$e8w6y...HASH...', 'ADMINISTRADOR');

INSERT INTO clientes (cedula_rif, nombre) VALUES
('V-00000000', 'CLIENTE GENERAL / MOSTRADOR');

INSERT INTO proveedores (rif, razon_social, contacto) VALUES
('J-00000000-0', 'PROVEEDOR PRINCIPAL', 'Contacto Almacén');

INSERT INTO categorias (nombre) VALUES 
('Viveres'), ('Charcuteria'), ('Bebidas'), ('Limpieza');

INSERT INTO metodos_pago (nombre, moneda, requiere_referencia) VALUES
('Efectivo $', 'USD', FALSE),
('Efectivo Bs', 'VES', FALSE),
('Pago Móvil', 'VES', TRUE),
('Punto de Venta', 'VES', TRUE),
('Zelle $', 'USD', TRUE),
('Efectivo Euro €', 'EUR', FALSE);

INSERT INTO tasas_cambio (bcv, paralelo, euro) VALUES (36.5000, 41.2000, 39.8000);
