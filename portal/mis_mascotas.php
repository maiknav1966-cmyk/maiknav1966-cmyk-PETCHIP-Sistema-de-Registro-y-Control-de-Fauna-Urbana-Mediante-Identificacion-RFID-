<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion_dueno("../");

$id_dueno = (int) $_SESSION["dueno_id"];
$dueno = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM duenos WHERE id_dueno=$id_dueno"));
$mascotas = mysqli_query($conexion, "SELECT * FROM perros WHERE id_dueno=$id_dueno ORDER BY nombre");

$no_leidas = 0;
$r = mysqli_query($conexion, "SELECT COUNT(*) AS n FROM notificaciones WHERE id_dueno=$id_dueno AND leida=0");
if ($r && ($f = mysqli_fetch_assoc($r))) $no_leidas = (int) $f["n"];

$raiz = "../";
$pagina_activa = "mascotas";
$titulo_pagina = "Mis mascotas";
include("../includes/header_dueno.php");
?>

<div class="pc-card mb-4 animar" style="background: linear-gradient(135deg, var(--pc-navy-2), var(--pc-navy)); color:#fff; border:none;">
    <div class="pc-card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h4 class="mb-1 text-white">Hola, <?php echo htmlspecialchars($dueno['nombre']); ?> 👋</h4>
            <p class="mb-0" style="opacity:.85;">Aquí puedes ver a tus mascotas, su código QR y su historial de ubicaciones.</p>
        </div>
        <?php if ($no_leidas > 0): ?>
        <a href="notificaciones.php" class="btn btn-pc btn-pc-outline text-white border-white">
            <i class="bi bi-bell-fill me-1"></i><?php echo $no_leidas; ?> notificación<?php echo $no_leidas>1?'es':''; ?> nueva<?php echo $no_leidas>1?'s':''; ?>
        </a>
        <?php endif; ?>
    </div>
</div>

<h5 class="mb-3 animar"><i class="bi bi-heart-fill me-2 text-success"></i>Mis mascotas (<?php echo mysqli_num_rows($mascotas); ?>)</h5>

<?php if (mysqli_num_rows($mascotas) === 0): ?>
<div class="pc-card animar"><div class="pc-card-body text-center py-5 text-muted">Aún no tienes mascotas registradas en el sistema. Acude al módulo municipal para darla de alta.</div></div>
<?php else: ?>
<div class="row g-3">
<?php while ($m = mysqli_fetch_assoc($mascotas)): ?>
    <div class="col-md-6 col-lg-4">
        <a href="mascota.php?id=<?php echo $m['id_perro']; ?>" class="text-decoration-none text-reset">
        <div class="pc-card h-100 animar">
            <div class="pc-card-body text-center">
                <img src="<?php echo $m['foto'] ? '../uploads/perros/'.htmlspecialchars($m['foto']) : '../img/logo.png'; ?>" class="rounded-4 mb-3" style="width:110px;height:110px;object-fit:cover;">
                <h5 class="mb-1"><?php echo htmlspecialchars($m['nombre']); ?></h5>
                <p class="text-muted mb-2 small"><?php echo htmlspecialchars($m['especie'] ?: 'Perro'); ?> · <?php echo htmlspecialchars($m['raza'] ?: 'Sin raza'); ?></p>
                <?php echo badge_estado($m['estado'] ?? 'Activo'); ?>
            </div>
        </div>
        </a>
    </div>
<?php endwhile; ?>
</div>
<?php endif; ?>

<?php include("../includes/footer_dueno.php"); ?>
