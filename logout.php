<?php
session_start();
include("config/conexion.php");
include("includes/funciones.php");

if (isset($_SESSION["usuario"])) {
    registrar_bitacora($conexion, "Cierre de sesión", "Autenticación");
}

session_destroy();
header("Location: login.php");
exit();
?>
