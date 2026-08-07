<?php
/**
 * config/conexion.php
 * Conexión a SQLite usando PDO.
 * La base de datos se crea automáticamente si no existe,
 * junto con las tablas y datos de ejemplo.
 */

$DB_PATH = __DIR__ . '/../data/alquileres.db';

// Crear carpeta data/ si no existe
if (!is_dir(__DIR__ . '/../data')) {
    mkdir(__DIR__ . '/../data', 0755, true);
}

try {
    $pdo = new PDO('sqlite:' . $DB_PATH, null, null, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Activar claves foráneas en SQLite
    $pdo->exec('PRAGMA foreign_keys = ON');

    // Crear tablas si no existen
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS propiedad (
            id              INTEGER PRIMARY KEY AUTOINCREMENT,
            direccion       TEXT    NOT NULL,
            precio_alquiler REAL    NOT NULL CHECK (precio_alquiler >= 0),
            cuartos         INTEGER NOT NULL CHECK (cuartos >= 0),
            disponible      INTEGER NOT NULL DEFAULT 1
        );

        CREATE TABLE IF NOT EXISTS tenant (
            id       INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre   TEXT NOT NULL,
            telefono TEXT NOT NULL
        );

        CREATE TABLE IF NOT EXISTS contrato (
            id           INTEGER PRIMARY KEY AUTOINCREMENT,
            tenant_id    INTEGER NOT NULL REFERENCES tenant(id)    ON DELETE CASCADE,
            propiedad_id INTEGER NOT NULL REFERENCES propiedad(id) ON DELETE CASCADE,
            fecha_inicio TEXT    NOT NULL,
            fecha_fin    TEXT    NOT NULL,
            CHECK (fecha_fin >= fecha_inicio)
        );
    ");

    // Insertar datos de ejemplo solo si las tablas están vacías
    $count = $pdo->query('SELECT COUNT(*) FROM propiedad')->fetchColumn();
    if ($count == 0) {
        $pdo->exec("
            INSERT INTO propiedad (direccion, precio_alquiler, cuartos, disponible) VALUES
              ('Calle Loíza 1234, San Juan, PR',    950.00, 2, 1),
              ('Ave. Ponce de León 50, Hato Rey',  1250.00, 3, 1),
              ('Calle Cerra 88, Santurce',           750.00, 1, 0),
              ('Calle Sol 21, Viejo San Juan',      1500.00, 2, 1);

            INSERT INTO tenant (nombre, telefono) VALUES
              ('María Rodríguez',  '787-555-1010'),
              ('José Pérez',       '787-555-2020'),
              ('Ana Hernández',    '939-555-3030');

            INSERT INTO contrato (tenant_id, propiedad_id, fecha_inicio, fecha_fin) VALUES
              (3, 3, '2026-01-01', '2026-12-31');
        ");
    }

} catch (PDOException $e) {
    die('Error de conexión a la base de datos: ' . htmlspecialchars($e->getMessage()));
}
