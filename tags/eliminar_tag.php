<?php
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("eliminar_tags", "../");

$id = (int) $_GET["id"];
$fila = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT codigo_tag FROM tags_rfid WHERE id_tag=$id"));
$codigo = $fila["codigo_tag"] ?? "desconocido";

mysqli_query($conexion, "DELETE FROM tags_rfid WHERE id_tag=$id");
registrar_bitacora($conexion, "Eliminó el chip de identificación \"$codigo\"", "Chips de identificación");

header("Location: lista_tags.php");
exit();
?>
