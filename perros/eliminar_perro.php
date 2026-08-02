<?php
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("eliminar_animales", "../");

$id = (int) $_GET["id"];

$fila = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT nombre FROM perros WHERE id_perro=$id"));
$nombre = $fila["nombre"] ?? "desconocido";

mysqli_query($conexion, "DELETE FROM perros WHERE id_perro=$id");
registrar_bitacora($conexion, "Eliminó a la mascota \"$nombre\"", "Mascotas");

header("Location: lista_perros.php");
exit();
?>
