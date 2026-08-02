<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("gestionar_tags", "../");

$sql = "SELECT id_perro, nombre FROM perros WHERE id_perro NOT IN (SELECT id_animal FROM tags_rfid) ORDER BY nombre";
$resultado = mysqli_query($conexion, $sql);

$raiz = "../";
$pagina_activa = "tags";
$titulo_pagina = "Registrar chip de identificación";
include("../includes/header.php");
?>

<div class="d-flex justify-content-between align-items-center mb-4 animar">
    <div>
        <h4 class="mb-1"><i class="bi bi-tag-fill me-2 text-success"></i>Registro de chip de identificación</h4>
        <p class="text-muted mb-0">Vincula un identificador RFID a un animal registrado</p>
    </div>
    <a href="lista_tags.php" class="btn btn-pc btn-pc-outline"><i class="bi bi-list-ul me-1"></i>Ver lista</a>
</div>

<div class="alert alert-info pc-alert animar mb-3">
    <i class="bi bi-info-circle me-1"></i> El sistema aún trabaja con Tags simulados (prefijo <code>RFID-TEMP-XXX</code>). Migrar a un Tag físico real solo requiere actualizar este código.
</div>

<div class="row justify-content-center">
<div class="col-lg-7">
<div class="pc-card animar">
<div class="pc-card-body">

<?php if (mysqli_num_rows($resultado) === 0): ?>
<div class="alert alert-warning pc-alert">Todos los animales ya tienen un Tag asignado, o no hay animales registrados.</div>
<?php endif; ?>

<form action="guardar_tag.php" method="POST" class="pc-form necesita-validacion" novalidate>

    <div class="mb-3">
        <label class="form-label">Código del Tag *</label>
        <input type="text" name="codigo_tag" class="form-control" placeholder="RFID-TEMP-001" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Fecha de asignación *</label>
        <input type="date" name="fecha_asignacion" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Animal *</label>
        <select name="id_perro" class="form-select" required>
            <option value="">Seleccione un animal</option>
            <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                <option value="<?php echo $fila['id_perro']; ?>"><?php echo htmlspecialchars($fila['nombre']); ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-pc btn-pc-verde"><i class="bi bi-save me-1"></i>Guardar Tag</button>
        <a href="../menu.php" class="btn btn-pc btn-pc-outline">Cancelar</a>
    </div>
</form>

</div>
</div>
</div>
</div>

<?php include("../includes/footer.php"); ?>
