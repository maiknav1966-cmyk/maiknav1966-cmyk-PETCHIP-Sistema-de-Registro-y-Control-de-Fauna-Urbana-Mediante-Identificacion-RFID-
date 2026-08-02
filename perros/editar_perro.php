<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("editar_animales", "../");

$id = (int) $_GET["id"];

$fila = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM perros WHERE id_perro=$id"));
if (!$fila) { header("Location: lista_perros.php"); exit(); }

$duenos = mysqli_query($conexion, "SELECT id_dueno, nombre FROM duenos ORDER BY nombre");

$raiz = "../";
$pagina_activa = "perros";
$titulo_pagina = "Editar mascota";
include("../includes/header.php");
?>

<div class="d-flex justify-content-between align-items-center mb-4 animar">
    <div>
        <h4 class="mb-1"><i class="bi bi-pencil-square me-2 text-success"></i>Editar mascota</h4>
        <p class="text-muted mb-0">Actualiza los datos de <?php echo htmlspecialchars($fila['nombre']); ?></p>
    </div>
    <a href="../perfil/perfil_perro.php?id=<?php echo $id; ?>" class="btn btn-pc btn-pc-outline"><i class="bi bi-eye me-1"></i>Ver perfil</a>
</div>

<form action="actualizar_perro.php" method="POST" enctype="multipart/form-data" class="pc-form necesita-validacion animar" novalidate>
<input type="hidden" name="id_perro" value="<?php echo $id; ?>">

<div class="row g-3">
    <div class="col-lg-8">
        <div class="pc-card mb-3">
            <div class="pc-card-header"><i class="bi bi-info-circle me-2"></i>Datos generales</div>
            <div class="pc-card-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre de la mascota *</label>
                    <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($fila['nombre']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Especie *</label>
                    <select name="especie" class="form-select" required>
                        <?php foreach (["Perro","Gato","Otro"] as $e): ?>
                        <option value="<?php echo $e; ?>" <?php echo $fila['especie']==$e?'selected':''; ?>><?php echo $e; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Raza</label>
                    <select name="raza" id="selectRaza" class="form-select pc-select-otra" data-otro-target="raza_otra_wrap"></select>
                </div>
                <div class="col-md-4 d-none" id="raza_otra_wrap">
                    <label class="form-label">¿Cuál raza?</label>
                    <input type="text" name="raza_otra" class="form-control" value="<?php echo htmlspecialchars($fila['raza'] ?? ''); ?>" placeholder="Escribe la raza">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sexo</label>
                    <select name="sexo" class="form-select">
                        <?php foreach (["Macho","Hembra"] as $s): ?>
                        <option value="<?php echo $s; ?>" <?php echo ($fila['sexo']??'')==$s?'selected':''; ?>><?php echo $s; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Edad (años)</label>
                    <input type="number" min="0" name="edad" class="form-control" value="<?php echo htmlspecialchars($fila['edad'] ?? ''); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha de nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control" value="<?php echo htmlspecialchars($fila['fecha_nacimiento'] ?? ''); ?>">
                </div>
                <?php
                    $colores_lista = colores_comunes();
                    $color_actual = $fila['color'] ?? '';
                    $color_es_otro = $color_actual !== '' && !in_array($color_actual, $colores_lista, true);
                ?>
                <div class="col-md-4">
                    <label class="form-label">Color</label>
                    <select name="color" class="form-select pc-select-otra" data-otro-target="color_otra_wrap">
                        <option value="">Selecciona un color</option>
                        <?php foreach ($colores_lista as $c): ?>
                        <option value="<?php echo htmlspecialchars($c); ?>" <?php echo ($color_actual === $c) ? 'selected' : ''; ?>><?php echo htmlspecialchars($c); ?></option>
                        <?php endforeach; ?>
                        <option value="Otro" <?php echo $color_es_otro ? 'selected' : ''; ?>>Otro (especificar)</option>
                    </select>
                </div>
                <div class="col-md-4 <?php echo $color_es_otro ? '' : 'd-none'; ?>" id="color_otra_wrap">
                    <label class="form-label">¿Cuál color?</label>
                    <input type="text" name="color_otra" class="form-control" value="<?php echo $color_es_otro ? htmlspecialchars($color_actual) : ''; ?>" placeholder="Escribe el color">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Peso (kg)</label>
                    <input type="number" step="0.1" min="0" name="peso" class="form-control" value="<?php echo htmlspecialchars($fila['peso'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tamaño</label>
                    <select name="tamano" class="form-select">
                        <?php foreach (["Pequeño","Mediano","Grande"] as $t): ?>
                        <option value="<?php echo $t; ?>" <?php echo ($fila['tamano']??'')==$t?'selected':''; ?>><?php echo $t; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Colonia donde habita</label>
                    <input type="text" name="colonia" class="form-control" value="<?php echo htmlspecialchars($fila['colonia'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Dueño *</label>
                    <select name="id_dueno" class="form-select" required>
                        <?php while ($d = mysqli_fetch_assoc($duenos)): ?>
                        <option value="<?php echo $d['id_dueno']; ?>" <?php echo $fila['id_dueno']==$d['id_dueno']?'selected':''; ?>><?php echo htmlspecialchars($d['nombre']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="2"><?php echo htmlspecialchars($fila['observaciones'] ?? ''); ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="pc-card mb-3">
            <div class="pc-card-header"><i class="bi bi-camera-fill me-2"></i>Fotografía</div>
            <div class="pc-card-body text-center">
                <img id="previewFotoAnimal" src="<?php echo $fila['foto'] ? '../uploads/perros/'.htmlspecialchars($fila['foto']) : '../img/logo.png'; ?>" class="foto-preview mb-3">
                <input type="file" name="foto" accept="image/*" class="form-control input-foto" data-preview="#previewFotoAnimal">
            </div>
        </div>
        <div class="pc-card">
            <div class="pc-card-header"><i class="bi bi-clipboard2-pulse-fill me-2"></i>Salud y estatus</div>
            <div class="pc-card-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="esterilizado" value="1" id="swEster" <?php echo $fila['esterilizado']?'checked':''; ?>>
                    <label class="form-check-label" for="swEster">Mascota esterilizada</label>
                </div>
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <?php foreach (["Activo","Perdido","Adoptado","Fallecido"] as $e): ?>
                    <option value="<?php echo $e; ?>" <?php echo ($fila['estado']??'Activo')==$e?'selected':''; ?>><?php echo $e; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-pc btn-pc-verde"><i class="bi bi-save me-1"></i>Guardar cambios</button>
    <a href="lista_perros.php" class="btn btn-pc btn-pc-outline">Cancelar</a>
</div>

</form>

<script>
var razasPorEspecie = <?php echo json_encode(razas_por_especie(), JSON_UNESCAPED_UNICODE); ?>;
var razaActual = <?php echo json_encode($fila['raza'] ?? '', JSON_UNESCAPED_UNICODE); ?>;

function poblarRazas(especie, seleccionActual) {
    var select = document.getElementById('selectRaza');
    if (!select) return;
    var lista = razasPorEspecie[especie] || razasPorEspecie['Otro'];
    var html = '<option value="">Selecciona una raza</option>';
    lista.forEach(function (r) {
        html += '<option value="' + r + '"' + (r === seleccionActual ? ' selected' : '') + '>' + r + '</option>';
    });
    var esOtra = seleccionActual && lista.indexOf(seleccionActual) === -1;
    html += '<option value="Otra"' + (esOtra ? ' selected' : '') + '>Otra (especificar)</option>';
    select.innerHTML = html;
    select.dispatchEvent(new Event('change'));
}

var selectEspecie = document.querySelector('select[name="especie"]');
if (selectEspecie) {
    poblarRazas(selectEspecie.value, razaActual);
    selectEspecie.addEventListener('change', function () { poblarRazas(this.value, ''); });
}

document.querySelectorAll('.pc-select-otra').forEach(function (sel) {
    var destino = document.getElementById(sel.dataset.otroTarget);
    if (!destino) return;
    var actualizar = function () {
        destino.classList.toggle('d-none', sel.value !== 'Otra' && sel.value !== 'Otro');
    };
    sel.addEventListener('change', actualizar);
    actualizar();
});
</script>

<?php include("../includes/footer.php"); ?>
