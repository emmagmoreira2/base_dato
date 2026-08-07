<?php
/**
 * public/tenants.php
 * CRUD de inquilinos.
 */
require __DIR__ . '/../config/conexion.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    try {
        if ($accion === 'crear') {
            $stmt = $pdo->prepare('INSERT INTO tenant (nombre, telefono) VALUES (:n, :t)');
            $stmt->execute([
                ':n' => trim($_POST['nombre']),
                ':t' => trim($_POST['telefono']),
            ]);
            $_SESSION['msg'] = ['tipo'=>'ok','texto'=>'Tenant añadido.'];
        }
        elseif ($accion === 'editar') {
            $stmt = $pdo->prepare(
                'UPDATE tenant SET nombre = :n, telefono = :t WHERE id = :id'
            );
            $stmt->execute([
                ':id' => (int)$_POST['id'],
                ':n'  => trim($_POST['nombre']),
                ':t'  => trim($_POST['telefono']),
            ]);
            $_SESSION['msg'] = ['tipo'=>'ok','texto'=>'Tenant actualizado.'];
        }
        elseif ($accion === 'eliminar') {
            $stmt = $pdo->prepare('DELETE FROM tenant WHERE id = :id');
            $stmt->execute([':id' => (int)$_POST['id']]);
            $_SESSION['msg'] = ['tipo'=>'ok','texto'=>'Tenant eliminado.'];
        }
    } catch (PDOException $e) {
        $_SESSION['msg'] = ['tipo'=>'error','texto'=>'Error: '.$e->getMessage()];
    }
    header('Location: tenants.php');
    exit;
}

$editando = null;
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare('SELECT * FROM tenant WHERE id = :id');
    $stmt->execute([':id' => (int)$_GET['editar']]);
    $editando = $stmt->fetch();
}

$tenants = $pdo->query('SELECT * FROM tenant ORDER BY id DESC')->fetchAll();

$titulo = 'Tenants';
$pagina = 'tenants';
require __DIR__ . '/_cabecera.php';
?>

<h1>Gestión de Tenants (Inquilinos)</h1>
<p>Esta página también utiliza <code>INSERT</code>, <code>UPDATE</code> y <code>DELETE</code>
sobre la tabla <code>tenant</code>.</p>

<div class="split">

  <div class="card">
    <h2><?= $editando ? 'Editar tenant' : 'Añadir nuevo tenant' ?></h2>
    <form method="POST" action="tenants.php">
      <input type="hidden" name="accion" value="<?= $editando ? 'editar' : 'crear' ?>">
      <?php if ($editando): ?>
        <input type="hidden" name="id" value="<?= (int)$editando['id'] ?>">
      <?php endif; ?>

      <div class="campo">
        <label>Nombre completo</label>
        <input type="text" name="nombre" required
               value="<?= htmlspecialchars($editando['nombre'] ?? '') ?>">
      </div>

      <div class="campo">
        <label>Teléfono</label>
        <input type="tel" name="telefono" required placeholder="787-555-0000"
               value="<?= htmlspecialchars($editando['telefono'] ?? '') ?>">
      </div>

      <button type="submit" class="btn btn-primario">
        <?= $editando ? 'Guardar cambios' : 'Añadir tenant' ?>
      </button>
      <?php if ($editando): ?>
        <a href="tenants.php" class="btn btn-secundario">Cancelar</a>
      <?php endif; ?>
    </form>
  </div>

  <div class="card">
    <h2>Listado de tenants (<?= count($tenants) ?>)</h2>
    <?php if (!$tenants): ?>
      <p>No hay tenants registrados todavía.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr><th>ID</th><th>Nombre</th><th>Teléfono</th><th>Acciones</th></tr>
        </thead>
        <tbody>
        <?php foreach ($tenants as $t): ?>
          <tr>
            <td><?= (int)$t['id'] ?></td>
            <td><?= htmlspecialchars($t['nombre']) ?></td>
            <td><?= htmlspecialchars($t['telefono']) ?></td>
            <td>
              <a href="tenants.php?editar=<?= (int)$t['id'] ?>" class="btn btn-secundario">Editar</a>
              <form method="POST" action="tenants.php" style="display:inline;"
                    onsubmit="return confirm('¿Eliminar este tenant? Esto también eliminará sus contratos.');">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                <button type="submit" class="btn btn-peligro">Eliminar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

</div>

<?php require __DIR__ . '/_pie.php'; ?>
