<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
include("pdf/pdf_helper.php");
requerir_sesion("../");
requerir_permiso("ver_reportes", "../");

$resultado = mysqli_query($conexion, "SELECT d.*, (SELECT COUNT(*) FROM perros p WHERE p.id_dueno=d.id_dueno) AS total_mascotas FROM duenos d ORDER BY d.nombre");
registrar_bitacora($conexion, "Generó el reporte PDF de dueños", "Reportes");

$html = '<p style="color:#6b7280;margin-bottom:10px;">' . mysqli_num_rows($resultado) . ' dueño(s) registrado(s).</p>';
$html .= '<table class="datos"><thead><tr><th>Nombre</th><th>Teléfono</th><th>Correo</th><th>Dirección</th><th>Colonia</th><th>Municipio</th><th>Mascotas</th></tr></thead><tbody>';
while ($f = mysqli_fetch_assoc($resultado)) {
    $html .= '<tr>'
        . '<td>' . htmlspecialchars($f['nombre']) . '</td>'
        . '<td>' . htmlspecialchars($f['telefono'] ?: '—') . '</td>'
        . '<td>' . htmlspecialchars($f['correo'] ?: '—') . '</td>'
        . '<td>' . htmlspecialchars($f['direccion'] ?: '—') . '</td>'
        . '<td>' . htmlspecialchars($f['colonia'] ?: '—') . '</td>'
        . '<td>' . htmlspecialchars($f['municipio']) . '</td>'
        . '<td>' . $f['total_mascotas'] . '</td>'
        . '</tr>';
}
$html .= '</tbody></table>';

generar_pdf_reporte("Reporte de dueños registrados", $html, "petchip_reporte_duenos");
