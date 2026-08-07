<?php
$titulo = 'Inicio';
$pagina = 'inicio';
require __DIR__ . '/_cabecera.php';
?>

<div class="card">
  <h1>Sistema de Manejo de Propiedades y Alquileres</h1>
  <p>
    Esta aplicación permite a un dueño o administrador de alquileres llevar el control
    de sus <strong>propiedades</strong>, sus <strong>inquilinos (tenants)</strong> y los
    <strong>contratos</strong> de alquiler que conectan a un inquilino con una propiedad
    durante un periodo de tiempo.
  </p>
</div>

<div class="card">
  <h2>¿Cómo se usa el sistema?</h2>
  <ol style="margin-left: 22px;">
    <li>
      <strong>Propiedades</strong> – Ve a la página <em>Propiedades</em> para añadir,
      editar o eliminar propiedades. Cada propiedad tiene dirección, precio de alquiler,
      cantidad de cuartos y si está disponible.
    </li>
    <li>
      <strong>Tenants</strong> – En la página <em>Tenants</em> registras a los inquilinos
      con su nombre y teléfono. También puedes editarlos o eliminarlos.
    </li>
    <li>
      <strong>Contratos</strong> – En <em>Contratos</em> se crean los contratos que
      conectan a un tenant con una propiedad. Cada contrato tiene fecha de inicio y de
      fin. Al crear un contrato, la propiedad se marca como <em>no disponible</em>.
      Cuando el contrato se elimina, la propiedad vuelve a estar <em>disponible</em>.
    </li>
    <li>
      <strong>Reporte</strong> – La página <em>Reporte</em> muestra datos seleccionados:
      las propiedades disponibles, los tenants registrados y los contratos activos
      (los que están vigentes para la fecha de hoy).
    </li>
  </ol>
</div>

<div class="card">
  <h2>Resumen de la base de datos</h2>
  <p>El sistema utiliza tres entidades en PostgreSQL:</p>
  <ul style="margin-left: 22px;">
    <li><strong>propiedad</strong> – id, dirección, precio, cuartos, disponible</li>
    <li><strong>tenant</strong> – id, nombre, teléfono</li>
    <li><strong>contrato</strong> – id, tenant_id, propiedad_id, fecha_inicio, fecha_fin</li>
  </ul>
  <p style="margin-top: 10px;">
    Las relaciones son: un <em>tenant</em> puede tener varios <em>contratos</em>,
    y una <em>propiedad</em> puede tener varios <em>contratos</em>.
    El <em>contrato</em> es la entidad que conecta a ambos.
  </p>
</div>

<?php require __DIR__ . '/_pie.php'; ?>
