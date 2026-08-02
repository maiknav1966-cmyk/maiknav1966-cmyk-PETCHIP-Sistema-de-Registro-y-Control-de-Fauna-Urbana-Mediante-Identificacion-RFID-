<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("gestionar_campanas", "../");

if (isset($_GET["cambiar_estado"])) {
    $idc = (int) $_GET["id"];
    $nuevo = limpiar_dato($conexion, $_GET["cambiar_estado"]);
    mysqli_query($conexion, "UPDATE campanas_esterilizacion SET estado='$nuevo' WHERE id_campania=$idc");
    registrar_bitacora($conexion, "Actualizó el estado de una campaña a \"$nuevo\"", "Campañas");
    header("Location: lista_campanas.php"); exit();
}

$campanas = mysqli_query($conexion, "SELECT ce.*,
        (SELECT COUNT(*) FROM campanas_atendidos ca WHERE ca.id_campana = ce.id_campania) AS realizadas
    FROM campanas_esterilizacion ce ORDER BY ce.fecha_inicio DESC");

// ---- Tarjetas resumen y estadisticas del modulo ----
function contar_campanas($conexion, $sql) {
    $r = mysqli_query($conexion, $sql);
    if (!$r) return 0;
    $f = mysqli_fetch_row($r);
    return (int) $f[0];
}
$total_activas    = contar_campanas($conexion, "SELECT COUNT(*) FROM campanas_esterilizacion WHERE estado IN ('Programada','En curso')");
$total_finalizadas= contar_campanas($conexion, "SELECT COUNT(*) FROM campanas_esterilizacion WHERE estado='Finalizada'");
$total_canceladas = contar_campanas($conexion, "SELECT COUNT(*) FROM campanas_esterilizacion WHERE estado='Cancelada'");
$r_cupos = mysqli_query($conexion, "SELECT SUM(GREATEST(ce.meta_animales - (SELECT COUNT(*) FROM campanas_atendidos ca WHERE ca.id_campana = ce.id_campania), 0)) AS disp
    FROM campanas_esterilizacion ce WHERE ce.estado IN ('Programada','En curso')");
$cupos_disponibles = (int) (mysqli_fetch_assoc($r_cupos)['disp'] ?? 0);
$total_atendidos = contar_campanas($conexion, "SELECT COUNT(*) FROM campanas_atendidos");

// Datos para la grafica: cupo vs. realizadas por campana
$r_grafica = mysqli_query($conexion, "SELECT ce.nombre, ce.meta_animales,
        (SELECT COUNT(*) FROM campanas_atendidos ca WHERE ca.id_campana = ce.id_campania) AS realizadas
    FROM campanas_esterilizacion ce ORDER BY ce.fecha_inicio DESC LIMIT 8");
$campanas_labels = []; $campanas_cupo = []; $campanas_realizadas = [];
while ($g = mysqli_fetch_assoc($r_grafica)) {
    $campanas_labels[] = $g['nombre'];
    $campanas_cupo[] = (int) $g['meta_animales'];
    $campanas_realizadas[] = (int) $g['realizadas'];
}

$raiz = "../"; $pagina_activa = "campanas"; $titulo_pagina = "Campañas de esterilización";
include("../includes/header.php");
?>
<div class="d-flex justify-content-between align-items-center mb-4 animar">
    <div>
        <h4 class="mb-1"><i class="bi bi-calendar2-heart-fill me-2 text-success"></i>Campañas de esterilización</h4>
        <p class="text-muted mb-0">Jornadas municipales programadas</p>
    </div>
    <div class="d-flex gap-2">
        <a href="../reportes/pdf_campanas.php" target="_blank" class="btn btn-pc btn-pc-primario"><i class="bi bi-file-earmark-pdf me-1"></i>Reporte PDF</a>
        <a href="campanas.php" class="btn btn-pc btn-pc-verde"><i class="bi bi-plus-circle me-1"></i>Nueva campaña</a>
    </div>
</div>

<?php if (isset($_GET['atendido'])): ?>
<div class="alert alert-success pc-alert alert-auto d-flex align-items-center gap-2 animar">
    <i class="bi bi-check-circle-fill"></i> Animal atendido registrado correctamente.
</div>
<?php endif; ?>

<!-- TARJETAS RESUMEN -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card bg-grad-verde animar animar-1">
            <i class="bi bi-play-circle-fill icono-fondo"></i>
            <div class="valor"><?php echo $total_activas; ?></div>
            <div class="etiqueta">Campañas activas</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card bg-grad-azul animar animar-2">
            <i class="bi bi-check-circle-fill icono-fondo"></i>
            <div class="valor"><?php echo $total_finalizadas; ?></div>
            <div class="etiqueta">Finalizadas</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card bg-grad-ambar animar animar-3">
            <i class="bi bi-clipboard2-heart-fill icono-fondo"></i>
            <div class="valor"><?php echo $cupos_disponibles; ?></div>
            <div class="etiqueta">Cupos disponibles</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card bg-grad-morado animar animar-4">
            <i class="bi bi-clipboard2-check-fill icono-fondo"></i>
            <div class="valor"><?php echo $total_atendidos; ?></div>
            <div class="etiqueta">Animales atendidos (histórico)</div>
        </div>
    </div>
</div>

<div class="row g-3">
<?php if (mysqli_num_rows($campanas) === 0): ?>
<div class="col-12"><div class="pc-card animar"><div class="pc-card-body text-center text-muted py-4">No hay campañas registradas.</div></div></div>
<?php endif; ?>
<?php while ($c = mysqli_fetch_assoc($campanas)):
    $porcentaje = $c['meta_animales'] > 0 ? min(100, round($c['realizadas']/$c['meta_animales']*100)) : 0;
?>
<div class="col-md-6 col-lg-4">
    <div class="pc-card h-100 animar">
        <div class="pc-card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="mb-0"><?php echo htmlspecialchars($c['nombre']); ?></h6>
                <?php echo badge_estado($c['estado']); ?>
            </div>
            <p class="small text-muted mb-1"><i class="bi bi-calendar3 me-1"></i><?php echo date('d/m/Y', strtotime($c['fecha_inicio'])); ?></p>
            <p class="small text-muted mb-3"><i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($c['ubicacion']); ?></p>
            <div class="progress mb-2" style="height:8px;">
                <div class="progress-bar bg-success" style="width:<?php echo $porcentaje; ?>%"></div>
            </div>
            <p class="small mb-3"><?php echo $c['realizadas']; ?> / <?php echo $c['meta_animales']; ?> esterilizaciones realizadas (<?php echo $porcentaje; ?>%)</p>
            <div class="d-flex gap-2 flex-wrap">
                <a href="registrar_atendido.php?id=<?php echo $c['id_campania']; ?>" class="btn btn-pc btn-pc-verde btn-sm"><i class="bi bi-plus-circle me-1"></i>Registrar atendido</a>
                <div class="dropdown">
                    <button class="btn btn-pc btn-pc-outline btn-sm dropdown-toggle" data-bs-toggle="dropdown">Cambiar estado</button>
                    <ul class="dropdown-menu">
                        <?php foreach (["Programada","En curso","Finalizada","Cancelada"] as $e): ?>
                        <li><a class="dropdown-item" href="?cambiar_estado=<?php echo urlencode($e); ?>&id=<?php echo $c['id_campania']; ?>"><?php echo $e; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endwhile; ?>
</div>

<!-- ESTADISTICAS DEL MODULO -->
<?php if (count($campanas_labels) > 0): ?>
<div class="pc-card mt-4 animar">
    <div class="pc-card-body">
        <h6 class="mb-3"><i class="bi bi-bar-chart-line-fill me-2 text-success"></i>Estadísticas del módulo — cupo vs. atendidos</h6>
        <canvas id="graficoCampanas" height="90"></canvas>
    </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
<?php if (count($campanas_labels) > 0): ?>
new Chart(document.getElementById('graficoCampanas'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($campanas_labels); ?>,
        datasets: [
            { label: 'Cupo', data: <?php echo json_encode($campanas_cupo); ?>, backgroundColor: '#94a3b8' },
            { label: 'Atendidos', data: <?php echo json_encode($campanas_realizadas); ?>, backgroundColor: '#1C9756' }
        ]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
});
<?php endif; ?>
</script>
<?php include("../includes/footer.php"); ?>
