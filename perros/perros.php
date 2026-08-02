<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("crear_animales", "../");

$duenos = mysqli_query($conexion, "SELECT id_dueno, nombre FROM duenos ORDER BY nombre");

$raiz = "../";
$pagina_activa = "perros";
$titulo_pagina = "Registrar mascota";
include("../includes/header.php");
?>

<div class="d-flex justify-content-between align-items-center mb-4 animar">
    <div>
        <h4 class="mb-1"><i class="bi bi-heart-fill me-2 text-success"></i>Registrar mascota</h4>
        <p class="text-muted mb-0">Captura la información completa de la mascota</p>
    </div>
    <a href="lista_perros.php" class="btn btn-pc btn-pc-outline"><i class="bi bi-list-ul me-1"></i>Ver lista</a>
</div>

<?php if (mysqli_num_rows($duenos) === 0): ?>
<div class="alert alert-warning pc-alert animar">
    <?php if (tiene_permiso('crear_duenos')): ?>
        Aún no hay dueños registrados. <a href="../duenos/duenos.php">Registra un dueño primero</a>.
    <?php else: ?>
        Aún no hay dueños registrados. Solicita a un Encargado o al Administrador que registre uno antes de continuar.
    <?php endif; ?>
</div>
<?php endif; ?>

<form action="guardar_perro.php" method="POST" enctype="multipart/form-data" class="pc-form necesita-validacion animar" novalidate>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="pc-card mb-3">
            <div class="pc-card-header"><i class="bi bi-info-circle me-2"></i>Datos generales</div>
            <div class="pc-card-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre del animal *</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Especie *</label>
                    <select name="especie" class="form-select" required>
                        <option value="Perro">Perro</option>
                        <option value="Gato">Gato</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Raza</label>
                    <select name="raza" id="selectRaza" class="form-select pc-select-otra" data-otro-target="raza_otra_wrap"></select>
                </div>
                <div class="col-md-4 d-none" id="raza_otra_wrap">
                    <label class="form-label">¿Cuál raza?</label>
                    <input type="text" name="raza_otra" class="form-control" placeholder="Escribe la raza">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sexo</label>
                    <select name="sexo" class="form-select">
                        <option value="Macho">Macho</option>
                        <option value="Hembra">Hembra</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Edad (años)</label>
                    <input type="number" name="edad" min="0" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fecha de nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Color</label>
                    <select name="color" class="form-select pc-select-otra" data-otro-target="color_otra_wrap">
                        <option value="">Selecciona un color</option>
                        <?php foreach (colores_comunes() as $c): ?>
                        <option value="<?php echo htmlspecialchars($c); ?>"><?php echo htmlspecialchars($c); ?></option>
                        <?php endforeach; ?>
                        <option value="Otro">Otro (especificar)</option>
                    </select>
                </div>
                <div class="col-md-4 d-none" id="color_otra_wrap">
                    <label class="form-label">¿Cuál color?</label>
                    <input type="text" name="color_otra" class="form-control" placeholder="Escribe el color">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Peso (kg)</label>
                    <input type="number" step="0.1" min="0" name="peso" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tamaño</label>
                    <select name="tamano" class="form-select">
                        <option value="Pequeño">Pequeño</option>
                        <option value="Mediano">Mediano</option>
                        <option value="Grande">Grande</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Colonia donde habita</label>
                    <input type="text" name="colonia" class="form-control" placeholder="Ej. Centro, San José...">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Dueño *</label>
                    <select name="id_dueno" class="form-select" required>
                        <option value="">Seleccione un dueño</option>
                        <?php while ($fila = mysqli_fetch_assoc($duenos)): ?>
                            <option value="<?php echo $fila['id_dueno']; ?>"><?php echo htmlspecialchars($fila['nombre']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="2" placeholder="Comportamiento, señas particulares, etc."></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="pc-card mb-3">
            <div class="pc-card-header"><i class="bi bi-camera-fill me-2"></i>Fotografía</div>
            <div class="pc-card-body text-center">
                <img id="previewFotoAnimal" src="../img/logo.png" class="foto-preview mb-3">
                <input type="file" name="foto" accept="image/*" class="form-control input-foto" data-preview="#previewFotoAnimal">
            </div>
        </div>
        <div class="pc-card">
            <div class="pc-card-header"><i class="bi bi-clipboard2-pulse-fill me-2"></i>Salud</div>
            <div class="pc-card-body">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" name="esterilizado" value="1" id="swEster">
                    <label class="form-check-label" for="swEster">Mascota esterilizada</label>
                </div>
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="Activo">Activo</option>
                    <option value="Perdido">Perdido</option>
                    <option value="Adoptado">Adoptado</option>
                </select>
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-pc btn-pc-verde"><i class="bi bi-save me-1"></i>Registrar mascota</button>
    <a href="../menu.php" class="btn btn-pc btn-pc-outline">Cancelar</a>
</div>

</form>

<script>
var razasPorEspecie = <?php echo json_encode(razas_por_especie(), JSON_UNESCAPED_UNICODE); ?>;

function poblarRazas(especie, seleccionActual) {
    var select = document.getElementById('selectRaza');
    if (!select) return;
    var lista = razasPorEspecie[especie] || razasPorEspecie['Otro'];
    var html = '<option value="">Selecciona una raza</option>';
    lista.forEach(function (r) {
        html += '<option value="' + r + '"' + (r === seleccionActual ? ' selected' : '') + '>' + r + '</option>';
    });
    var esOtra = seleccionActual && lista.indexOf(seleccionActual) === -1 && seleccionActual !== '';
    html += '<option value="Otra"' + (esOtra ? ' selected' : '') + '>Otra (especificar)</option>';
    select.innerHTML = html;
    select.dispatchEvent(new Event('change'));
}

var selectEspecie = document.querySelector('select[name="especie"]');
if (selectEspecie) {
    poblarRazas(selectEspecie.value, '<?php echo isset($_POST["raza"]) ? addslashes($_POST["raza"]) : ""; ?>');
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
