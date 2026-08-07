# Lista de requisitos en orden de prioridad
## Sistema de Manejo de Propiedades y Alquileres

**Curso:** CCOM 4027 — Introducción al Manejo de Datos
**Proyecto:** Base de datos + interfaz web (PHP + PostgreSQL)

---

## Idea general

El sistema permite a un dueño o administrador de alquileres llevar el control
de sus **propiedades**, sus **inquilinos (tenants)** y los **contratos de
alquiler** que conectan a un inquilino con una propiedad por un período de
tiempo.

---

## Requisitos en orden de prioridad

### Prioridad ALTA (esenciales para el sistema)

1. **R1. Guardar propiedades** — El sistema debe permitir registrar propiedades
   con: ID, dirección, precio de alquiler, cantidad de cuartos y disponibilidad
   (sí/no).

2. **R2. Guardar tenants (inquilinos)** — El sistema debe permitir registrar
   inquilinos con: ID, nombre y teléfono.

3. **R3. Guardar contratos** — El sistema debe permitir registrar contratos
   con: ID, tenant, propiedad, fecha de inicio y fecha de fin. El contrato es
   la entidad que conecta a un tenant con una propiedad.

4. **R4. Insertar registros (INSERT)** — Desde la interfaz web se debe poder
   añadir propiedades, tenants y contratos.

5. **R5. Eliminar registros (DELETE)** — Desde la interfaz web se debe poder
   eliminar propiedades, tenants y contratos.

6. **R6. Editar registros (UPDATE)** — Desde la interfaz web se debe poder
   modificar la información de propiedades, tenants y contratos.

### Prioridad MEDIA (importantes para la utilidad del sistema)

7. **R7. Listar propiedades disponibles (SELECT)** — Mostrar las propiedades
   que actualmente están marcadas como disponibles.

8. **R8. Listar tenants (SELECT)** — Mostrar todos los tenants registrados.

9. **R9. Listar contratos activos (SELECT)** — Mostrar los contratos cuya
   fecha de inicio y fin contengan la fecha de hoy.

10. **R10. Página de inicio con instrucciones** — Una página que explique
    de qué se trata la aplicación y cómo se usa.

### Prioridad BAJA (mejoras de calidad)

11. **R11. Cuando se crea un contrato, la propiedad asociada se marca
    automáticamente como no disponible.**

12. **R12. Cuando se elimina un contrato, la propiedad asociada vuelve a
    estar disponible.**

13. **R13. Validar fechas** — La fecha de fin no puede ser anterior a la
    de inicio (constraint a nivel de base de datos).

14. **R14. Mensajes de confirmación** — Mostrar mensajes cuando una operación
    tiene éxito o cuando falla.

15. **R15. Confirmar antes de eliminar** — Pedir confirmación al usuario
    antes de borrar un registro.

---

## Entidades (para el diagrama de la base de datos)

- **Propiedad**
- **Tenant**
- **Contrato**

## Relaciones

- Un **tenant** puede tener uno o varios **contratos**.
- Una **propiedad** puede tener uno o varios **contratos** (no a la vez).
- Un **contrato** conecta exactamente un tenant con exactamente una propiedad.
