<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
include("pdf/pdf_helper.php");
requerir_sesion("../");
requerir_permiso("ver_reportes", "../");

$resultado = mysqli_query($conexion, "SELECT t.*, p.nombre AS animal FROM tags_rfid t INNER JOIN perros p ON t.id_animal = p.id_perro ORDER BY t.id_tag DESC");
registrar_bitacora($conexion, "Generó el reporte PDF de chips de identificación", "Reportes");

$html = '<p style="color:#6b7280;margin-bottom:10px;">' . mysqli_num_rows($resultado) . ' Tag(s) asignado(s).</p>';
$html .= '<table class="datos"><thead><tr><th>Código</th><th>Fecha de asignación</th><th>Estado</th><th>Animal</th></tr></thead><tbody>';
while ($f = mysqli_fetch_assoc($resultado)) {
    $html .= '<tr>'
        . '<td>' . htmlspecialchars($f['codigo_tag']) . '</td>'
        . '<td>' . date('d/m/Y', strtotime($f['fecha_asignacion'])) . '</td>'
        . '<td>' . htmlspecialchars($f['estado'] ?: 'Activo') . '</td>'
        . '<td>' . htmlspecialchars($f['animal']) . '</td>'
        . '</tr>';
}
$html .= '</tbody></table>';

generar_pdf_reporte("Reporte de chips de identificación", $html, "petchip_reporte_tags");
