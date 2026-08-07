<?php
/**
 * public/_cabecera.php
 * Cabecera HTML + barra de navegación reutilizable.
 *
 * Antes de incluir este archivo, define:
 *   $titulo  = 'Texto del título';
 *   $pagina  = 'inicio' | 'propiedades' | 'tenants' | 'contratos';  (para marcar activo)
 */
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$titulo = $titulo ?? 'Sistema de Alquileres';
$pagina = $pagina ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($titulo) ?> · Sistema de Alquileres</title>
  <link rel="stylesheet" href="estilo.css">
</head>
<body>

<nav>
  <span class="marca">🏠 Alquileres UPR</span>
  <a href="index.php"       class="<?= $pagina==='inicio'      ? 'activo':'' ?>">Inicio</a>
  <a href="propiedades.php" class="<?= $pagina==='propiedades' ? 'activo':'' ?>">Propiedades</a>
  <a href="tenants.php"     class="<?= $pagina==='tenants'     ? 'activo':'' ?>">Tenants</a>
  <a href="contratos.php"   class="<?= $pagina==='contratos'   ? 'activo':'' ?>">Contratos</a>
  <a href="reporte.php"     class="<?= $pagina==='reporte'     ? 'activo':'' ?>">Reporte</a>
</nav>

<main>

<?php
// Mostrar mensaje flash si existe
if (!empty($_SESSION['msg'])) {
    $clase = $_SESSION['msg']['tipo'] === 'ok' ? 'msg-ok' : 'msg-error';
    echo '<div class="msg '.$clase.'">'.htmlspecialchars($_SESSION['msg']['texto']).'</div>';
    unset($_SESSION['msg']);
}
?>
