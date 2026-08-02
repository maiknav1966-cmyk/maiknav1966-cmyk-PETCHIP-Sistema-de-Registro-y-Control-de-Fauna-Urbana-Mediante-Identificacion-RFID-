<?php
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("gestionar_tags", "../");

$codigo_tag = limpiar_dato($conexion, $_POST["codigo_tag"]);
$fecha_asignacion = limpiar_dato($conexion, $_POST["fecha_asignacion"]);
$id_perro = (int) $_POST["id_perro"];

$sql = "INSERT INTO tags_rfid(codigo_tag, fecha_asignacion, id_animal) VALUES('$codigo_tag','$fecha_asignacion',$id_perro)";

if (mysqli_query($conexion, $sql)) {
    registrar_bitacora($conexion, "Registró el chip de identificación \"$codigo_tag\"", "Chips de identificación");
    echo "<script>alert('Chip de identificación registrado correctamente.'); window.location='lista_tags.php';</script>";
} else {
    echo "<script>alert('Error al registrar el chip de identificación: " . addslashes(mysqli_error($conexion)) . "'); window.location='tags.php';</script>";
}

mysqli_close($conexion);
?>
