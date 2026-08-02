<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion_dueno("../");

$id_dueno = (int) $_SESSION["dueno_id"];
$mensaje = ""; $tipo_mensaje = "success";

if (isset($_GET["enviado"])) {
    $mensaje = "Tu reporte se publicó correctamente.";
}

// Marcar un reporte propio como resuelto (mascota encontrada)
if (isset($_POST["marcar_encontrada"])) {
    $id_reporte = (int) $_POST["id_reporte"];

    // Solo puede resolver reportes de mascotas que le pertenecen
    $reporte = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT r.* FROM reportes_extravio r
        INNER JOIN perros p ON r.id_perro = p.id_perro
        WHERE r.id_reporte=$id_reporte AND p.id_dueno=$id_dueno"));

    if ($reporte) {
        mysqli_query($conexion, "UPDATE reportes_extravio SET estado='Resuelto' WHERE id_reporte=$id_reporte");
        if ($reporte["id_perro"]) {
            mysqli_query($conexion, "UPDATE perros SET estado='Activo' WHERE id_perro=" . (int) $reporte["id_perro"]);
        }
        registrar_bitacora($conexion, "El dueño marcó como encontrada a \"{$reporte['nombre_animal']}\"", "Portal dueños");
        $mensaje = "¡Qué buena noticia! Tu mascota fue marcada como encontrada.";
    } else {
        $tipo_mensaje = "danger";
        $mensaje = "No se pudo actualizar ese reporte.";
    }
}

$reportes = mysqli_query($conexion, "SELECT r.* FROM reportes_extravio r
    INNER JOIN perros p ON r.id_perro = p.id_perro
    WHERE p.id_dueno=$id_dueno
    ORDER BY r.fecha_registro DESC");

$raiz = "../";
$pagina_activa = "estado_reporte";
$titulo_pagina = "Estado del reporte";
include("../includes/header_dueno.php");
?>

<div class="mb-4 animar d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <h4 class="mb-1"><i class="bi bi-clipboard2-check-fill me-2 text-success"></i>Estado del reporte</h4>
        <p class="text-muted mb-0">Consulta el avance de los reportes de pérdida de tus mascotas</p>
    </div>
    <a href="reportar_perdida.php" class="btn btn-pc btn-pc-rojo"><i class="bi bi-exclamation-octagon-fill me-1"></i>Reportar mascota perdida</a>
</div>

<?php if ($mensaje): ?>
<div class="alert alert-<?php echo $tipo_mensaje; ?> pc-alert alert-auto animar"><?php echo htmlspecialchars($mensaje); ?></div>
<?php endif; ?>

<?php if (mysqli_num_rows($reportes) === 0): ?>
<div class="pc-card animar"><div class="pc-card-body text-center py-5 text-muted">Aún no has publicado ningún reporte de pérdida.</div></div>
<?php else: ?>
<div class="row g-3">
<?php while ($r = mysqli_fetch_assoc($reportes)): ?>
    <div class="col-md-6">
        <div class="pc-card h-100 animar">
            <div class="pc-card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="mb-0"><?php echo htmlspecialchars($r["nombre_animal"] ?: "Mascota"); ?></h6>
                    <?php echo badge_estado($r["estado"]); ?>
                </div>
                <p class="small text-muted mb-1"><i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($r["lugar"]); ?></p>
                <p class="small text-muted mb-2"><i class="bi bi-calendar3 me-1"></i><?php echo date("d/m/Y", strtotime($r["fecha"])); ?></p>
                <p class="small mb-3"><?php echo htmlspecialchars($r["descripcion"]); ?></p>
                <?php if ($r["estado"] === "Activo"): ?>
                <form method="POST" onsubmit="return confirm('¿Confirmas que tu mascota ya fue encontrada?');">
                    <input type="hidden" name="id_reporte" value="<?php echo $r['id_reporte']; ?>">
                    <button type="submit" name="marcar_encontrada" class="btn btn-pc btn-pc-verde btn-sm w-100">
                        <i class="bi bi-check-circle-fill me-1"></i>Marcar como encontrada
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endwhile; ?>
</div>
<?php endif; ?>

<?php include("../includes/footer_dueno.php"); ?>
