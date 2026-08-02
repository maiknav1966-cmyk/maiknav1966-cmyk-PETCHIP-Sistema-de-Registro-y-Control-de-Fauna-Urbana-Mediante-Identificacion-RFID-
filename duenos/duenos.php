<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("crear_duenos", "../");

$mensaje = ""; $tipo_mensaje = "success";

if (isset($_POST["guardar"])) {

    $nombre = limpiar_dato($conexion, $_POST["nombre"]);
    $telefono = limpiar_dato($conexion, $_POST["telefono"]);
    $correo = limpiar_dato($conexion, $_POST["correo"]);
    $direccion = limpiar_dato($conexion, $_POST["direccion"]);
    $colonia = limpiar_dato($conexion, $_POST["colonia"]);
    $municipio = limpiar_dato($conexion, $_POST["municipio"] ?: "Ozumba");
    $codigo_postal = limpiar_dato($conexion, $_POST["codigo_postal"]);
    $foto = subir_foto("foto", "../uploads/duenos");
    $foto_sql = $foto ? "'$foto'" : "NULL";

    $sql = "INSERT INTO duenos(nombre, telefono, correo, direccion, colonia, municipio, codigo_postal, foto)
            VALUES('$nombre','$telefono','$correo','$direccion','$colonia','$municipio','$codigo_postal',$foto_sql)";

    if (mysqli_query($conexion, $sql)) {
        registrar_bitacora($conexion, "Registró al dueño \"$nombre\"", "Dueños");
        $mensaje = "Dueño registrado correctamente.";
    } else {
        $tipo_mensaje = "danger";
        $mensaje = "Error: " . mysqli_error($conexion);
    }
}

$raiz = "../";
$pagina_activa = "duenos";
$titulo_pagina = "Registrar dueño";
include("../includes/header.php");
?>

<div class="d-flex justify-content-between align-items-center mb-4 animar">
    <div>
        <h4 class="mb-1"><i class="bi bi-person-plus-fill me-2 text-success"></i>Registrar dueño</h4>
        <p class="text-muted mb-0">Captura los datos del propietario responsable</p>
    </div>
    <a href="lista_duenos.php" class="btn btn-pc btn-pc-outline"><i class="bi bi-list-ul me-1"></i>Ver lista</a>
</div>

<?php if ($mensaje): ?>
<div class="alert alert-<?php echo $tipo_mensaje; ?> pc-alert alert-auto animar"><?php echo htmlspecialchars($mensaje); ?></div>
<?php endif; ?>

<div class="pc-card animar">
    <div class="pc-card-body">
        <form method="POST" enctype="multipart/form-data" class="pc-form row g-3 necesita-validacion" novalidate>

            <div class="col-12 d-flex align-items-center gap-3 mb-2">
                <img id="previewFotoDueno" src="../img/logo.png" class="foto-preview">
                <div>
                    <label class="form-label mb-1">Fotografía (opcional)</label>
                    <input type="file" name="foto" accept="image/*" class="form-control input-foto" data-preview="#previewFotoDueno">
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Nombre completo *</label>
                <input type="text" name="nombre" class="form-control" required>
                <div class="invalid-feedback">Este campo es obligatorio.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Teléfono *</label>
                <input type="text" name="telefono" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Correo electrónico</label>
                <input type="email" name="correo" class="form-control" placeholder="correo@ejemplo.com">
            </div>
            <div class="col-md-6">
                <label class="form-label">Colonia / Localidad</label>
                <input type="text" name="colonia" id="colonia" class="form-control">
            </div>

            <div class="col-md-7">
                <label class="form-label">Dirección *</label>
                <input type="text" name="direccion" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Municipio</label>
                <input type="text" name="municipio" id="municipio" class="form-control" value="Ozumba">
            </div>
            <div class="col-md-2">
                <label class="form-label">C.P. / Localidad</label>
                <select name="codigo_postal" id="codigo_postal" class="form-select" data-colonia="#colonia" data-municipio="#municipio">
                    <option value="">Selecciona...</option>
                    <optgroup label="Ozumba de Alzate">
                        <option value="56800" data-colonia="Ozumba de Alzate (Cabecera municipal)" data-municipio="Ozumba">Ozumba de Alzate (Cabecera municipal) — 56800</option>
                        <option value="56800" data-colonia="San Vicente Chimalhuacán" data-municipio="Ozumba">San Vicente Chimalhuacán — 56800</option>
                        <option value="56800" data-colonia="San Mateo Tecalco" data-municipio="Ozumba">San Mateo Tecalco — 56800</option>
                        <option value="56800" data-colonia="Santiago Mamalhuazuca" data-municipio="Ozumba">Santiago Mamalhuazuca — 56800</option>
                        <option value="56800" data-colonia="San José Tlacotitlán" data-municipio="Ozumba">San José Tlacotitlán — 56800</option>
                    </optgroup>
                    <optgroup label="Municipios de alrededores">
                        <option value="56880" data-colonia="Tepetlixpa" data-municipio="Tepetlixpa">Tepetlixpa — 56880</option>
                        <option value="56890" data-colonia="Nepantla de Sor Juana Inés de la Cruz" data-municipio="Tepetlixpa">Nepantla de Sor Juana Inés de la Cruz (Tepetlixpa) — 56890</option>
                        <option value="56970" data-colonia="Atlautla de Victoria" data-municipio="Atlautla">Atlautla de Victoria — 56970</option>
                        <option value="56983" data-colonia="Popo Park" data-municipio="Atlautla">Popo Park (Atlautla) — 56983</option>
                        <option value="56900" data-colonia="Amecameca de Juárez" data-municipio="Amecameca">Amecameca de Juárez — 56900</option>
                        <option value="56860" data-colonia="Juchitepec" data-municipio="Juchitepec">Juchitepec — 56860</option>
                    </optgroup>
                </select>
            </div>

            <div class="col-12 d-flex gap-2 mt-3">
                <button type="submit" name="guardar" class="btn btn-pc btn-pc-verde"><i class="bi bi-save me-1"></i>Guardar dueño</button>
                <a href="../menu.php" class="btn btn-pc btn-pc-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
<script>
document.getElementById("codigo_postal").addEventListener("change", function () {
    const opcion = this.options[this.selectedIndex];
    const colonia = opcion.getAttribute("data-colonia");
    const municipio = opcion.getAttribute("data-municipio");
    if (colonia) document.getElementById("colonia").value = colonia;
    if (municipio) document.getElementById("municipio").value = municipio;
});
</script>
