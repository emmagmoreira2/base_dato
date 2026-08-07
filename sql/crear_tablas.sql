-- =====================================================================
-- Proyecto: Sistema de Manejo de Propiedades y Alquileres
-- Curso: CCOM 4027 - Introducción al Manejo de Datos
-- Base de datos: PostgreSQL
-- =====================================================================

-- DROP TABLE IF EXISTS contrato CASCADE;
-- DROP TABLE IF EXISTS tenant CASCADE;
-- DROP TABLE IF EXISTS propiedad CASCADE;

CREATE TABLE IF NOT EXISTS propiedad (
    id              SERIAL PRIMARY KEY,
    tipo            VARCHAR(20)    NOT NULL DEFAULT 'Casa',
    numero          VARCHAR(20),
    direccion       VARCHAR(200)   NOT NULL,
    precio_alquiler NUMERIC(10,2)  NOT NULL CHECK (precio_alquiler >= 0),
    cuartos         INTEGER        NOT NULL CHECK (cuartos >= 0),
    disponible      BOOLEAN        NOT NULL DEFAULT TRUE,
    pago_mes        BOOLEAN        NOT NULL DEFAULT FALSE,
    monto_mensual   NUMERIC(10,2)  NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS tenant (
    id        SERIAL PRIMARY KEY,
    nombre    VARCHAR(150) NOT NULL,
    telefono  VARCHAR(20)  NOT NULL
);

CREATE TABLE IF NOT EXISTS contrato (
    id            SERIAL PRIMARY KEY,
    tenant_id     INTEGER NOT NULL REFERENCES tenant(id)    ON DELETE CASCADE,
    propiedad_id  INTEGER NOT NULL REFERENCES propiedad(id) ON DELETE CASCADE,
    fecha_inicio  DATE    NOT NULL,
    fecha_fin     DATE    NOT NULL,
    CONSTRAINT fechas_validas CHECK (fecha_fin >= fecha_inicio)
);

CREATE INDEX IF NOT EXISTS idx_contrato_tenant      ON contrato(tenant_id);
CREATE INDEX IF NOT EXISTS idx_contrato_propiedad   ON contrato(propiedad_id);
CREATE INDEX IF NOT EXISTS idx_propiedad_disponible ON propiedad(disponible);
CREATE INDEX IF NOT EXISTS idx_propiedad_tipo       ON propiedad(tipo);

-- Datos de ejemplo
INSERT INTO propiedad (tipo, numero, direccion, precio_alquiler, cuartos, disponible, pago_mes, monto_mensual) VALUES
  ('Casa',        'Casa #1',  'Calle Loíza 1234, San Juan, PR',       950.00, 2, TRUE,  FALSE, 950.00),
  ('Apartamento', 'Apt 2B',   'Ave. Ponce de León 50, Hato Rey',     1250.00, 3, TRUE,  FALSE, 1250.00),
  ('Casa',        'Casa #3',  'Calle Cerra 88, Santurce',              750.00, 1, FALSE, TRUE,  750.00),
  ('Apartamento', 'Apt 4A',   'Calle Sol 21, Viejo San Juan',         1500.00, 2, TRUE,  FALSE, 1500.00);

INSERT INTO tenant (nombre, telefono) VALUES
  ('María Rodríguez',  '787-555-1010'),
  ('José Pérez',       '787-555-2020'),
  ('Ana Hernández',    '939-555-3030');

INSERT INTO contrato (tenant_id, propiedad_id, fecha_inicio, fecha_fin) VALUES
  (3, 3, '2026-01-01', '2026-12-31');

SELECT 'propiedad' AS tabla, COUNT(*) AS filas FROM propiedad
UNION ALL
SELECT 'tenant',   COUNT(*) FROM tenant
UNION ALL
SELECT 'contrato', COUNT(*) FROM contrato;
