<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("gestionar_extravio", "../");

if (isset($_GET["resolver"])) {
    $idr = (int) $_GET["resolver"];
    mysqli_query($conexion, "UPDATE reportes_extravio SET estado='Resuelto' WHERE id_reporte=$idr");
    registrar_bitacora($conexion, "Marcó como resuelto un reporte de extravío", "Perdidos y encontrados");
    header("Location: lista_extravio.php"); exit();
}

$reportes = mysqli_query($conexion, "SELECT * FROM reportes_extravio ORDER BY fecha_registro DESC");

$raiz = "../"; $pagina_activa = "extravio"; $titulo_pagina = "Perdidos y encontrados";
include("../includes/header.php");
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 animar gap-2">
    <div>
        <h4 class="mb-1"><i class="bi bi-search-heart me-2 text-success"></i>Perdidos y encontrados</h4>
        <p class="text-muted mb-0">Reportes ciudadanos de animales extraviados</p>
    </div>
    <div class="d-flex gap-2">
        <a href="../publico/perdidos.php" target="_blank" class="btn btn-pc btn-pc-outline"><i class="bi bi-globe2 me-1"></i>Ver página pública</a>
        <a href="../reportes/pdf_extravio.php" target="_blank" class="btn btn-pc btn-pc-primario"><i class="bi bi-file-earmark-pdf me-1"></i>Reporte PDF</a>
        <a href="formulario.php" class="btn btn-pc btn-pc-verde"><i class="bi bi-plus-circle me-1"></i>Nuevo reporte</a>
    </div>
</div>

<div class="row g-3">
<?php if (mysqli_num_rows($reportes) === 0): ?>
<div class="col-12"><div class="pc-card animar"><div class="pc-card-body text-center text-muted py-4">No hay reportes registrados.</div></div></div>
<?php endif; ?>
<?php while ($r = mysqli_fetch_assoc($reportes)): ?>
<div class="col-md-6 col-lg-4">
    <div class="pc-card h-100 animar">
        <div class="pc-card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="badge rounded-pill <?php echo $r['tipo']=='Perdido'?'bg-danger-subtle text-danger':'bg-info-subtle text-info'; ?> px-3 py-2"><?php echo $r['tipo']; ?></span>
                <?php echo badge_estado($r['estado']); ?>
            </div>
            <h6 class="mb-1"><?php echo htmlspecialchars($r['nombre_animal'] ?: 'Animal sin nombre'); ?></h6>
            <p class="small text-muted mb-1"><?php echo htmlspecialchars($r['especie'] ?: ''); ?></p>
            <p class="small mb-2"><?php echo htmlspecialchars($r['descripcion']); ?></p>
            <p class="small text-muted mb-1"><i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($r['lugar']); ?></p>
            <p class="small text-muted mb-2"><i class="bi bi-calendar3 me-1"></i><?php echo date('d/m/Y', strtotime($r['fecha'])); ?></p>
            <?php if (!empty($r['recompensa'])): ?>
            <p class="small mb-2"><i class="bi bi-cash-coin me-1"></i><?php echo htmlspecialchars($r['recompensa']); ?></p>
            <?php endif; ?>
            <p class="small mb-3"><i class="bi bi-telephone me-1"></i><?php echo htmlspecialchars($r['contacto']); ?></p>
            <?php if ($r['estado'] !== 'Resuelto'): ?>
            <a href="?resolver=<?php echo $r['id_reporte']; ?>" class="btn btn-pc btn-pc-outline btn-sm" onclick="return confirm('¿Marcar como resuelto?')">Marcar resuelto</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endwhile; ?>
</div>
<?php include("../includes/footer.php"); ?>
