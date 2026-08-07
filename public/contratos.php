<?php
require_once __DIR__ . '/../config/conexion.php';
session_start();

$msg = $_SESSION['msg'] ?? '';
unset($_SESSION['msg']);

// ── INSERT ──────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'insertar') {
    $tenant_id    = (int)$_POST['tenant_id'];
    $propiedad_id = (int)$_POST['propiedad_id'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin    = $_POST['fecha_fin'];

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare(
            "INSERT INTO contrato (tenant_id, propiedad_id, fecha_inicio, fecha_fin)
             VALUES (:tid, :pid, :fi, :ff)"
        );
        $stmt->execute([
            ':tid' => $tenant_id,
            ':pid' => $propiedad_id,
            ':fi'  => $fecha_inicio,
            ':ff'  => $fecha_fin,
        ]);

        $pdo->prepare("UPDATE propiedad SET disponible = FALSE WHERE id = :pid")
            ->execute([':pid' => $propiedad_id]);

        $pdo->commit();
        $_SESSION['msg'] = "✅ Contrato creado correctamente.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['msg'] = "❌ Error al crear contrato: " . $e->getMessage();
    }

    header("Location: contratos.php");
    exit;
}

// ── DELETE ───────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'eliminar') {
    $id = (int)$_POST['id'];

    try {
        $pdo->beginTransaction();

        $row = $pdo->prepare("SELECT propiedad_id FROM contrato WHERE id = :id");
        $row->execute([':id' => $id]);
        $contrato = $row->fetch();

        $pdo->prepare("DELETE FROM contrato WHERE id = :id")->execute([':id' => $id]);

        if ($contrato) {
            $pdo->prepare("UPDATE propiedad SET disponible = TRUE WHERE id = :pid")
                ->execute([':pid' => $contrato['propiedad_id']]);
        }

        $pdo->commit();
        $_SESSION['msg'] = "✅ Contrato eliminado y propiedad liberada.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['msg'] = "❌ Error al eliminar: " . $e->getMessage();
    }

    header("Location: contratos.php");
    exit;
}

// ── UPDATE ────────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'actualizar') {
    $id           = (int)$_POST['id'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin    = $_POST['fecha_fin'];

    try {
        $pdo->prepare(
            "UPDATE contrato SET fecha_inicio = :fi, fecha_fin = :ff WHERE id = :id"
        )->execute([':fi' => $fecha_inicio, ':ff' => $fecha_fin, ':id' => $id]);

        $_SESSION['msg'] = "✅ Contrato actualizado.";
    } catch (Exception $e) {
        $_SESSION['msg'] = "❌ Error al actualizar: " . $e->getMessage();
    }

    header("Location: contratos.php");
    exit;
}

// ── Datos para el formulario ─────────────────────────────────────────────────
$tenants     = $pdo->query("SELECT id, nombre FROM tenant ORDER BY nombre")->fetchAll();
$propiedades = $pdo->query(
    "SELECT id, direccion, precio_alquiler FROM propiedad WHERE disponible = TRUE ORDER BY direccion"
)->fetchAll();

// ── Lista de contratos ────────────────────────────────────────────────────────
$contratos = $pdo->query(
    "SELECT c.id,
            t.nombre          AS tenant,
            p.direccion       AS propiedad,
            p.precio_alquiler AS precio,
            c.fecha_inicio,
            c.fecha_fin,
            c.tenant_id,
            c.propiedad_id
     FROM contrato c
     JOIN tenant    t ON t.id = c.tenant_id
     JOIN propiedad p ON p.id = c.propiedad_id
     ORDER BY c.fecha_inicio DESC"
)->fetchAll();

// Contrato en modo edición
$editar = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare(
        "SELECT c.*, t.nombre AS tenant, p.direccion AS propiedad
         FROM contrato c
         JOIN tenant    t ON t.id = c.tenant_id
         JOIN propiedad p ON p.id = c.propiedad_id
         WHERE c.id = :id"
    );
    $stmt->execute([':id' => (int)$_GET['editar']]);
    $editar = $stmt->fetch();
}
?>
<?php include '_cabecera.php'; ?>

<h2>Contratos</h2>

<?php if ($msg): ?>
  <p class="msg"><?= htmlspecialchars($msg) ?></p>
<?php endif; ?>

<div class="layout">

  <!-- ── Formulario ── -->
  <div class="form-box">
    <?php if ($editar): ?>
      <h3>Editar contrato #<?= $editar['id'] ?></h3>
      <form method="post">
        <input type="hidden" name="accion" value="actualizar">
        <input type="hidden" name="id"     value="<?= $editar['id'] ?>">

        <label>Tenant</label>
        <p><?= htmlspecialchars($editar['tenant']) ?></p>

        <label>Propiedad</label>
        <p><?= htmlspecialchars($editar['propiedad']) ?></p>

        <label>Fecha inicio</label>
        <input type="date" name="fecha_inicio"
               value="<?= htmlspecialchars($editar['fecha_inicio']) ?>" required>

        <label>Fecha fin</label>
        <input type="date" name="fecha_fin"
               value="<?= htmlspecialchars($editar['fecha_fin']) ?>" required>

        <button class="btn-edit" type="submit">Guardar cambios</button>
        <a class="btn-edit" href="contratos.php" style="display:inline-block;text-decoration:none;">Cancelar</a>
      </form>

    <?php else: ?>
      <h3>Nuevo contrato</h3>
      <form method="post">
        <input type="hidden" name="accion" value="insertar">

        <label>Tenant</label>
        <select name="tenant_id" required>
          <option value="">— selecciona —</option>
          <?php foreach ($tenants as $t): ?>
            <option value="<?= $t['id'] ?>">
              <?= htmlspecialchars($t['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <label>Propiedad disponible</label>
        <select name="propiedad_id" required>
          <option value="">— selecciona —</option>
          <?php foreach ($propiedades as $p): ?>
            <option value="<?= $p['id'] ?>">
              <?= htmlspecialchars($p['direccion']) ?>
              ($<?= number_format($p['precio_alquiler'], 2) ?>)
            </option>
          <?php endforeach; ?>
        </select>

        <label>Fecha inicio</label>
        <input type="date" name="fecha_inicio" required>

        <label>Fecha fin</label>
        <input type="date" name="fecha_fin" required>

        <button type="submit">Crear contrato</button>
      </form>
    <?php endif; ?>
  </div>

  <!-- ── Tabla ── -->
  <div class="table-box">
    <h3>Contratos registrados</h3>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Tenant</th>
          <th>Propiedad</th>
          <th>Precio/mes</th>
          <th>Inicio</th>
          <th>Fin</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($contratos)): ?>
          <tr><td colspan="7">No hay contratos registrados.</td></tr>
        <?php else: ?>
          <?php foreach ($contratos as $c): ?>
            <tr>
              <td><?= $c['id'] ?></td>
              <td><?= htmlspecialchars($c['tenant']) ?></td>
              <td><?= htmlspecialchars($c['propiedad']) ?></td>
              <td>$<?= number_format($c['precio'], 2) ?></td>
              <td><?= htmlspecialchars($c['fecha_inicio']) ?></td>
              <td><?= htmlspecialchars($c['fecha_fin']) ?></td>
              <td>
                <form method="get" style="display:inline">
                  <input type="hidden" name="editar" value="<?= $c['id'] ?>">
                  <button class="btn-edit" type="submit">Editar</button>
                </form>
                <form method="post" style="display:inline"
                      onsubmit="return confirm('¿Eliminar contrato #<?= $c['id'] ?>?');">
                  <input type="hidden" name="accion" value="eliminar">
                  <input type="hidden" name="id"     value="<?= $c['id'] ?>">
                  <button class="btn-delete" type="submit">Eliminar</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</div>

<?php include '_pie.php'; ?>
