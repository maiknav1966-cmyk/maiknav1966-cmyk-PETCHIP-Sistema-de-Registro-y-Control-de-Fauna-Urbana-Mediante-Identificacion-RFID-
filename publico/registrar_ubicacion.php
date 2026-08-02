<?php
// Guarda la ubicación (lat/lng) de un escaneo público, solo si el
// id_lectura corresponde realmente al token de la mascota enviado.
// Evita que cualquiera pueda alterar lecturas ajenas.
include("../config/conexion.php");
include("../includes/funciones.php");
header("Content-Type: application/json");

$id_lectura = (int) ($_POST["id_lectura"] ?? 0);
$token = limpiar_dato($conexion, $_POST["t"] ?? "");
$lat = isset($_POST["lat"]) ? (float) $_POST["lat"] : null;
$lng = isset($_POST["lng"]) ? (float) $_POST["lng"] : null;

if ($id_lectura && $token !== "" && $lat !== null && $lng !== null) {
    $chk = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT lecturas_rfid.id_lectura
        FROM lecturas_rfid
        INNER JOIN tags_rfid ON lecturas_rfid.id_tag = tags_rfid.id_tag
        INNER JOIN perros ON tags_rfid.id_animal = perros.id_perro
        WHERE lecturas_rfid.id_lectura=$id_lectura AND perros.token_publico='$token'"));
    if ($chk) {
        mysqli_query($conexion, "UPDATE lecturas_rfid SET lat=$lat, lng=$lng WHERE id_lectura=$id_lectura");
        echo json_encode(["ok" => true]);
        exit();
    }
}
echo json_encode(["ok" => false]);
