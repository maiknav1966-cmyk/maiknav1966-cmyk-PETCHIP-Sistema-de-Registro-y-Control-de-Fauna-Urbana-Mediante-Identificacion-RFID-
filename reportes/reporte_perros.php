<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("ver_reportes", "../");

$sql = "SELECT p.*, d.nombre AS dueno FROM perros p INNER JOIN duenos d ON p.id_dueno = d.id_dueno ORDER BY p.nombre";
$resultado = mysqli_query($conexion, $sql);
registrar_bitacora($conexion, "Consultó el reporte de animales", "Reportes");

$raiz = "../"; $pagina_activa = "reportes"; $titulo_pagina = "Reporte de animales";
include("../includes/header.php");
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 animar gap-2 no-print">
    <div>
        <h4 class="mb-1"><i class="bi bi-heart-fill me-2 text-success"></i>Reporte de animales registrados</h4>
        <p class="text-muted mb-0"><?php echo mysqli_num_rows($resultado); ?> registro(s) · Generado el <?php echo date('d/m/Y H:i'); ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="pdf_perros.php" target="_blank" class="btn btn-pc btn-pc-primario"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
        <a href="exportar_csv.php?tipo=perros" class="btn btn-pc btn-pc-verde"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
        <button onclick="window.print()" class="btn btn-pc btn-pc-outline"><i class="bi bi-printer me-1"></i>Imprimir</button>
    </div>
</div>

<div class="pc-tabla-wrap animar">
<table class="pc-tabla">
<thead><tr><th>Nombre</th><th>Especie</th><th>Raza</th><th>Edad</th><th>Sexo</th><th>Esterilizado</th><th>Estado</th><th>Colonia</th><th>Dueño</th></tr></thead>
<tbody>
<?php while ($f = mysqli_fetch_assoc($resultado)): ?>
<tr>
    <td><?php echo htmlspecialchars($f['nombre']); ?></td>
    <td><?php echo htmlspecialchars($f['especie']); ?></td>
    <td><?php echo htmlspecialchars($f['raza'] ?: '—'); ?></td>
    <td><?php echo htmlspecialchars($f['edad'] ?: '—'); ?></td>
    <td><?php echo htmlspecialchars($f['sexo'] ?: '—'); ?></td>
    <td><?php echo $f['esterilizado'] ? 'Sí' : 'No'; ?></td>
    <td><?php echo htmlspecialchars($f['estado'] ?: 'Activo'); ?></td>
    <td><?php echo htmlspecialchars($f['colonia'] ?: '—'); ?></td>
    <td><?php echo htmlspecialchars($f['dueno']); ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
<?php include("../includes/footer.php"); ?>
