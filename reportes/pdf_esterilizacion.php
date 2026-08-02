<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
include("pdf/pdf_helper.php");
requerir_sesion("../");
requerir_permiso("ver_reportes", "../");

$resultado = mysqli_query($conexion, "SELECT nombre, especie, esterilizado, colonia, sexo FROM perros ORDER BY esterilizado, nombre");
registrar_bitacora($conexion, "Generó el reporte PDF de esterilización", "Reportes");

$total = mysqli_num_rows($resultado);
$html = '<p style="color:#6b7280;margin-bottom:10px;">' . $total . ' animal(es) registrado(s) · útil para planear campañas municipales.</p>';
$html .= '<table class="datos"><thead><tr><th>Nombre</th><th>Especie</th><th>Sexo</th><th>Colonia</th><th>Esterilizado</th></tr></thead><tbody>';
while ($f = mysqli_fetch_assoc($resultado)) {
    $clase = $f['esterilizado'] ? 'bg-verde' : 'bg-ambar';
    $texto = $f['esterilizado'] ? 'Sí' : 'Pendiente';
    $html .= '<tr>'
        . '<td>' . htmlspecialchars($f['nombre']) . '</td>'
        . '<td>' . htmlspecialchars($f['especie']) . '</td>'
        . '<td>' . htmlspecialchars($f['sexo'] ?: '—') . '</td>'
        . '<td>' . htmlspecialchars($f['colonia'] ?: '—') . '</td>'
        . '<td><span class="badge-pdf ' . $clase . '">' . $texto . '</span></td>'
        . '</tr>';
}
$html .= '</tbody></table>';

generar_pdf_reporte("Reporte de esterilizaciones", $html, "petchip_reporte_esterilizacion");
