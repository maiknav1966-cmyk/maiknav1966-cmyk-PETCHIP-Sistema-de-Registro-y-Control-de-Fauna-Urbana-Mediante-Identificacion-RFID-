<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
include("pdf/pdf_helper.php");
requerir_sesion("../");
requerir_permiso("ver_reportes", "../");

$resultado = mysqli_query($conexion, "SELECT p.*, d.nombre AS dueno FROM perros p INNER JOIN duenos d ON p.id_dueno = d.id_dueno ORDER BY p.nombre");
registrar_bitacora($conexion, "Generó el reporte PDF de animales", "Reportes");

$html = '<p style="color:#6b7280;margin-bottom:10px;">' . mysqli_num_rows($resultado) . ' animal(es) registrado(s) en el sistema.</p>';
$html .= '<table class="datos"><thead><tr><th>Nombre</th><th>Especie</th><th>Raza</th><th>Edad</th><th>Sexo</th><th>Esterilizado</th><th>Estado</th><th>Colonia</th><th>Dueño</th></tr></thead><tbody>';
while ($f = mysqli_fetch_assoc($resultado)) {
    $html .= '<tr>'
        . '<td>' . htmlspecialchars($f['nombre']) . '</td>'
        . '<td>' . htmlspecialchars($f['especie']) . '</td>'
        . '<td>' . htmlspecialchars($f['raza'] ?: '—') . '</td>'
        . '<td>' . htmlspecialchars($f['edad'] ?: '—') . '</td>'
        . '<td>' . htmlspecialchars($f['sexo'] ?: '—') . '</td>'
        . '<td>' . ($f['esterilizado'] ? 'Sí' : 'No') . '</td>'
        . '<td>' . htmlspecialchars($f['estado'] ?: 'Activo') . '</td>'
        . '<td>' . htmlspecialchars($f['colonia'] ?: '—') . '</td>'
        . '<td>' . htmlspecialchars($f['dueno']) . '</td>'
        . '</tr>';
}
$html .= '</tbody></table>';

generar_pdf_reporte("Reporte general de animales", $html, "petchip_reporte_animales");
