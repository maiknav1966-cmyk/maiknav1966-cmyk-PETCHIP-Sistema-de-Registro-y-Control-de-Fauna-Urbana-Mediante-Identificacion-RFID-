<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
include("pdf/pdf_helper.php");
requerir_sesion("../");
requerir_permiso("ver_reportes", "../");

$resultado = mysqli_query($conexion, "SELECT ce.*,
        (SELECT COUNT(*) FROM campanas_atendidos ca WHERE ca.id_campana = ce.id_campania) AS realizadas
    FROM campanas_esterilizacion ce ORDER BY ce.fecha_inicio DESC");
registrar_bitacora($conexion, "Generó el reporte PDF de campañas", "Reportes");

$clases_estado = [
    "Programada" => "bg-ambar", "En curso" => "bg-verde",
    "Finalizada" => "bg-verde", "Cancelada" => "bg-rojo",
];

$html = '<p style="color:#6b7280;margin-bottom:10px;">' . mysqli_num_rows($resultado) . ' campaña(s) registrada(s).</p>';
$html .= '<table class="datos"><thead><tr><th>Campaña</th><th>Fecha</th><th>Lugar</th><th>Cupo</th><th>Atendidos</th><th>Avance</th><th>Estado</th></tr></thead><tbody>';
if (mysqli_num_rows($resultado) === 0) {
    $html .= '<tr><td colspan="7" style="text-align:center;color:#9ca3af;">No hay campañas registradas.</td></tr>';
}
while ($f = mysqli_fetch_assoc($resultado)) {
    $porcentaje = $f['meta_animales'] > 0 ? min(100, round($f['realizadas'] / $f['meta_animales'] * 100)) : 0;
    $clase = $clases_estado[$f['estado']] ?? 'bg-gris';
    $html .= '<tr>'
        . '<td>' . htmlspecialchars($f['nombre']) . '</td>'
        . '<td>' . date('d/m/Y', strtotime($f['fecha_inicio'])) . '</td>'
        . '<td>' . htmlspecialchars($f['ubicacion']) . '</td>'
        . '<td>' . $f['meta_animales'] . '</td>'
        . '<td>' . $f['realizadas'] . '</td>'
        . '<td>' . $porcentaje . '%</td>'
        . '<td><span class="badge-pdf ' . $clase . '">' . htmlspecialchars($f['estado']) . '</span></td>'
        . '</tr>';
}
$html .= '</tbody></table>';

generar_pdf_reporte("Reporte de campañas de esterilización", $html, "petchip_reporte_campanas");
