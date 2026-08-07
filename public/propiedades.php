<?php
/**
 * public/propiedades.php
 * CRUD de propiedades con tipo (Casa/Apartamento), número,
 * inquilino activo, pago del mes y monto mensual.
 */
require __DIR__ . '/../config/conexion.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// ── POST ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    try {
        if ($accion === 'crear') {
            $stmt = $pdo->prepare(
                'INSERT INTO propiedad
                    (tipo, numero, direccion, precio_alquiler, cuartos, disponible, pago_mes, monto_mensual)
                 VALUES
                    (:tipo, :num, :dir, :precio, :cuartos, :disp, :pago, :monto)'
            );
            $stmt->execute([
                ':tipo'   => $_POST['tipo'],
                ':num'    => trim($_POST['numero']),
                ':dir'    => trim($_POST['direccion']),
                ':precio' => (float)$_POST['precio_alquiler'],
                ':cuartos'=> (int)$_POST['cuartos'],
                ':disp'   => isset($_POST['disponible'])  ? 'true' : 'false',
                ':pago'   => isset($_POST['pago_mes'])    ? 'true' : 'false',
                ':monto'  => (float)$_POST['monto_mensual'],
            ]);
            $_SESSION['msg'] = ['tipo'=>'ok','texto'=>'Propiedad añadida correctamente.'];
        }
        elseif ($accion === 'editar') {
            $stmt = $pdo->prepare(
                'UPDATE propiedad SET
                    tipo            = :tipo,
                    numero          = :num,
                    direccion       = :dir,
                    precio_alquiler = :precio,
                    cuartos         = :cuartos,
                    disponible      = :disp,
                    pago_mes        = :pago,
                    monto_mensual   = :monto
                 WHERE id = :id'
            );
            $stmt->execute([
                ':id'     => (int)$_POST['id'],
                ':tipo'   => $_POST['tipo'],
                ':num'    => trim($_POST['numero']),
                ':dir'    => trim($_POST['direccion']),
                ':precio' => (float)$_POST['precio_alquiler'],
                ':cuartos'=> (int)$_POST['cuartos'],
                ':disp'   => isset($_POST['disponible'])  ? 'true' : 'false',
                ':pago'   => isset($_POST['pago_mes'])    ? 'true' : 'false',
                ':monto'  => (float)$_POST['monto_mensual'],
            ]);
            $_SESSION['msg'] = ['tipo'=>'ok','texto'=>'Propiedad actualizada.'];
        }
        elseif ($accion === 'eliminar') {
            $stmt = $pdo->prepare('DELETE FROM propiedad WHERE id = :id');
            $stmt->execute([':id' => (int)$_POST['id']]);
            $_SESSION['msg'] = ['tipo'=>'ok','texto'=>'Propiedad eliminada.'];
        }
    } catch (PDOException $e) {
        $_SESSION['msg'] = ['tipo'=>'error','texto'=>'Error: '.$e->getMessage()];
    }
    header('Location: propiedades.php');
    exit;
}

// ── GET ─────────────────────────────────────────────────
$editando = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare('SELECT * FROM propiedad WHERE id = :id');
    $stmt->execute([':id' => (int)$_GET['editar']]);
    $editando = $stmt->fetch();
}

// Traer propiedades con inquilino activo via JOIN
$propiedades = $pdo->query(
    "SELECT p.*,
            t.nombre AS inquilino
       FROM propiedad p
       LEFT JOIN contrato c ON c.propiedad_id = p.id
                            AND CURRENT_DATE BETWEEN c.fecha_inicio AND c.fecha_fin
       LEFT JOIN tenant t ON t.id = c.tenant_id
       ORDER BY p.tipo ASC, p.id DESC"
)->fetchAll();

// Separar por tipo
$apartamentos = array_filter($propiedades, fn($p) => $p['tipo'] === 'Apartamento');
$casas        = array_filter($propiedades, fn($p) => $p['tipo'] === 'Casa');

$titulo = 'Propiedades'; $pagina = 'propiedades';
require __DIR__ . '/_cabecera.php';
?>

<h1>Gestión de Propiedades</h1>
<p>Añade, edita o elimina propiedades. Operaciones: <code>INSERT</code>, <code>UPDATE</code>, <code>DELETE</code>.</p>

<div class="split">

  <!-- ══ FORMULARIO ══ -->
  <div class="card">
    <h2><?= $editando ? 'Editar propiedad' : 'Añadir nueva propiedad' ?></h2>
    <form method="POST" action="propiedades.php">
      <input type="hidden" name="accion" value="<?= $editando ? 'editar' : 'crear' ?>">
      <?php if ($editando): ?>
        <input type="hidden" name="id" value="<?= (int)$editando['id'] ?>">
      <?php endif; ?>

      <div class="campo">
        <label>Tipo de propiedad</label>
        <select name="tipo" required>
          <option value="Casa"        <?= (!$editando || $editando['tipo']==='Casa')        ? 'selected':'' ?>>🏠 Casa</option>
          <option value="Apartamento" <?= ($editando  && $editando['tipo']==='Apartamento') ? 'selected':'' ?>>🏢 Apartamento</option>
        </select>
      </div>

      <div class="campo">
        <label>Número de casa / apartamento</label>
        <input type="text" name="numero" placeholder="Ej: Apt 3B / Casa #12"
               value="<?= htmlspecialchars($editando['numero'] ?? '') ?>">
      </div>

      <div class="campo">
        <label>Dirección</label>
        <input type="text" name="direccion" required
               value="<?= htmlspecialchars($editando['direccion'] ?? '') ?>">
      </div>

      <div class="campo">
        <label>Precio de alquiler ($)</label>
        <input type="number" name="precio_alquiler" step="0.01" min="0" required
               value="<?= htmlspecialchars($editando['precio_alquiler'] ?? '') ?>">
      </div>

      <div class="campo">
        <label>Monto mensual que debe ($)</label>
        <input type="number" name="monto_mensual" step="0.01" min="0"
               value="<?= htmlspecialchars($editando['monto_mensual'] ?? '0') ?>">
      </div>

      <div class="campo">
        <label>Cantidad de cuartos</label>
        <input type="number" name="cuartos" min="0" required
               value="<?= htmlspecialchars($editando['cuartos'] ?? '') ?>">
      </div>

      <div class="campo" style="display:flex; gap:20px;">
        <label style="display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
          <input type="checkbox" name="disponible" style="width:auto;"
                 <?= (!$editando || ($editando['disponible']==='t'||$editando['disponible']===true)) ? 'checked':'' ?>>
          Disponible
        </label>
        <label style="display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
          <input type="checkbox" name="pago_mes" style="width:auto;"
                 <?= ($editando && ($editando['pago_mes']==='t'||$editando['pago_mes']===true)) ? 'checked':'' ?>>
          Pagó este mes
        </label>
      </div>

      <button type="submit" class="btn btn-primario">
        <?= $editando ? 'Guardar cambios' : 'Añadir propiedad' ?>
      </button>
      <?php if ($editando): ?>
        <a href="propiedades.php" class="btn btn-secundario">Cancelar</a>
      <?php endif; ?>
    </form>
  </div>

  <!-- ══ TABLA ══ -->
  <div class="card">
    <h2>Listado de propiedades (<?= count($propiedades) ?>)</h2>

    <?php
    function tablaProps(array $lista, string $icono, string $titulo): void { ?>
      <?php if ($lista): ?>
      <h2 style="margin-top:18px;"><?= $icono ?> <?= $titulo ?> (<?= count($lista) ?>)</h2>
      <table>
        <thead><tr>
          <th>#</th><th>Dirección</th><th>Precio</th>
          <th>Inquilino</th><th>Pagó</th><th>Debe/mes</th>
          <th>Disp.</th><th>Acciones</th>
        </tr></thead>
        <tbody>
        <?php foreach ($lista as $p):
            $disp = ($p['disponible']==='t' || $p['disponible']===true);
            $pago = ($p['pago_mes']==='t'   || $p['pago_mes']===true);
        ?>
          <tr>
            <td><?= htmlspecialchars($p['numero'] ?? '—') ?></td>
            <td><?= htmlspecialchars($p['direccion']) ?></td>
            <td>$<?= number_format($p['precio_alquiler'],2) ?></td>
            <td><?= $p['inquilino'] ? htmlspecialchars($p['inquilino']) : '<span style="color:#aaa;">—</span>' ?></td>
            <td><span class="badge <?= $pago?'badge-si':'badge-no' ?>"><?= $pago?'Sí':'No' ?></span></td>
            <td>$<?= number_format($p['monto_mensual'],2) ?></td>
            <td><span class="badge <?= $disp?'badge-si':'badge-no' ?>"><?= $disp?'Sí':'No' ?></span></td>
            <td>
              <a href="propiedades.php?editar=<?= (int)$p['id'] ?>" class="btn btn-secundario">Editar</a>
              <form method="POST" style="display:inline;"
                    onsubmit="return confirm('¿Eliminar esta propiedad? También eliminará sus contratos.');">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                <button type="submit" class="btn btn-peligro">Eliminar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif;
    }

    tablaProps(iterator_to_array($apartamentos), '🏢', 'Apartamentos');
    tablaProps(iterator_to_array($casas),        '🏠', 'Casas');

    if (!$propiedades): ?>
      <p>No hay propiedades registradas todavía.</p>
    <?php endif; ?>
  </div>

</div>

<?php require __DIR__ . '/_pie.php'; ?>
