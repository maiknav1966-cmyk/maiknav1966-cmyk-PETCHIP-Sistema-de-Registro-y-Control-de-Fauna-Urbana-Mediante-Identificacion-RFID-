<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
include("pdf/pdf_helper.php");
requerir_sesion("../");
requerir_permiso("ver_animales", "../");

$id = (int) ($_GET["id"] ?? 0);
$animal = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT perros.*, duenos.nombre AS dueno_nombre, duenos.telefono AS dueno_telefono, duenos.direccion AS dueno_direccion
    FROM perros INNER JOIN duenos ON perros.id_dueno = duenos.id_dueno WHERE perros.id_perro=$id"));

if (!$animal) { header("Location: ../perros/lista_perros.php"); exit(); }

$tag = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM tags_rfid WHERE id_animal=$id"));
$vacunas = mysqli_query($conexion, "SELECT * FROM vacunas WHERE id_animal=$id ORDER BY fecha_aplicacion DESC");
$historial = mysqli_query($conexion, "SELECT * FROM historial_veterinario WHERE id_animal=$id ORDER BY fecha DESC");
registrar_bitacora($conexion, "Generó la ficha PDF de \"{$animal['nombre']}\"", "Reportes");

$ruta_foto = $animal['foto'] ? realpath(dirname(__DIR__) . "/uploads/perros/" . $animal['foto']) : false;
$foto_src = $ruta_foto ? "file://" . $ruta_foto : "file://" . realpath(dirname(__DIR__) . "/img/logo.png");

$html = '
<table style="width:100%; margin-bottom:14px;">
<tr>
<td style="width:100px; vertical-align:top;"><img src="' . $foto_src . '" style="width:90px;height:90px;object-fit:cover;border-radius:8px;"></td>
<td style="vertical-align:top;">
    <div style="font-size:15px;font-weight:bold;color:#123E99;">' . htmlspecialchars($animal['nombre']) . '</div>
    <div style="color:#6b7280;margin-bottom:4px;">' . htmlspecialchars($animal['especie']) . ' · ' . htmlspecialchars($animal['raza'] ?: 'Sin raza') . '</div>
    <span class="badge-pdf bg-verde">' . htmlspecialchars($animal['estado'] ?: 'Activo') . '</span>
</td>
</tr>
</table>

<table class="datos" style="margin-bottom:14px;">
<tr><th colspan="2">Características</th></tr>
<tr><td style="width:40%;">Sexo</td><td>' . htmlspecialchars($animal['sexo'] ?: '—') . '</td></tr>
<tr><td>Edad</td><td>' . htmlspecialchars($animal['edad'] ?: '—') . ' años</td></tr>
<tr><td>Fecha de nacimiento</td><td>' . ($animal['fecha_nacimiento'] ? date('d/m/Y', strtotime($animal['fecha_nacimiento'])) : '—') . '</td></tr>
<tr><td>Color</td><td>' . htmlspecialchars($animal['color'] ?: '—') . '</td></tr>
<tr><td>Peso</td><td>' . htmlspecialchars($animal['peso'] ?: '—') . ' kg</td></tr>
<tr><td>Tamaño</td><td>' . htmlspecialchars($animal['tamano'] ?: '—') . '</td></tr>
<tr><td>Colonia</td><td>' . htmlspecialchars($animal['colonia'] ?: '—') . '</td></tr>
<tr><td>Esterilizado</td><td>' . ($animal['esterilizado'] ? 'Sí' : 'Pendiente') . '</td></tr>
</table>

<table class="datos" style="margin-bottom:14px;">
<tr><th colspan="2">Dueño e identificación RFID</th></tr>
<tr><td style="width:40%;">Dueño</td><td>' . htmlspecialchars($animal['dueno_nombre']) . '</td></tr>
<tr><td>Teléfono</td><td>' . htmlspecialchars($animal['dueno_telefono'] ?: '—') . '</td></tr>
<tr><td>Dirección</td><td>' . htmlspecialchars($animal['dueno_direccion'] ?: '—') . '</td></tr>
<tr><td>Código de chip de identificación</td><td>' . ($tag ? htmlspecialchars($tag['codigo_tag']) : 'Sin chip asignado') . '</td></tr>
</table>';

if ($animal['observaciones']) {
    $html .= '<table class="datos" style="margin-bottom:14px;"><tr><th>Observaciones</th></tr><tr><td>' . nl2br(htmlspecialchars($animal['observaciones'])) . '</td></tr></table>';
}

$html .= '<table class="datos" style="margin-bottom:14px;"><thead><tr><th colspan="4">Historial de vacunas</th></tr><tr><th>Vacuna</th><th>Aplicación</th><th>Próxima</th><th>Veterinario</th></tr></thead><tbody>';
if (mysqli_num_rows($vacunas) === 0) {
    $html .= '<tr><td colspan="4" style="text-align:center;color:#9ca3af;">Sin vacunas registradas.</td></tr>';
}
while ($v = mysqli_fetch_assoc($vacunas)) {
    $html .= '<tr><td>' . htmlspecialchars($v['nombre_vacuna']) . '</td><td>' . date('d/m/Y', strtotime($v['fecha_aplicacion'])) . '</td>'
        . '<td>' . ($v['proxima_dosis'] ? date('d/m/Y', strtotime($v['proxima_dosis'])) : '—') . '</td><td>' . htmlspecialchars($v['veterinario'] ?: '—') . '</td></tr>';
}
$html .= '</tbody></table>';

$html .= '<table class="datos"><thead><tr><th colspan="4">Línea de tiempo veterinaria</th></tr><tr><th>Fecha</th><th>Motivo</th><th>Diagnóstico</th><th>Tratamiento</th></tr></thead><tbody>';
if (mysqli_num_rows($historial) === 0) {
    $html .= '<tr><td colspan="4" style="text-align:center;color:#9ca3af;">Sin eventos veterinarios registrados.</td></tr>';
}
while ($h = mysqli_fetch_assoc($historial)) {
    $html .= '<tr><td>' . date('d/m/Y', strtotime($h['fecha'])) . '</td><td>' . htmlspecialchars($h['motivo']) . '</td>'
        . '<td>' . htmlspecialchars($h['diagnostico'] ?: '—') . '</td><td>' . htmlspecialchars($h['tratamiento'] ?: '—') . '</td></tr>';
}
$html .= '</tbody></table>';

generar_pdf_reporte("Ficha del animal — " . $animal['nombre'], $html, "petchip_ficha_" . $animal['id_perro']);
