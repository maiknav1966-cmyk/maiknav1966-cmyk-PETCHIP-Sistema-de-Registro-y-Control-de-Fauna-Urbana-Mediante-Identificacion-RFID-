<?php
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("ver_reportes", "../");

$tipo = $_GET["tipo"] ?? "";
$mapa = [
    "perros"  => ["SELECT p.nombre, p.especie, p.raza, p.edad, p.sexo, p.estado, p.esterilizado, p.colonia, d.nombre AS dueno
                   FROM perros p INNER JOIN duenos d ON p.id_dueno = d.id_dueno ORDER BY p.nombre",
                   ["Nombre","Especie","Raza","Edad","Sexo","Estado","Esterilizado","Colonia","Dueño"]],
    "duenos"  => ["SELECT nombre, telefono, correo, direccion, colonia, municipio FROM duenos ORDER BY nombre",
                   ["Nombre","Teléfono","Correo","Dirección","Colonia","Municipio"]],
    "tags"    => ["SELECT t.codigo_tag, t.fecha_asignacion, t.estado, p.nombre AS animal
                   FROM tags_rfid t INNER JOIN perros p ON t.id_animal = p.id_perro ORDER BY t.id_tag DESC",
                   ["Código", "Fecha de asignación", "Estado", "Animal"]],
    "esterilizacion" => ["SELECT nombre, especie, esterilizado, colonia FROM perros ORDER BY esterilizado, nombre",
                   ["Nombre","Especie","Esterilizado","Colonia"]],
    "vacunas" => ["SELECT p.nombre AS animal, v.nombre_vacuna, v.fecha_aplicacion, v.proxima_dosis AS proxima_fecha, v.veterinario
                   FROM vacunas v INNER JOIN perros p ON v.id_animal = p.id_perro ORDER BY v.fecha_aplicacion DESC",
                   ["Animal","Vacuna","Fecha de aplicación","Próxima fecha","Veterinario"]],
];

if (!isset($mapa[$tipo])) { die("Reporte no válido."); }

[$sql, $encabezados] = $mapa[$tipo];
$resultado = mysqli_query($conexion, $sql);

header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=reporte_{$tipo}.csv");

$salida = fopen("php://output", "w");
fprintf($salida, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM para acentos en Excel
fputcsv($salida, $encabezados);

while ($fila = mysqli_fetch_assoc($resultado)) {
    fputcsv($salida, $fila);
}
fclose($salida);

registrar_bitacora($conexion, "Exportó el reporte \"$tipo\" a Excel", "Reportes");
exit();
?>
