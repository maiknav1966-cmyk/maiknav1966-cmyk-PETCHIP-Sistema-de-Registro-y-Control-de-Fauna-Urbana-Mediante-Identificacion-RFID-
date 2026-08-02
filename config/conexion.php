<?php
// ============================================================
// Conexion a la base de datos - PetChip
// ============================================================
$host = "localhost";
$usuario = "root";
$contrasena = "";
$base_datos = "petchip";

$conexion = new mysqli($host, $usuario, $contrasena, $base_datos);

if ($conexion->connect_error) {
    die("Error de conexion: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");
?>
