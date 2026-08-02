<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("ver_bitacora", "../");

$modulo = isset($_GET['modulo']) ? limpiar_dato($conexion, $_GET['modulo']) : "";
$where = $modulo ? "WHERE modulo='$modulo'" : "";

$registros = mysqli_query($conexion, "SELECT * FROM bitacora $where ORDER BY id_bitacora DESC LIMIT 200");
$modulos = mysqli_query($conexion, "SELECT DISTINCT modulo FROM bitacora WHERE modulo<>'' ORDER BY modulo");

$raiz = "../"; $pagina_activa = "bitacora"; $titulo_pagina = "Bitácora";
include("../includes/header.php");
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 animar gap-2">
    <div>
        <h4 class="mb-1"><i class="bi bi-clock-history me-2 text-success"></i>Bitácora de acciones</h4>
        <p class="text-muted mb-0">Historial de actividad del sistema</p>
    </div>
    <form method="GET" class="d-flex gap-2">
        <select name="modulo" class="form-select" onchange="this.form.submit()">
            <option value="">Todos los módulos</option>
            <?php while ($m = mysqli_fetch_assoc($modulos)): ?>
            <option value="<?php echo htmlspecialchars($m['modulo']); ?>" <?php echo $modulo==$m['modulo']?'selected':''; ?>><?php echo htmlspecialchars($m['modulo']); ?></option>
            <?php endwhile; ?>
        </select>
    </form>
</div>

<div class="pc-tabla-wrap animar">
<table class="pc-tabla">
<thead><tr><th>Fecha y hora</th><th>Usuario</th><th>Módulo</th><th>Acción</th></tr></thead>
<tbody>
<?php if (mysqli_num_rows($registros) === 0): ?>
<tr><td colspan="4" class="text-center text-muted py-4">Sin registros.</td></tr>
<?php endif; ?>
<?php while ($r = mysqli_fetch_assoc($registros)): ?>
<tr>
    <td><?php echo date('d/m/Y H:i', strtotime($r['fecha_hora'])); ?></td>
    <td><?php echo htmlspecialchars($r['usuario']); ?></td>
    <td><span class="badge rounded-pill bg-success-subtle text-success px-3 py-2"><?php echo htmlspecialchars($r['modulo'] ?: '—'); ?></span></td>
    <td><?php echo htmlspecialchars($r['accion']); ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
<?php include("../includes/footer.php"); ?>
