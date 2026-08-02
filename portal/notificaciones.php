<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion_dueno("../");

$id_dueno = (int) $_SESSION["dueno_id"];

if (isset($_POST["marcar_leidas"])) {
    mysqli_query($conexion, "UPDATE notificaciones SET leida=1 WHERE id_dueno=$id_dueno");
    header("Location: notificaciones.php"); exit();
}

$notifs = mysqli_query($conexion, "SELECT * FROM notificaciones WHERE id_dueno=$id_dueno ORDER BY fecha_creacion DESC LIMIT 50");
$no_leidas = 0;
$notifs_arr = [];
while ($n = mysqli_fetch_assoc($notifs)) {
    $notifs_arr[] = $n;
    if ((int) $n["leida"] === 0) $no_leidas++;
}

$raiz = "../";
$pagina_activa = "notificaciones";
$titulo_pagina = "Notificaciones";
include("../includes/header_dueno.php");
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 animar gap-2">
    <div>
        <h4 class="mb-1"><i class="bi bi-bell-fill me-2 text-success"></i>Notificaciones</h4>
        <p class="text-muted mb-0">Avisos cuando alguien escanea el QR de tus mascotas o reporta haberlas encontrado</p>
    </div>
    <?php if ($no_leidas > 0): ?>
    <form method="POST"><button type="submit" name="marcar_leidas" class="btn btn-pc btn-pc-outline"><i class="bi bi-check2-all me-1"></i>Marcar todas como leídas</button></form>
    <?php endif; ?>
</div>

<div class="pc-card animar">
    <div class="pc-card-body">
        <?php if (empty($notifs_arr)): ?>
            <p class="text-muted mb-0 text-center py-4">Sin notificaciones todavía.</p>
        <?php else: ?>
            <div class="timeline">
            <?php foreach ($notifs_arr as $n): ?>
                <div class="timeline-item">
                    <div><?php echo (int) $n['leida'] === 0 ? '<span class="badge bg-primary-subtle text-primary rounded-pill me-1">Nuevo</span>' : ''; ?><?php echo htmlspecialchars($n['mensaje']); ?></div>
                    <div class="fecha"><?php echo tiempo_relativo($n['fecha_creacion']); ?></div>
                    <?php if ($n['id_perro']): ?>
                    <a href="mascota.php?id=<?php echo (int) $n['id_perro']; ?>" class="small">Ver mascota <i class="bi bi-arrow-right-short"></i></a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include("../includes/footer_dueno.php"); ?>
