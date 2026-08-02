<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("gestionar_tags", "../");

$id = (int) $_GET["id"];
$tag = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM tags_rfid WHERE id_tag=$id"));
if (!$tag) { header("Location: lista_tags.php"); exit(); }

$perros = mysqli_query($conexion, "SELECT id_perro, nombre FROM perros ORDER BY nombre");

$raiz = "../";
$pagina_activa = "tags";
$titulo_pagina = "Editar chip de identificación";
include("../includes/header.php");
?>

<div class="mb-4 animar">
    <h4 class="mb-1"><i class="bi bi-pencil-square me-2 text-success"></i>Editar chip de identificación</h4>
    <p class="text-muted mb-0">Código: <?php echo htmlspecialchars($tag['codigo_tag']); ?></p>
</div>

<div class="row justify-content-center">
<div class="col-lg-7">
<div class="pc-card animar">
<div class="pc-card-body">
<form action="actualizar_tag.php" method="POST" class="pc-form necesita-validacion" novalidate>
<input type="hidden" name="id_tag" value="<?php echo $id; ?>">

    <div class="mb-3">
        <label class="form-label">Código del Tag *</label>
        <input type="text" name="codigo_tag" class="form-control" value="<?php echo htmlspecialchars($tag['codigo_tag']); ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Fecha de asignación *</label>
        <input type="date" name="fecha_asignacion" class="form-control" value="<?php echo $tag['fecha_asignacion']; ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Estado</label>
        <select name="estado" class="form-select">
            <?php foreach (["Activo","Inactivo","Perdido"] as $e): ?>
            <option value="<?php echo $e; ?>" <?php echo ($tag['estado']??'Activo')==$e?'selected':''; ?>><?php echo $e; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Animal *</label>
        <select name="id_perro" class="form-select" required>
            <?php while ($p = mysqli_fetch_assoc($perros)): ?>
            <option value="<?php echo $p['id_perro']; ?>" <?php echo $tag['id_animal']==$p['id_perro']?'selected':''; ?>><?php echo htmlspecialchars($p['nombre']); ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-pc btn-pc-verde"><i class="bi bi-save me-1"></i>Guardar cambios</button>
        <a href="lista_tags.php" class="btn btn-pc btn-pc-outline">Cancelar</a>
    </div>
</form>
</div>
</div>
</div>
</div>

<?php include("../includes/footer.php"); ?>
