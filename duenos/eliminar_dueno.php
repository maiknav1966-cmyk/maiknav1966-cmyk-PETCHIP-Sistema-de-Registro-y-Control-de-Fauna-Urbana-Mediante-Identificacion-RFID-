<?php
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("eliminar_duenos", "../");

$id = (int) $_GET["id"];

$fila = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT nombre FROM duenos WHERE id_dueno=$id"));
$nombre = $fila["nombre"] ?? "desconocido";

mysqli_query($conexion, "DELETE FROM duenos WHERE id_dueno=$id");
registrar_bitacora($conexion, "Eliminó al dueño \"$nombre\"", "Dueños");

header("Location: lista_duenos.php");
exit();
?>
