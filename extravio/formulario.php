<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("gestionar_extravio", "../");

if (isset($_POST["guardar"])) {
    $tipo = limpiar_dato($conexion, $_POST["tipo"]);
    $nombre_animal = limpiar_dato($conexion, $_POST["nombre_animal"]);
    $especie = limpiar_dato($conexion, $_POST["especie"]);
    $descripcion = limpiar_dato($conexion, $_POST["descripcion"]);
    $lugar = limpiar_dato($conexion, $_POST["lugar"]);
    $fecha = limpiar_dato($conexion, $_POST["fecha"]);
    $contacto = limpiar_dato($conexion, $_POST["contacto"]);
    $foto = subir_foto("foto", "../uploads/perros");
    $foto_sql = $foto ? "'$foto'" : "NULL";
    $recompensa = limpiar_dato($conexion, $_POST["recompensa"] ?? "");
    $recompensa_sql = $recompensa !== "" ? "'$recompensa'" : "NULL";
    $lat = isset($_POST["lat"]) && $_POST["lat"] !== "" ? (float) $_POST["lat"] : null;
    $lng = isset($_POST["lng"]) && $_POST["lng"] !== "" ? (float) $_POST["lng"] : null;
    $lat_sql = $lat !== null ? $lat : "NULL";
    $lng_sql = $lng !== null ? $lng : "NULL";

    mysqli_query($conexion, "INSERT INTO reportes_extravio(tipo, nombre_animal, especie, descripcion, lugar, fecha, contacto, foto, recompensa, lat, lng)
        VALUES('$tipo','$nombre_animal','$especie','$descripcion','$lugar','$fecha','$contacto',$foto_sql,$recompensa_sql,$lat_sql,$lng_sql)");
    registrar_bitacora($conexion, "Registró un reporte de \"$tipo\"", "Perdidos y encontrados");
    header("Location: lista_extravio.php"); exit();
}

$raiz = "../"; $pagina_activa = "extravio"; $titulo_pagina = "Nuevo reporte";
include("../includes/header.php");
?>
<div class="mb-4 animar">
    <h4 class="mb-1"><i class="bi bi-search-heart me-2 text-success"></i>Reportar animal perdido o encontrado</h4>
</div>
<div class="row justify-content-center">
<div class="col-lg-7"><div class="pc-card animar"><div class="pc-card-body">
<form method="POST" enctype="multipart/form-data" class="pc-form necesita-validacion row g-3" novalidate>
    <div class="col-md-6">
        <label class="form-label">Tipo de reporte *</label>
        <select name="tipo" class="form-select" required>
            <option value="Perdido">Perdido</option>
            <option value="Encontrado">Encontrado</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Especie</label>
        <select name="especie" class="form-select">
            <option value="Perro">Perro</option>
            <option value="Gato">Gato</option>
            <option value="Otro">Otro</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Nombre del animal (si se conoce)</label>
        <input type="text" name="nombre_animal" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label">Fecha *</label>
        <input type="date" name="fecha" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
    </div>
    <div class="col-12">
        <label class="form-label">Descripción *</label>
        <textarea name="descripcion" class="form-control" rows="2" required placeholder="Color, tamaño, señas particulares..."></textarea>
    </div>
    <div class="col-md-6">
        <label class="form-label">Lugar *</label>
        <input type="text" name="lugar" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Contacto *</label>
        <input type="text" name="contacto" class="form-control" placeholder="Teléfono o correo" required>
    </div>
    <div class="col-12">
        <label class="form-label">Fotografía</label>
        <input type="file" name="foto" accept="image/*" class="form-control">
    </div>
    <div class="col-md-6">
        <label class="form-label">Recompensa (opcional)</label>
        <input type="text" name="recompensa" class="form-control" placeholder="Ej. $500">
    </div>
    <div class="col-12">
        <label class="form-label">Última ubicación conocida (opcional — toca el mapa para marcarla)</label>
        <div id="mapaUbicacion" style="height:220px;border-radius:12px;overflow:hidden;border:1px solid var(--pc-borde);"></div>
        <input type="hidden" name="lat" id="campoLat">
        <input type="hidden" name="lng" id="campoLng">
    </div>
    <div class="col-12 d-flex gap-2 mt-2">
        <button type="submit" name="guardar" class="btn btn-pc btn-pc-verde"><i class="bi bi-save me-1"></i>Publicar reporte</button>
        <a href="lista_extravio.php" class="btn btn-pc btn-pc-outline">Cancelar</a>
    </div>
</form>
</div></div></div>
</div>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const mapaUb = L.map('mapaUbicacion').setView([18.9667, -98.7994], 13); // Ozumba, Edo. Méx.
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(mapaUb);
let marcadorUb = null;
mapaUb.on('click', function (e) {
    if (marcadorUb) mapaUb.removeLayer(marcadorUb);
    marcadorUb = L.marker(e.latlng).addTo(mapaUb);
    document.getElementById('campoLat').value = e.latlng.lat;
    document.getElementById('campoLng').value = e.latlng.lng;
});
</script>
<?php include("../includes/footer.php"); ?>
