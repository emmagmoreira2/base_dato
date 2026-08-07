<?php
/**
 * public/reporte.php
 * Reporte con SELECT — tres pestañas con todos los campos actualizados.
 */
require __DIR__ . '/../config/conexion.php';

$vista = $_GET['v'] ?? 'disponibles';

switch ($vista) {

    case 'tenants':
        $sql = 'SELECT t.id, t.nombre, t.telefono,
                       COUNT(c.id) AS total_contratos
                  FROM tenant t
                  LEFT JOIN contrato c ON c.tenant_id = t.id
                 GROUP BY t.id, t.nombre, t.telefono
                 ORDER BY t.nombre';
        $rows = $pdo->query($sql)->fetchAll();
        break;

    case 'activos':
        $sql = "SELECT c.id,
                       t.nombre       AS tenant_nombre,
                       t.telefono,
                       p.tipo,
                       p.numero,
                       p.direccion    AS propiedad_direccion,
                       p.precio_alquiler,
                       p.monto_mensual,
                       p.pago_mes,
                       c.fecha_inicio,
                       c.fecha_fin
                  FROM contrato c
                  JOIN tenant    t ON t.id = c.tenant_id
                  JOIN propiedad p ON p.id = c.propiedad_id
                 WHERE CURRENT_DATE BETWEEN c.fecha_inicio AND c.fecha_fin
                 ORDER BY c.fecha_fin ASC";
        $rows = $pdo->query($sql)->fetchAll();
        break;

    case 'disponibles':
    default:
        $vista = 'disponibles';
        $sql = 'SELECT id, tipo, numero, direccion, precio_alquiler,
                       cuartos, pago_mes, monto_mensual
                  FROM propiedad
                 WHERE disponible = TRUE
                 ORDER BY tipo ASC, precio_alquiler ASC';
        $rows = $pdo->query($sql)->fetchAll();
        break;
}

$titulo = 'Reporte';
$pagina = 'reporte';
require __DIR__ . '/_cabecera.php';
?>

<h1>Reporte (SELECT)</h1>
<p>Datos seleccionados con consultas <code>SELECT</code>. Cambia la vista con las pestañas:</p>

<div class="tabs">
  <a href="reporte.php?v=disponibles" class="<?= $vista==='disponibles'?'activo':'' ?>">Propiedades disponibles</a>
  <a href="reporte.php?v=tenants"     class="<?= $vista==='tenants'    ?'activo':'' ?>">Tenants</a>
  <a href="reporte.php?v=activos"     class="<?= $vista==='activos'    ?'activo':'' ?>">Contratos activos</a>
</div>

<div class="card">

<?php if ($vista === 'disponibles'): ?>
  <h2>Propiedades disponibles (<?= count($rows) ?>)</h2>
  <p>Propiedades sin contrato vigente, ordenadas por tipo y precio.</p>
  <?php if (!$rows): ?>
    <p>No hay propiedades disponibles en este momento.</p>
  <?php else: ?>
    <table>
      <thead><tr>
        <th>ID</th><th>Tipo</th><th>Número</th><th>Dirección</th>
        <th>Precio</th><th>Cuartos</th><th>Pagó mes</th><th>Monto mensual</th>
      </tr></thead>
      <tbody>
      <?php foreach ($rows as $r):
          $pago = ($r['pago_mes']==='t' || $r['pago_mes']===true);
      ?>
        <tr>
          <td><?= (int)$r['id'] ?></td>
          <td><?= htmlspecialchars($r['tipo']) ?></td>
          <td><?= htmlspecialchars($r['numero'] ?? '—') ?></td>
          <td><?= htmlspecialchars($r['direccion']) ?></td>
          <td>$<?= number_format($r['precio_alquiler'],2) ?></td>
          <td><?= (int)$r['cuartos'] ?></td>
          <td><span class="badge <?= $pago?'badge-si':'badge-no' ?>"><?= $pago?'Sí':'No' ?></span></td>
          <td>$<?= number_format($r['monto_mensual'],2) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

<?php elseif ($vista === 'tenants'): ?>
  <h2>Tenants registrados (<?= count($rows) ?>)</h2>
  <p>Listado de inquilinos con su cantidad total de contratos.</p>
  <?php if (!$rows): ?>
    <p>No hay tenants registrados.</p>
  <?php else: ?>
    <table>
      <thead><tr>
        <th>ID</th><th>Nombre</th><th>Teléfono</th><th>Total de contratos</th>
      </tr></thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= (int)$r['id'] ?></td>
          <td><?= htmlspecialchars($r['nombre']) ?></td>
          <td><?= htmlspecialchars($r['telefono']) ?></td>
          <td><?= (int)$r['total_contratos'] ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

<?php elseif ($vista === 'activos'): ?>
  <h2>Contratos activos (<?= count($rows) ?>)</h2>
  <p>Contratos vigentes para la fecha de hoy.</p>
  <?php if (!$rows): ?>
    <p>No hay contratos activos en este momento.</p>
  <?php else: ?>
    <table>
      <thead><tr>
        <th>ID</th><th>Tenant</th><th>Teléfono</th>
        <th>Tipo</th><th>Número</th><th>Dirección</th>
        <th>Precio</th><th>Pagó mes</th><th>Monto mensual</th>
        <th>Inicio</th><th>Fin</th>
      </tr></thead>
      <tbody>
      <?php foreach ($rows as $r):
          $pago = ($r['pago_mes']==='t' || $r['pago_mes']===true);
      ?>
        <tr>
          <td><?= (int)$r['id'] ?></td>
          <td><?= htmlspecialchars($r['tenant_nombre']) ?></td>
          <td><?= htmlspecialchars($r['telefono']) ?></td>
          <td><?= htmlspecialchars($r['tipo']) ?></td>
          <td><?= htmlspecialchars($r['numero'] ?? '—') ?></td>
          <td><?= htmlspecialchars($r['propiedad_direccion']) ?></td>
          <td>$<?= number_format($r['precio_alquiler'],2) ?></td>
          <td><span class="badge <?= $pago?'badge-si':'badge-no' ?>"><?= $pago?'Sí':'No' ?></span></td>
          <td>$<?= number_format($r['monto_mensual'],2) ?></td>
          <td><?= htmlspecialchars($r['fecha_inicio']) ?></td>
          <td><?= htmlspecialchars($r['fecha_fin']) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
<?php endif; ?>

</div>

<?php require __DIR__ . '/_pie.php'; ?>
