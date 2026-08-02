<?php
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("crear_animales", "../");

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
$observaciones = limpiar_dato($conexion, $_POST["observaciones"]);
$esterilizado = isset($_POST["esterilizado"]) ? 1 : 0;
$estado = limpiar_dato($conexion, $_POST["estado"] ?: "Activo");
$id_dueno = (int) $_POST["id_dueno"];
$foto = subir_foto("foto", "../uploads/perros");
$foto_sql = $foto ? "'$foto'" : "NULL";

$sql = "INSERT INTO perros(nombre, especie, raza, edad, sexo, color, peso, tamano, fecha_nacimiento,
        esterilizado, estado, colonia, foto, observaciones, id_dueno)
        VALUES('$nombre','$especie','$raza',$edad,'$sexo','$color',$peso,'$tamano',$fecha_nacimiento,
        $esterilizado,'$estado','$colonia','" . addslashes($observaciones) . "',$foto_sql,$id_dueno)";

if (mysqli_query($conexion, $sql)) {
    registrar_bitacora($conexion, "Registró a la mascota \"$nombre\"", "Mascotas");
    echo "<script>alert('Mascota registrada correctamente.'); window.location='lista_perros.php';</script>";
} else {
    echo "<script>alert('Error al registrar: " . addslashes(mysqli_error($conexion)) . "'); window.location='perros.php';</script>";
}

mysqli_close($conexion);
?>
