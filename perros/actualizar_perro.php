<?php
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("editar_animales", "../");

$id = (int) $_POST["id_perro"];
$nombre = limpiar_dato($conexion, $_POST["nombre"]);
$especie = limpiar_dato($conexion, $_POST["especie"]);
$raza_sel = $_POST["raza"] ?? "";
$raza_valor = ($raza_sel === "Otra") ? trim($_POST["raza_otra"] ?? "") : $raza_sel;
$raza = limpiar_dato($conexion, $raza_valor);
$edad = $_POST["edad"] !== "" ? (int) $_POST["edad"] : "NULL";
$sexo = limpiar_dato($conexion, $_POST["sexo"]);
$fecha_nacimiento = $_POST["fecha_nacimiento"] !== "" ? "'" . limpiar_dato($conexion, $_POST["fecha_nacimiento"]) . "'" : "NULL";
$color_sel = $_POST["color"] ?? "";
$color_valor = ($color_sel === "Otro") ? trim($_POST["color_otra"] ?? "") : $color_sel;
$color = limpiar_dato($conexion, $color_valor);
$peso = $_POST["peso"] !== "" ? (float) $_POST["peso"] : "NULL";
$tamano = limpiar_dato($conexion, $_POST["tamano"]);
$colonia = limpiar_dato($conexion, $_POST["colonia"]);
$observaciones = addslashes(limpiar_dato($conexion, $_POST["observaciones"]));
$esterilizado = isset($_POST["esterilizado"]) ? 1 : 0;
$estado = limpiar_dato($conexion, $_POST["estado"] ?: "Activo");
$id_dueno = (int) $_POST["id_dueno"];

$foto_nueva = subir_foto("foto", "../uploads/perros");
$set_foto = $foto_nueva ? ", foto='$foto_nueva'" : "";

$sql = "UPDATE perros SET
    nombre='$nombre', especie='$especie', raza='$raza', edad=$edad, sexo='$sexo',
    color='$color', peso=$peso, tamano='$tamano', fecha_nacimiento=$fecha_nacimiento,
    esterilizado=$esterilizado, estado='$estado', colonia='$colonia',
    observaciones='$observaciones', id_dueno=$id_dueno $set_foto
    WHERE id_perro=$id";

if (mysqli_query($conexion, $sql)) {
    registrar_bitacora($conexion, "Actualizó a la mascota \"$nombre\"", "Mascotas");
    echo "<script>alert('Mascota actualizada correctamente.'); window.location='lista_perros.php';</script>";
} else {
    echo "<script>alert('Error al actualizar: " . addslashes(mysqli_error($conexion)) . "'); window.location='lista_perros.php';</script>";
}

mysqli_close($conexion);
?>
