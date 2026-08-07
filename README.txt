=====================================================================
  Sistema de Manejo de Propiedades y Alquileres
  Emma G. Moreira Irizarry- 801-23-7349
  Curso: CCOM 4027 - Introducción al Manejo de Datos
  Profesor: Carlos J. Corrada Bravo
=====================================================================

DESCRIPCIÓN
-----------
Aplicación web que permite a un administrador de alquileres
llevar el control de sus propiedades (casas y apartamentos), sus
inquilinos (tenants) y los contratos de alquiler que conectan a un
Inquilino de una propiedad durante un periodo de tiempo.


FUNCIONALIDADES
---------------
- Tres entidades: propiedad, tenant, contrato.
- Dos relaciones: tenant <-> contrato y propiedad <-> contrato.
- Cinco páginas accesibles desde el menú:
    index.php        -> presentación e instrucciones
    propiedades.php  -> CRUD completo (INSERT, UPDATE, DELETE)
    tenants.php      -> CRUD completo (INSERT, UPDATE, DELETE)
    contratos.php    -> CRUD completo (INSERT, UPDATE, DELETE)
    reporte.php      -> consultas SELECT con tres pestañas

- Las propiedades se dividen en casas y apartamentos.
- Al crear un contrato, la propiedad se marca como NO disponible.
- Al eliminar un contrato, la propiedad vuelve a estar disponible.
- Cuando un contrato se vence (fecha_fin < hoy), la propiedad
  Vuelve a estar disponible automáticamente.
- Seguimiento del pago mensual y del monto mensual por propiedad.


INSTALACIÓN EN ADA
------------------
1. Conectarse a PostgreSQL:
   psql -h localhost -U emma_moreira -d ccom4027c52

2. Crear las tablas:
   psql -h localhost -U emma_moreira -d ccom4027c52 -f sql/crear_tablas.sql

3. Configurar config/conexion.php:
   $DB_HOST = 'localhost';
   $DB_PORT = '5432';
   $DB_NAME = 'ccom4027c52';
   $DB_USER = 'emma_moreira';
   $DB_PASS = 'tu_contrasena';

4. URL del sistema:
   ada.uprrp.edu/~emma.moreira/proyecto_alquileres/public/index.php


CÓMO USAR EL SISTEMA
--------------------
1. Propiedades  - Añade casas o apartamentos con tipo, número,
                  dirección, precio, cuartos, disponibilidad,
                  Monto mensual y si pagaron este mes.
                  La tabla se divide por tipo y muestra el
                  Inquilino activo con contrato vigente.

2. Tenants      - Registra a los inquilinos con su nombre y teléfono.

3. Contratos    - Crea contratos seleccionando un tenant y una
                  propiedad disponible con fechas de inicio y fin.
                  Al crear: propiedad -> no disponible.
                  Al eliminar o vencer: propiedad -> disponible.

4. Reporte      - Tres pestañas SELECT:
                  * Propiedades disponibles
                  * Tenants con total de contratos
                  * Contratos activos vigentes hoy

