<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("consultar_rfid", "../");

$raiz = "../";
$pagina_activa = "consulta";
$titulo_pagina = "Encontrar mascota";
include("../includes/header.php");
?>

<div class="mb-4 animar">
    <h4 class="mb-1"><i class="bi bi-broadcast me-2 text-success"></i>Encontrar mascota por chip</h4>
    <p class="text-muted mb-0">Ingresa o escanea el código del Tag para ver toda la información del animal y su dueño</p>
</div>

<div class="row justify-content-center">
<div class="col-lg-7">
<div class="pc-card animar">
<div class="pc-card-body text-center py-5">
    <i class="bi bi-tag" style="font-size:3.4rem; color:var(--pc-primario);"></i>
    <h5 class="mt-3 mb-3">Escanear / capturar código de Tag</h5>
    <form action="resultado_tag.php" method="GET" class="d-flex gap-2 justify-content-center">
        <input type="text" name="codigo_tag" class="form-control" style="max-width:320px" placeholder="Ej. RFID-TEMP-001" autofocus required>
        <button class="btn btn-pc btn-pc-verde"><i class="bi bi-search me-1"></i>Consultar</button>
    </form>
    <p class="text-muted small mt-3 mb-0">El lector RFID físico llenará este campo automáticamente al acercar el Tag.</p>
</div>
</div>

<div class="pc-card mt-3 animar">
<div class="pc-card-header"><i class="bi bi-clock-history me-2"></i>Tags disponibles</div>
<div class="pc-card-body">
<?php
$tags = mysqli_query($conexion, "SELECT codigo_tag FROM tags_rfid ORDER BY id_tag DESC LIMIT 8");
if (mysqli_num_rows($tags) === 0): ?>
    <p class="text-muted mb-0">Aún no hay Tags registrados.</p>
<?php else: while ($t = mysqli_fetch_assoc($tags)): ?>
    <a href="resultado_tag.php?codigo_tag=<?php echo urlencode($t['codigo_tag']); ?>" class="badge rounded-pill bg-success-subtle text-success px-3 py-2 text-decoration-none me-2 mb-2 d-inline-block"><?php echo htmlspecialchars($t['codigo_tag']); ?></a>
<?php endwhile; endif; ?>
</div>
</div>
</div>
</div>

<?php include("../includes/footer.php"); ?>
