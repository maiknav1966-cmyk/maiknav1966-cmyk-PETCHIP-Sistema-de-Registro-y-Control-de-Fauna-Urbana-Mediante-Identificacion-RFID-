<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("ver_reportes", "../");

$resultado = mysqli_query($conexion, "SELECT t.*, p.nombre AS animal FROM tags_rfid t INNER JOIN perros p ON t.id_animal = p.id_perro ORDER BY t.id_tag DESC");
registrar_bitacora($conexion, "Consultó el reporte de chips de identificación", "Reportes");

$raiz = "../"; $pagina_activa = "reportes"; $titulo_pagina = "Reporte de chips de identificación";
include("../includes/header.php");
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 animar gap-2 no-print">
    <div>
        <h4 class="mb-1"><i class="bi bi-tag-fill me-2 text-success"></i>Reporte de chips de identificación</h4>
        <p class="text-muted mb-0"><?php echo mysqli_num_rows($resultado); ?> registro(s) · Generado el <?php echo date('d/m/Y H:i'); ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="pdf_tags.php" target="_blank" class="btn btn-pc btn-pc-primario"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
        <a href="exportar_csv.php?tipo=tags" class="btn btn-pc btn-pc-verde"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
        <button onclick="window.print()" class="btn btn-pc btn-pc-outline"><i class="bi bi-printer me-1"></i>Imprimir</button>
    </div>
</div>

<div class="pc-tabla-wrap animar">
<table class="pc-tabla">
<thead><tr><th>Código</th><th>Fecha de asignación</th><th>Estado</th><th>Animal</th></tr></thead>
<tbody>
<?php while ($f = mysqli_fetch_assoc($resultado)): ?>
<tr>
    <td><?php echo htmlspecialchars($f['codigo_tag']); ?></td>
    <td><?php echo date('d/m/Y', strtotime($f['fecha_asignacion'])); ?></td>
    <td><?php echo htmlspecialchars($f['estado'] ?: 'Activo'); ?></td>
    <td><?php echo htmlspecialchars($f['animal']); ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
<?php include("../includes/footer.php"); ?>
