<?php
include("../config/conexion.php");
include("../includes/funciones.php");

$token = limpiar_dato($conexion, $_POST["t"] ?? "");
$animal = $token !== "" ? mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM perros WHERE token_publico='$token'")) : null;

if (!$animal) {
    header("Location: ficha.php");
    exit();
}

$id_perro = (int) $animal["id_perro"];
$nombre = limpiar_dato($conexion, $_POST["nombre"] ?? "");
$telefono = limpiar_dato($conexion, $_POST["telefono"] ?? "");
$lugar = limpiar_dato($conexion, $_POST["lugar"] ?? "");
$comentarios = limpiar_dato($conexion, $_POST["comentarios"] ?? "");
$lat = isset($_POST["lat"]) && $_POST["lat"] !== "" ? (float) $_POST["lat"] : null;
$lng = isset($_POST["lng"]) && $_POST["lng"] !== "" ? (float) $_POST["lng"] : null;
$lat_sql = $lat !== null ? $lat : "NULL";
$lng_sql = $lng !== null ? $lng : "NULL";

mysqli_query($conexion, "INSERT INTO avisos_encontrado(id_perro, nombre_reportante, telefono_reportante, comentarios, lugar, lat, lng)
    VALUES($id_perro, '$nombre', '$telefono', '$comentarios', '$lugar', $lat_sql, $lng_sql)");

$quien = $nombre !== "" ? $nombre : "Alguien";
$mensaje_notif = "$quien reportó haber encontrado a " . $animal["nombre"] . " en: $lugar";
crear_notificacion($conexion, (int) $animal["id_dueno"], "aviso_encontrado", $mensaje_notif, $id_perro);

registrar_bitacora($conexion, "Aviso público: encontraron a \"" . $animal["nombre"] . "\" en $lugar", "Avisar al dueño");

header("Location: ficha.php?t=" . urlencode($token) . "&aviso=1");
exit();
