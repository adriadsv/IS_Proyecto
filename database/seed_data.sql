-- ============================================================
-- Script para cargar datos de prueba - Sistema Chocolatería
-- Ejecutar en phpMyAdmin de Hostinger
-- ============================================================

SET FOREIGN_KEY_CHECKS=0;

-- Limpiar tablas (OPCIONAL - comentar si no quieres borrar datos existentes)
-- TRUNCATE TABLE KARDEX;
-- TRUNCATE TABLE PROXFAC;
-- TRUNCATE TABLE PROXCMP;
-- TRUNCATE TABLE PROXBOD;
-- TRUNCATE TABLE FACTURAS;
-- TRUNCATE TABLE COMPRAS;
-- TRUNCATE TABLE PRODUCTOS;
-- TRUNCATE TABLE CLIENTES;
-- TRUNCATE TABLE PROVEEDORES;
-- TRUNCATE TABLE BODEGAS;
-- TRUNCATE TABLE CATEGORIA;
-- TRUNCATE TABLE TRANSACCION;

-- ============================================================
-- CATEGORIAS
-- ============================================================
INSERT INTO CATEGORIA (CAT_CODIGO, CAT_NOMBRE, CAT_DESCRIPCION) VALUES
('CAT01', 'Chocolates Oscuros', 'Chocolates con alto contenido de cacao'),
('CAT02', 'Chocolates con Leche', 'Chocolates suaves y cremosos'),
('CAT03', 'Chocolates Blancos', 'Chocolates dulces de manteca de cacao'),
('CAT04', 'Trufas', 'Trufas artesanales con rellenos especiales'),
('CAT05', 'Bombones', 'Bombones variados y surtidos')
ON DUPLICATE KEY UPDATE CAT_NOMBRE=VALUES(CAT_NOMBRE);

-- ============================================================
-- BODEGAS
-- ============================================================
INSERT INTO BODEGAS (BOD_CODIGO, BOD_DESCRIPCION, BOD_DIRECCION, BOD_NOMBRE_ENCARGADO, BOD_TELEFONO_ENCARGADO) VALUES
('BOD001', 'Bodega Principal', 'Av. Principal 123, Quito', 'María González', '0987654321'),
('BOD002', 'Bodega Secundaria', 'Calle Secundaria 456, Quito', 'Juan Pérez', '0987654322'),
('BOD003', 'Bodega Norte', 'Av. Norte 789, Quito', 'Ana Torres', '0987654323')
ON DUPLICATE KEY UPDATE BOD_DESCRIPCION=VALUES(BOD_DESCRIPCION);

-- ============================================================
-- PROVEEDORES
-- ============================================================
INSERT INTO PROVEEDORES (PRV_ID, PRV_RUC, PRV_RAZON_SOCIAL, PRV_CORREO, PRV_DIRECCION, PRV_TELEFONO) VALUES
(1, '1792345678001', 'Cacao Premium S.A.', 'ventas@cacaopremium.com', 'Zona Industrial, Guayaquil', '0423456789'),
(2, '1792345679001', 'Dulces Andinos Cía.', 'contacto@dulcesandinos.com', 'Sector Sur, Quito', '0223456789'),
(3, '1792345680001', 'Importadora Chocolates del Mundo', 'info@chocomundo.com', 'Puerto Principal, Guayaquil', '0423456790')
ON DUPLICATE KEY UPDATE PRV_RAZON_SOCIAL=VALUES(PRV_RAZON_SOCIAL);

-- ============================================================
-- CLIENTES
-- ============================================================
INSERT INTO CLIENTES (CLI_ID, CLI_CEDULA_RUC, CLI_NOMBRE, CLI_TELEFONO, CLI_CORREO) VALUES
(1, '1718293847', 'Carlos Mendoza', '0998765432', 'carlos.mendoza@email.com'),
(2, '1718293848', 'Laura Fernández', '0998765433', 'laura.fernandez@email.com'),
(3, '1718293849', 'Roberto Silva', '0998765434', 'roberto.silva@email.com'),
(4, '1718293850', 'Patricia Rojas', '0998765435', 'patricia.rojas@email.com'),
(5, '1718293851', 'Diego Castro', '0998765436', 'diego.castro@email.com')
ON DUPLICATE KEY UPDATE CLI_NOMBRE=VALUES(CLI_NOMBRE);

-- ============================================================
-- PRODUCTOS
-- ============================================================
INSERT INTO PRODUCTOS (PRD_CODIGO, CAT_CODIGO, PRD_DESCRIPCION, PRD_PRECIO, PRD_COSTO_ADQUISICION) VALUES
('PROD001', 'CAT01', 'Chocolate Oscuro 70% Cacao - 100g', 3.50, 2.00),
('PROD002', 'CAT01', 'Chocolate Oscuro 85% Cacao - 100g', 4.00, 2.30),
('PROD003', 'CAT02', 'Chocolate con Leche Classic - 100g', 2.50, 1.50),
('PROD004', 'CAT02', 'Chocolate con Leche y Almendras - 100g', 3.00, 1.80),
('PROD005', 'CAT03', 'Chocolate Blanco Premium - 100g', 3.20, 1.90),
('PROD006', 'CAT04', 'Trufa de Chocolate Oscuro - Unidad', 1.50, 0.80),
('PROD007', 'CAT04', 'Trufa de Frambuesa - Unidad', 1.80, 0.90),
('PROD008', 'CAT04', 'Trufa de Café - Unidad', 1.70, 0.85),
('PROD009', 'CAT05', 'Caja de Bombones Surtidos 12 und', 15.00, 8.00),
('PROD010', 'CAT05', 'Caja de Bombones Premium 24 und', 28.00, 15.00),
('PROD011', 'CAT01', 'Barra Chocolate Oscuro con Naranja', 3.80, 2.20),
('PROD012', 'CAT02', 'Chocolate con Leche y Avellanas', 3.50, 2.00),
('PROD013', 'CAT03', 'Chocolate Blanco con Frutillas', 3.60, 2.10),
('PROD014', 'CAT04', 'Trufa de Menta - Unidad', 1.60, 0.85),
('PROD015', 'CAT05', 'Bombones de Licor 6 und', 12.00, 6.50)
ON DUPLICATE KEY UPDATE PRD_PRECIO=VALUES(PRD_PRECIO);

-- ============================================================
-- STOCK EN BODEGAS (PROXBOD)
-- ============================================================
INSERT INTO PROXBOD (BOD_CODIGO, PRD_CODIGO, DET_BOD_CANTIDAD, DET_BOD_UBICACION) VALUES
-- Bodega Principal
('BOD001', 'PROD001', 50, 'Estante A1'),
('BOD001', 'PROD002', 30, 'Estante A2'),
('BOD001', 'PROD003', 80, 'Estante B1'),
('BOD001', 'PROD004', 45, 'Estante B2'),
('BOD001', 'PROD005', 35, 'Estante C1'),
('BOD001', 'PROD006', 100, 'Vitrina 1'),
('BOD001', 'PROD007', 85, 'Vitrina 2'),
('BOD001', 'PROD008', 90, 'Vitrina 3'),
('BOD001', 'PROD009', 25, 'Caja Fuerte'),
('BOD001', 'PROD010', 15, 'Caja Fuerte'),
-- Bodega Secundaria
('BOD002', 'PROD001', 30, 'Zona 1'),
('BOD002', 'PROD003', 50, 'Zona 2'),
('BOD002', 'PROD011', 40, 'Zona 3'),
('BOD002', 'PROD012', 35, 'Zona 4'),
('BOD002', 'PROD013', 30, 'Zona 5'),
-- Bodega Norte
('BOD003', 'PROD014', 60, 'Sección A'),
('BOD003', 'PROD015', 20, 'Sección B')
ON DUPLICATE KEY UPDATE DET_BOD_CANTIDAD=VALUES(DET_BOD_CANTIDAD);

-- ============================================================
-- COMPRAS
-- ============================================================
INSERT INTO COMPRAS (CMP_CODIGO, PRV_ID, CMP_FECHA_ENTREGA, CMP_ESTADO) VALUES
(1, 1, '2026-01-15', 'Entregado'),
(2, 2, '2026-01-18', 'Entregado'),
(3, 3, '2026-01-20', 'Pendiente')
ON DUPLICATE KEY UPDATE CMP_ESTADO=VALUES(CMP_ESTADO);

-- ============================================================
-- DETALLE DE COMPRAS (PROXCMP)
-- ============================================================
INSERT INTO PROXCMP (CMP_CODIGO, PRD_CODIGO, DET_CMP_CANTIDAD, DET_CMP_COSTO_UNITARIO, ESTADO_PROXCMP) VALUES
-- Compra 1
(1, 'PROD001', 100, 2.00, 'ACT'),
(1, 'PROD002', 50, 2.30, 'ACT'),
(1, 'PROD011', 40, 2.20, 'ACT'),
-- Compra 2
(2, 'PROD003', 150, 1.50, 'ACT'),
(2, 'PROD004', 80, 1.80, 'ACT'),
(2, 'PROD005', 60, 1.90, 'ACT'),
-- Compra 3
(3, 'PROD009', 30, 8.00, 'PEN'),
(3, 'PROD010', 20, 15.00, 'PEN')
ON DUPLICATE KEY UPDATE DET_CMP_CANTIDAD=VALUES(DET_CMP_CANTIDAD);

-- ============================================================
-- FACTURAS
-- ============================================================
INSERT INTO FACTURAS (FAC_CODIGO, FAC_FECHA, FAC_SUBTOTAL, FAC_IVA, FAC_MONTO_TOTAL, FAC_ESTADO, ID_CARRITO, CLI_ID) VALUES
(1, '2026-01-21 10:30:00', 10.00, 1.50, 11.50, 'PAG', 1001, 1),
(2, '2026-01-21 11:15:00', 25.50, 3.83, 29.33, 'PAG', 1002, 2),
(3, '2026-01-21 14:20:00', 15.00, 2.25, 17.25, 'PAG', 1003, 3),
(4, '2026-01-21 16:45:00', 8.50, 1.28, 9.78, 'PAG', 1004, 4),
(5, '2026-01-22 09:00:00', 45.00, 6.75, 51.75, 'PAG', 1005, 5)
ON DUPLICATE KEY UPDATE FAC_ESTADO=VALUES(FAC_ESTADO);

-- ============================================================
-- DETALLE DE FACTURAS (PROXFAC)
-- ============================================================
INSERT INTO PROXFAC (FAC_CODIGO, PRD_CODIGO, DET_FAC_CANTIDAD, DET_FAC_PRECIO_UNITARIO, ESTADO_PROXFAC) VALUES
-- Factura 1
(1, 'PROD001', 2, 3.50, 'ACT'),
(1, 'PROD006', 2, 1.50, 'ACT'),
-- Factura 2
(2, 'PROD003', 3, 2.50, 'ACT'),
(2, 'PROD009', 1, 15.00, 'ACT'),
(2, 'PROD007', 3, 1.80, 'ACT'),
-- Factura 3
(3, 'PROD009', 1, 15.00, 'ACT'),
-- Factura 4
(4, 'PROD004', 2, 3.00, 'ACT'),
(4, 'PROD008', 1, 1.70, 'ACT'),
-- Factura 5
(5, 'PROD010', 1, 28.00, 'ACT'),
(5, 'PROD002', 3, 4.00, 'ACT'),
(5, 'PROD012', 1, 3.50, 'ACT')
ON DUPLICATE KEY UPDATE DET_FAC_CANTIDAD=VALUES(DET_FAC_CANTIDAD);

-- ============================================================
-- TRANSACCIONES
-- ============================================================
INSERT INTO TRANSACCION (TRN_ID, TRN_POS, TRN_NEG) VALUES
(1, 100, 0),  -- Ingreso inicial
(2, 50, 0),   -- Ingreso
(3, 0, 10),   -- Salida por venta
(4, 0, 5),    -- Salida por venta
(5, 30, 0)    -- Ingreso por compra
ON DUPLICATE KEY UPDATE TRN_POS=VALUES(TRN_POS);

SET FOREIGN_KEY_CHECKS=1;

-- ============================================================
-- VERIFICACIÓN
-- ============================================================
SELECT 'CATEGORIAS' as Tabla, COUNT(*) as Total FROM CATEGORIA
UNION ALL
SELECT 'BODEGAS', COUNT(*) FROM BODEGAS
UNION ALL
SELECT 'PROVEEDORES', COUNT(*) FROM PROVEEDORES
UNION ALL
SELECT 'CLIENTES', COUNT(*) FROM CLIENTES
UNION ALL
SELECT 'PRODUCTOS', COUNT(*) FROM PRODUCTOS
UNION ALL
SELECT 'PROXBOD (Stock)', COUNT(*) FROM PROXBOD
UNION ALL
SELECT 'COMPRAS', COUNT(*) FROM COMPRAS
UNION ALL
SELECT 'PROXCMP (Detalle Compras)', COUNT(*) FROM PROXCMP
UNION ALL
SELECT 'FACTURAS', COUNT(*) FROM FACTURAS
UNION ALL
SELECT 'PROXFAC (Detalle Facturas)', COUNT(*) FROM PROXFAC
UNION ALL
SELECT 'TRANSACCIONES', COUNT(*) FROM TRANSACCION;

-- Script completado exitosamente
