<?php
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("gestionar_tags", "../");

$id = (int) $_POST["id_tag"];
$codigo_tag = limpiar_dato($conexion, $_POST["codigo_tag"]);
$fecha_asignacion = limpiar_dato($conexion, $_POST["fecha_asignacion"]);
$estado = limpiar_dato($conexion, $_POST["estado"] ?: "Activo");
$id_perro = (int) $_POST["id_perro"];

$sql = "UPDATE tags_rfid SET codigo_tag='$codigo_tag', fecha_asignacion='$fecha_asignacion',
        estado='$estado', id_animal=$id_perro WHERE id_tag=$id";

if (mysqli_query($conexion, $sql)) {
    registrar_bitacora($conexion, "Actualizó el chip de identificación \"$codigo_tag\"", "Chips de identificación");
    echo "<script>alert('Chip de identificación actualizado correctamente.'); window.location='lista_tags.php';</script>";
} else {
    echo "<script>alert('Error al actualizar el chip de identificación: " . addslashes(mysqli_error($conexion)) . "'); window.location='lista_tags.php';</script>";
}

mysqli_close($conexion);
?>
