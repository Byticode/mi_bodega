-- ==============================================================================
-- SISTEMA DE INVENTARIO Y VENTAS POS "MI BODEGA" (VENEZUELA)
-- Esquema (MySQL compatible)
-- ==============================================================================

CREATE DATABASE IF NOT EXISTS mi_bodega CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mi_bodega;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS ventas_idempotency;
DROP TABLE IF EXISTS ventas;
DROP TABLE IF EXISTS tasa_moneda;
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS clientes;
DROP TABLE IF EXISTS usuarios;

SET FOREIGN_KEY_CHECKS = 1;

-- ==============================================================================
-- 1. USUARIOS
-- ==============================================================================
CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  nombre VARCHAR(250),
  rol VARCHAR(50) NOT NULL DEFAULT 'vendedor',
  status VARCHAR(50) NOT NULL DEFAULT 'activo',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================================================
-- 2. CLIENTES
-- ==============================================================================
CREATE TABLE clientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cedula VARCHAR(50) NOT NULL UNIQUE,
  nombre VARCHAR(250),
  telefono VARCHAR(50),
  email VARCHAR(250),
  status VARCHAR(50) NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================================================
-- 3. PRODUCTOS
-- ==============================================================================
CREATE TABLE productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(250),
  precio_usd DECIMAL(12,2),
  stock INT,
  categoria VARCHAR(250),
  status VARCHAR(50) NOT NULL DEFAULT 'disponible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================================================
-- 4. TASA DE MONEDA
-- ==============================================================================
CREATE TABLE tasa_moneda (
  id INT AUTO_INCREMENT PRIMARY KEY,
  moneda VARCHAR(50) DEFAULT 'Bs',
  tasa_usd DECIMAL(12,6),
  tasa_euro DECIMAL(12,6),
  tasa_paralelo DECIMAL(12,6),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================================================
-- 5. VENTAS
-- ==============================================================================
CREATE TABLE ventas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  clientes_id INT NOT NULL,
  productos_id INT NOT NULL,
  tasa_moneda_id INT NOT NULL,
  cantidad INT NOT NULL DEFAULT 1,
  precio_unitario DECIMAL(12,2) NOT NULL,
  total DECIMAL(14,2) GENERATED ALWAYS AS (cantidad * precio_unitario) STORED,
  status VARCHAR(50) NOT NULL DEFAULT 'no_pagada',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_c_compra FOREIGN KEY (clientes_id) REFERENCES clientes(id) ON DELETE RESTRICT,
  CONSTRAINT fk_p_venta FOREIGN KEY (productos_id) REFERENCES productos(id) ON DELETE RESTRICT,
  CONSTRAINT fk_tasas_cambio FOREIGN KEY (tasa_moneda_id) REFERENCES tasa_moneda(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================================================
-- 6. VENTAS IDEMPOTENCY
-- ==============================================================================
CREATE TABLE ventas_idempotency (
  key_id VARCHAR(255) PRIMARY KEY, -- Renamed 'key' to 'key_id' as 'key' is a reserved word in MySQL
  venta_id INT,
  status VARCHAR(50),
  response JSON, -- Changed JSONB to JSON for MySQL
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================================================
-- 7. ÍNDICES
-- ==============================================================================
CREATE INDEX idx_ventas_clientes_id ON ventas (clientes_id);
CREATE INDEX idx_ventas_productos_id ON ventas (productos_id);
CREATE INDEX idx_ventas_tasa_moneda_id ON ventas (tasa_moneda_id);
CREATE INDEX idx_ventas_created_at ON ventas (created_at);
CREATE INDEX idx_ventas_status_created_at ON ventas (status, created_at);
CREATE INDEX idx_productos_categoria ON productos (categoria);
CREATE INDEX idx_productos_precio_usd ON productos (precio_usd);
CREATE INDEX idx_clientes_nombre ON clientes (nombre);

-- ==============================================================================
-- 8. DATOS INICIALES MÍNIMOS (SEED DATA)
-- ==============================================================================
INSERT INTO usuarios (username, password, nombre, rol, status) VALUES
('admin', '$2y$10$e8w6y...HASH_DE_CONTRASEÑA...', 'Administrador', 'administrador', 'activo'); -- Reemplaza el HASH_DE_CONTRASEÑA con uno real

INSERT INTO clientes (cedula, nombre, status) VALUES
('V-00000000', 'Cliente General', 'activo');

INSERT INTO productos (nombre, precio_usd, stock, categoria, status) VALUES
('Producto Generico 1', 10.50, 100, 'Electronica', 'disponible'),
('Producto Generico 2', 5.25, 50, 'Alimentos', 'disponible');

INSERT INTO tasa_moneda (moneda, tasa_usd, tasa_euro, tasa_paralelo) VALUES
('Bs', 36.000000, 40.000000, 38.000000);
