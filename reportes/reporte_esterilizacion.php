<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("ver_reportes", "../");

$resultado = mysqli_query($conexion, "SELECT nombre, especie, esterilizado, colonia, sexo FROM perros ORDER BY esterilizado, nombre");
registrar_bitacora($conexion, "Consultó el reporte de esterilización", "Reportes");

$raiz = "../"; $pagina_activa = "reportes"; $titulo_pagina = "Reporte de esterilización";
include("../includes/header.php");
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 animar gap-2 no-print">
    <div>
        <h4 class="mb-1"><i class="bi bi-clipboard2-pulse-fill me-2 text-success"></i>Reporte de esterilización</h4>
        <p class="text-muted mb-0">Útil para planear campañas municipales · Generado el <?php echo date('d/m/Y H:i'); ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="pdf_esterilizacion.php" target="_blank" class="btn btn-pc btn-pc-primario"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
        <a href="exportar_csv.php?tipo=esterilizacion" class="btn btn-pc btn-pc-verde"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
        <button onclick="window.print()" class="btn btn-pc btn-pc-outline"><i class="bi bi-printer me-1"></i>Imprimir</button>
    </div>
</div>

<div class="pc-tabla-wrap animar">
<table class="pc-tabla">
<thead><tr><th>Nombre</th><th>Especie</th><th>Sexo</th><th>Colonia</th><th>Esterilizado</th></tr></thead>
<tbody>
<?php while ($f = mysqli_fetch_assoc($resultado)): ?>
<tr>
    <td><?php echo htmlspecialchars($f['nombre']); ?></td>
    <td><?php echo htmlspecialchars($f['especie']); ?></td>
    <td><?php echo htmlspecialchars($f['sexo'] ?: '—'); ?></td>
    <td><?php echo htmlspecialchars($f['colonia'] ?: '—'); ?></td>
    <td><?php echo $f['esterilizado'] ? '<span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">Sí</span>' : '<span class="badge rounded-pill bg-warning-subtle text-warning px-3 py-2">Pendiente</span>'; ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
<?php include("../includes/footer.php"); ?>
