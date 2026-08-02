<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("ver_reportes", "../");

$resultado = mysqli_query($conexion, "SELECT d.*, (SELECT COUNT(*) FROM perros p WHERE p.id_dueno=d.id_dueno) AS total_mascotas FROM duenos d ORDER BY d.nombre");
registrar_bitacora($conexion, "Consultó el reporte de dueños", "Reportes");

$raiz = "../"; $pagina_activa = "reportes"; $titulo_pagina = "Reporte de dueños";
include("../includes/header.php");
?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 animar gap-2 no-print">
    <div>
        <h4 class="mb-1"><i class="bi bi-people-fill me-2 text-success"></i>Reporte de dueños registrados</h4>
        <p class="text-muted mb-0"><?php echo mysqli_num_rows($resultado); ?> registro(s) · Generado el <?php echo date('d/m/Y H:i'); ?></p>
    </div>
    <div class="d-flex gap-2">
        <a href="pdf_duenos.php" target="_blank" class="btn btn-pc btn-pc-primario"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
        <a href="exportar_csv.php?tipo=duenos" class="btn btn-pc btn-pc-verde"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
        <button onclick="window.print()" class="btn btn-pc btn-pc-outline"><i class="bi bi-printer me-1"></i>Imprimir</button>
    </div>
</div>

<div class="pc-tabla-wrap animar">
<table class="pc-tabla">
<thead><tr><th>Nombre</th><th>Teléfono</th><th>Correo</th><th>Dirección</th><th>Colonia</th><th>Municipio</th><th>Mascotas</th></tr></thead>
<tbody>
<?php while ($f = mysqli_fetch_assoc($resultado)): ?>
<tr>
    <td><?php echo htmlspecialchars($f['nombre']); ?></td>
    <td><?php echo htmlspecialchars($f['telefono']); ?></td>
    <td><?php echo htmlspecialchars($f['correo'] ?: '—'); ?></td>
    <td><?php echo htmlspecialchars($f['direccion']); ?></td>
    <td><?php echo htmlspecialchars($f['colonia'] ?: '—'); ?></td>
    <td><?php echo htmlspecialchars($f['municipio']); ?></td>
    <td><?php echo $f['total_mascotas']; ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
<?php include("../includes/footer.php"); ?>
