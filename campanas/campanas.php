<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("gestionar_campanas", "../");

if (isset($_POST["guardar"])) {
    $nombre = limpiar_dato($conexion, $_POST["nombre"]);
    $fecha = limpiar_dato($conexion, $_POST["fecha"]);
    $lugar = limpiar_dato($conexion, $_POST["lugar"]);
    $cupo = (int) $_POST["cupo"];
    mysqli_query($conexion, "INSERT INTO campanas_esterilizacion(nombre, fecha_inicio, ubicacion, meta_animales) VALUES('$nombre','$fecha','$lugar',$cupo)");
    registrar_bitacora($conexion, "Programó la campaña \"$nombre\"", "Campañas");
    header("Location: lista_campanas.php"); exit();
}

$raiz = "../"; $pagina_activa = "campanas"; $titulo_pagina = "Nueva campaña";
include("../includes/header.php");
?>
<div class="mb-4 animar">
    <h4 class="mb-1"><i class="bi bi-calendar2-heart-fill me-2 text-success"></i>Nueva campaña de esterilización</h4>
    <p class="text-muted mb-0">Programa una jornada municipal</p>
</div>
<div class="row justify-content-center">
<div class="col-lg-6"><div class="pc-card animar"><div class="pc-card-body">
<form method="POST" class="pc-form necesita-validacion" novalidate>
    <div class="mb-3"><label class="form-label">Nombre de la campaña *</label><input type="text" name="nombre" class="form-control" placeholder="Jornada de esterilización — Centro" required></div>
    <div class="mb-3"><label class="form-label">Fecha *</label><input type="date" name="fecha" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Lugar *</label><input type="text" name="lugar" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Cupo</label><input type="number" name="cupo" min="0" class="form-control" value="30"></div>
    <div class="d-flex gap-2">
        <button type="submit" name="guardar" class="btn btn-pc btn-pc-verde"><i class="bi bi-save me-1"></i>Guardar</button>
        <a href="lista_campanas.php" class="btn btn-pc btn-pc-outline">Cancelar</a>
    </div>
</form>
</div></div></div>
</div>
<?php include("../includes/footer.php"); ?>
