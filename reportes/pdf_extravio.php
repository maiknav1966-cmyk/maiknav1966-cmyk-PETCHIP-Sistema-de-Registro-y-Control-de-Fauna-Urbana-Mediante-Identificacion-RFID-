<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
include("pdf/pdf_helper.php");
requerir_sesion("../");
requerir_permiso("ver_reportes", "../");

$resultado = mysqli_query($conexion, "SELECT * FROM reportes_extravio WHERE tipo='Perdido' ORDER BY fecha DESC");
registrar_bitacora($conexion, "Generó el reporte PDF de animales perdidos", "Reportes");

$html = '<p style="color:#6b7280;margin-bottom:10px;">' . mysqli_num_rows($resultado) . ' reporte(s) de animales perdidos.</p>';
$html .= '<table class="datos"><thead><tr><th>Animal</th><th>Especie</th><th>Descripción</th><th>Lugar</th><th>Fecha</th><th>Contacto</th><th>Estado</th></tr></thead><tbody>';
if (mysqli_num_rows($resultado) === 0) {
    $html .= '<tr><td colspan="7" style="text-align:center;color:#9ca3af;">No hay reportes de animales perdidos.</td></tr>';
}
while ($f = mysqli_fetch_assoc($resultado)) {
    $clase = $f['estado'] === 'Resuelto' ? 'bg-verde' : 'bg-rojo';
    $html .= '<tr>'
        . '<td>' . htmlspecialchars($f['nombre_animal'] ?: 'Sin nombre') . '</td>'
        . '<td>' . htmlspecialchars($f['especie'] ?: '—') . '</td>'
        . '<td>' . htmlspecialchars($f['descripcion'] ?: '—') . '</td>'
        . '<td>' . htmlspecialchars($f['lugar'] ?: '—') . '</td>'
        . '<td>' . date('d/m/Y', strtotime($f['fecha'])) . '</td>'
        . '<td>' . htmlspecialchars($f['contacto']) . '</td>'
        . '<td><span class="badge-pdf ' . $clase . '">' . htmlspecialchars($f['estado']) . '</span></td>'
        . '</tr>';
}
$html .= '</tbody></table>';

generar_pdf_reporte("Reporte de animales perdidos", $html, "petchip_reporte_perdidos");
