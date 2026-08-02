<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
include("pdf/pdf_helper.php");
requerir_sesion("../");
requerir_permiso("ver_reportes", "../");

$resultado = mysqli_query($conexion, "SELECT v.*, p.nombre AS animal FROM vacunas v INNER JOIN perros p ON v.id_animal = p.id_perro ORDER BY v.fecha_aplicacion DESC");
registrar_bitacora($conexion, "Generó el reporte PDF de vacunas", "Reportes");

$html = '<p style="color:#6b7280;margin-bottom:10px;">' . mysqli_num_rows($resultado) . ' aplicación(es) registrada(s).</p>';
$html .= '<table class="datos"><thead><tr><th>Animal</th><th>Vacuna</th><th>Fecha de aplicación</th><th>Próxima fecha</th><th>Veterinario</th></tr></thead><tbody>';
if (mysqli_num_rows($resultado) === 0) {
    $html .= '<tr><td colspan="5" style="text-align:center;color:#9ca3af;">Aún no hay vacunas registradas.</td></tr>';
}
while ($f = mysqli_fetch_assoc($resultado)) {
    $html .= '<tr>'
        . '<td>' . htmlspecialchars($f['animal']) . '</td>'
        . '<td>' . htmlspecialchars($f['nombre_vacuna']) . '</td>'
        . '<td>' . date('d/m/Y', strtotime($f['fecha_aplicacion'])) . '</td>'
        . '<td>' . ($f['proxima_dosis'] ? date('d/m/Y', strtotime($f['proxima_dosis'])) : '—') . '</td>'
        . '<td>' . htmlspecialchars($f['veterinario'] ?: '—') . '</td>'
        . '</tr>';
}
$html .= '</tbody></table>';

generar_pdf_reporte("Reporte de vacunación", $html, "petchip_reporte_vacunas");
