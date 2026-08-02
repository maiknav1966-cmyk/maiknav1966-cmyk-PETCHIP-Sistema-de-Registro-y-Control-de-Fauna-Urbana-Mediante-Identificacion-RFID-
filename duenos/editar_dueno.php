<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("editar_duenos", "../");

$id = (int) $_GET["id"];

if (isset($_POST["actualizar"])) {

    $nombre = limpiar_dato($conexion, $_POST["nombre"]);
    $telefono = limpiar_dato($conexion, $_POST["telefono"]);
    $correo = limpiar_dato($conexion, $_POST["correo"]);
    $direccion = limpiar_dato($conexion, $_POST["direccion"]);
    $colonia = limpiar_dato($conexion, $_POST["colonia"]);
    $municipio = limpiar_dato($conexion, $_POST["municipio"]);
    $codigo_postal = limpiar_dato($conexion, $_POST["codigo_postal"]);

    $foto_nueva = subir_foto("foto", "../uploads/duenos");
    $set_foto = $foto_nueva ? ", foto='$foto_nueva'" : "";

    $sql = "UPDATE duenos SET nombre='$nombre', telefono='$telefono', correo='$correo',
            direccion='$direccion', colonia='$colonia', municipio='$municipio',
            codigo_postal='$codigo_postal' $set_foto WHERE id_dueno=$id";

    mysqli_query($conexion, $sql);
    registrar_bitacora($conexion, "Actualizó al dueño \"$nombre\"", "Dueños");

    header("Location: lista_duenos.php");
    exit();
}

$sql = "SELECT * FROM duenos WHERE id_dueno=$id";
$resultado = mysqli_query($conexion, $sql);
$fila = mysqli_fetch_assoc($resultado);

if (!$fila) { header("Location: lista_duenos.php"); exit(); }

$raiz = "../";
$pagina_activa = "duenos";
$titulo_pagina = "Editar dueño";
include("../includes/header.php");
?>

<div class="mb-4 animar">
    <h4 class="mb-1"><i class="bi bi-pencil-square me-2 text-success"></i>Editar dueño</h4>
    <p class="text-muted mb-0">Actualiza la información de <?php echo htmlspecialchars($fila['nombre']); ?></p>
</div>

<div class="pc-card animar">
    <div class="pc-card-body">
        <form method="POST" enctype="multipart/form-data" class="pc-form row g-3 necesita-validacion" novalidate>

            <div class="col-12 d-flex align-items-center gap-3 mb-2">
                <img id="previewFotoDueno" src="<?php echo $fila['foto'] ? '../uploads/duenos/'.htmlspecialchars($fila['foto']) : '../img/logo.png'; ?>" class="foto-preview">
                <div>
                    <label class="form-label mb-1">Cambiar fotografía</label>
                    <input type="file" name="foto" accept="image/*" class="form-control input-foto" data-preview="#previewFotoDueno">
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Nombre completo *</label>
                <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($fila['nombre']); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Teléfono *</label>
                <input type="text" name="telefono" class="form-control" value="<?php echo htmlspecialchars($fila['telefono']); ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Correo electrónico</label>
                <input type="email" name="correo" class="form-control" value="<?php echo htmlspecialchars($fila['correo'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Colonia</label>
                <input type="text" name="colonia" id="colonia" class="form-control" value="<?php echo htmlspecialchars($fila['colonia'] ?? ''); ?>">
            </div>

            <div class="col-md-7">
                <label class="form-label">Dirección *</label>
                <input type="text" name="direccion" class="form-control" value="<?php echo htmlspecialchars($fila['direccion']); ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Municipio</label>
                <input type="text" name="municipio" id="municipio" class="form-control" value="<?php echo htmlspecialchars($fila['municipio'] ?? 'Ozumba'); ?>">
            </div>
            <div class="col-md-2">
                <?php
                $localidades = [
                    ["56800", "Ozumba de Alzate (Cabecera municipal)", "Ozumba"],
                    ["56800", "San Vicente Chimalhuacán", "Ozumba"],
                    ["56800", "San Mateo Tecalco", "Ozumba"],
                    ["56800", "Santiago Mamalhuazuca", "Ozumba"],
                    ["56800", "San José Tlacotitlán", "Ozumba"],
                    ["56880", "Tepetlixpa", "Tepetlixpa"],
                    ["56890", "Nepantla de Sor Juana Inés de la Cruz", "Tepetlixpa"],
                    ["56970", "Atlautla de Victoria", "Atlautla"],
                    ["56983", "Popo Park", "Atlautla"],
                    ["56900", "Amecameca de Juárez", "Amecameca"],
                    ["56860", "Juchitepec", "Juchitepec"],
                ];
                $colonia_actual = $fila['colonia'] ?? '';
                $coincide = false;
                foreach ($localidades as $loc) {
                    if ($loc[1] === $colonia_actual) { $coincide = true; break; }
                }
                ?>
                <label class="form-label">C.P. / Localidad</label>
                <select name="codigo_postal" id="codigo_postal" class="form-select">
                    <?php if (!$coincide): ?>
                        <option value="<?php echo htmlspecialchars($fila['codigo_postal'] ?? ''); ?>" selected>Actual — <?php echo htmlspecialchars($fila['codigo_postal'] ?? 'sin dato'); ?></option>
                    <?php endif; ?>
                    <optgroup label="Ozumba de Alzate">
                        <?php foreach (array_slice($localidades, 0, 5) as $loc): ?>
                        <option value="<?php echo $loc[0]; ?>" data-colonia="<?php echo htmlspecialchars($loc[1]); ?>" data-municipio="<?php echo $loc[2]; ?>" <?php echo ($loc[1] === $colonia_actual) ? "selected" : ""; ?>><?php echo htmlspecialchars($loc[1]); ?> — <?php echo $loc[0]; ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                    <optgroup label="Municipios de alrededores">
                        <?php foreach (array_slice($localidades, 5) as $loc): ?>
                        <option value="<?php echo $loc[0]; ?>" data-colonia="<?php echo htmlspecialchars($loc[1]); ?>" data-municipio="<?php echo $loc[2]; ?>" <?php echo ($loc[1] === $colonia_actual) ? "selected" : ""; ?>><?php echo htmlspecialchars($loc[1]); ?> (<?php echo $loc[2]; ?>) — <?php echo $loc[0]; ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                </select>
            </div>

            <div class="col-12 d-flex gap-2 mt-3">
                <button type="submit" name="actualizar" class="btn btn-pc btn-pc-verde"><i class="bi bi-save me-1"></i>Guardar cambios</button>
                <a href="lista_duenos.php" class="btn btn-pc btn-pc-outline">Cancelar</a>
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
