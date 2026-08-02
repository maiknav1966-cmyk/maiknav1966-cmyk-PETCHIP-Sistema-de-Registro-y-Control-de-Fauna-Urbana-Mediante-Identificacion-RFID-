<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion_dueno("../");

$id_dueno = (int) $_SESSION["dueno_id"];
$dueno = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM duenos WHERE id_dueno=$id_dueno"));
$mensaje = ""; $tipo_mensaje = "success";

if (isset($_POST["reportar"])) {
    $id_perro = (int) ($_POST["id_perro"] ?? 0);

    // Verificamos que la mascota pertenezca a este dueño (nunca confiar en el POST)
    $animal = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM perros WHERE id_perro=$id_perro AND id_dueno=$id_dueno"));

    if (!$animal) {
        $tipo_mensaje = "danger";
        $mensaje = "Esa mascota no pertenece a tu cuenta.";
    } else {
        // Si ya existe un reporte activo de "Perdido" para esta mascota, no se duplica.
        $ya_activo = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT id_reporte FROM reportes_extravio
            WHERE id_perro=$id_perro AND tipo='Perdido' AND estado='Activo'"));

        if ($ya_activo) {
            $tipo_mensaje = "danger";
            $mensaje = "Ya existe un reporte activo de pérdida para esta mascota.";
        } else {
            $descripcion = limpiar_dato($conexion, $_POST["descripcion"]);
            $lugar = limpiar_dato($conexion, $_POST["lugar"]);
            $fecha = limpiar_dato($conexion, $_POST["fecha"]);
            $contacto = limpiar_dato($conexion, $_POST["contacto"]);
            $nombre_animal = mysqli_real_escape_string($conexion, $animal["nombre"]);
            $especie = mysqli_real_escape_string($conexion, $animal["especie"]);
            $foto_sql = $animal["foto"] ? "'" . mysqli_real_escape_string($conexion, $animal["foto"]) . "'" : "NULL";

            mysqli_query($conexion, "INSERT INTO reportes_extravio(tipo, id_perro, nombre_animal, especie, descripcion, lugar, fecha, contacto, foto, estado)
                VALUES('Perdido', $id_perro, '$nombre_animal', '$especie', '$descripcion', '$lugar', '$fecha', '$contacto', $foto_sql, 'Activo')");

            mysqli_query($conexion, "UPDATE perros SET estado='Perdido' WHERE id_perro=$id_perro");

            registrar_bitacora($conexion, "El dueño \"{$dueno['nombre']}\" reportó como perdida a \"{$animal['nombre']}\"", "Portal dueños");
            header("Location: estado_reporte.php?enviado=1"); exit();
        }
    }
}

$mascotas = mysqli_query($conexion, "SELECT * FROM perros WHERE id_dueno=$id_dueno ORDER BY nombre");

$raiz = "../";
$pagina_activa = "reportar";
$titulo_pagina = "Reportar mascota perdida";
include("../includes/header_dueno.php");
?>

<div class="mb-4 animar">
    <h4 class="mb-1"><i class="bi bi-exclamation-octagon-fill me-2 text-danger"></i>Reportar mascota perdida</h4>
    <p class="text-muted mb-0">Tu reporte se publicará en la sección de "Perdidos y encontrados" para que la comunidad pueda ayudarte.</p>
</div>

<?php if ($mensaje): ?>
<div class="alert alert-<?php echo $tipo_mensaje; ?> pc-alert alert-auto animar"><?php echo htmlspecialchars($mensaje); ?></div>
<?php endif; ?>

<?php if (mysqli_num_rows($mascotas) === 0): ?>
<div class="pc-card animar"><div class="pc-card-body text-center py-5 text-muted">Aún no tienes mascotas registradas en el sistema.</div></div>
<?php else: ?>
<div class="row justify-content-center">
<div class="col-lg-7"><div class="pc-card animar"><div class="pc-card-body">
<form method="POST" class="pc-form necesita-validacion row g-3" novalidate>
    <div class="col-12">
        <label class="form-label">¿Cuál de tus mascotas se perdió? *</label>
        <select name="id_perro" class="form-select" required>
            <option value="">Selecciona una mascota</option>
            <?php mysqli_data_seek($mascotas, 0); while ($m = mysqli_fetch_assoc($mascotas)): ?>
            <option value="<?php echo $m['id_perro']; ?>"><?php echo htmlspecialchars($m['nombre']); ?> (<?php echo htmlspecialchars($m['especie'] ?: 'Perro'); ?>)</option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Fecha en que se perdió *</label>
        <input type="date" name="fecha" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Contacto para que te avisen *</label>
        <input type="text" name="contacto" class="form-control" placeholder="Teléfono o correo"
               value="<?php echo htmlspecialchars($dueno['telefono'] ?: $dueno['correo'] ?: ''); ?>" required>
        <div class="form-text">Este dato se mostrará públicamente en el reporte para que puedan contactarte.</div>
    </div>
    <div class="col-12">
        <label class="form-label">Última zona donde se le vio *</label>
        <input type="text" name="lugar" class="form-control" required>
    </div>
    <div class="col-12">
        <label class="form-label">Descripción / señas particulares *</label>
        <textarea name="descripcion" class="form-control" rows="3" required placeholder="Collar, color, comportamiento, etc."></textarea>
    </div>
    <div class="col-12 d-flex gap-2 mt-2">
        <button type="submit" name="reportar" class="btn btn-pc btn-pc-rojo"><i class="bi bi-megaphone-fill me-1"></i>Publicar reporte</button>
        <a href="mis_mascotas.php" class="btn btn-pc btn-pc-outline">Cancelar</a>
    </div>
</form>
</div></div></div>
</div>
<?php endif; ?>

<?php include("../includes/footer_dueno.php"); ?>
