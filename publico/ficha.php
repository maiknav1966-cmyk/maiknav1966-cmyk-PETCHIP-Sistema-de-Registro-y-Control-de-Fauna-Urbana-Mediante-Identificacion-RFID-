<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("../config/conexion.php");
include("../includes/funciones.php");

$token = limpiar_dato($conexion, $_GET["t"] ?? "");
$animal = null;
$id_lectura = null;
$vacunas_arr = [];

if ($token !== "") {
    $animal = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM perros WHERE token_publico='$token'"));
}

if ($animal) {
    $id_animal = (int) $animal["id_perro"];

    // Registrar el escaneo (si el animal ya tiene un tag asignado)
    $tag = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM tags_rfid WHERE id_animal=$id_animal"));
    if ($tag) {
        mysqli_query($conexion, "INSERT INTO lecturas_rfid(id_tag, ubicacion, usuario, origen)
            VALUES(" . (int) $tag["id_tag"] . ", 'Escaneo público del QR', NULL, 'publico')");
        $id_lectura = mysqli_insert_id($conexion);
    }

    if ((int) $animal["compartir_info_medica"] === 1) {
        $res_vac = mysqli_query($conexion, "SELECT nombre_vacuna, fecha_aplicacion FROM vacunas
            WHERE id_animal=$id_animal ORDER BY fecha_aplicacion DESC LIMIT 5");
        while ($v = mysqli_fetch_assoc($res_vac)) $vacunas_arr[] = $v;
    }
}

$aviso_ok = isset($_GET["aviso"]);
$titulo_pagina = $animal ? "Ficha de " . $animal["nombre"] : "Mascota no encontrada";
include("../includes/header_publico.php");
?>

<?php if (!$animal): ?>
<div class="pc-card animar">
    <div class="pc-card-body text-center py-5">
        <i class="bi bi-qr-code-scan text-danger" style="font-size:2.6rem;"></i>
        <h5 class="mt-3">Código no válido</h5>
        <p class="text-muted mb-0">Este enlace de identificación no corresponde a ninguna mascota registrada en PetChip.</p>
    </div>
</div>
<?php else: ?>

<?php if ($aviso_ok): ?>
<div class="alert alert-success pc-alert animar"><i class="bi bi-check-circle-fill me-2"></i>¡Gracias! Se le avisó al dueño de <?php echo htmlspecialchars($animal["nombre"]); ?>.</div>
<?php endif; ?>

<div class="pc-card animar">
    <div class="pc-card-body text-center">
        <img src="<?php echo $animal["foto"] ? "../uploads/perros/" . htmlspecialchars($animal["foto"]) : "../img/logo.png"; ?>"
             class="rounded-4 mb-3" style="width:170px;height:170px;object-fit:cover;">
        <h3 class="mb-1"><?php echo htmlspecialchars($animal["nombre"]); ?></h3>
        <p class="text-muted mb-2"><?php echo htmlspecialchars($animal["especie"] ?: "Perro"); ?> · <?php echo htmlspecialchars($animal["raza"] ?: "Raza no especificada"); ?></p>
        <?php echo badge_estado($animal["estado"] ?? "Activo"); ?>

        <div class="row text-start mt-4 g-2 small">
            <div class="col-6"><strong>Sexo:</strong> <?php echo htmlspecialchars($animal["sexo"] ?: "—"); ?></div>
            <div class="col-6"><strong>Edad:</strong> <?php echo htmlspecialchars($animal["edad"] ?: "—"); ?> años</div>
            <div class="col-6"><strong>Color:</strong> <?php echo htmlspecialchars($animal["color"] ?: "—"); ?></div>
            <div class="col-6"><strong>Tamaño:</strong> <?php echo htmlspecialchars($animal["tamano"] ?: "—"); ?></div>
        </div>

        <?php if ((int) $animal["compartir_info_medica"] === 1): ?>
        <hr>
        <div class="text-start small">
            <p class="mb-2"><strong><i class="bi bi-shield-check me-1"></i>Esterilizado:</strong> <?php echo $animal["esterilizado"] ? "Sí" : "No"; ?></p>
            <?php if ($animal["observaciones"]): ?>
            <p class="mb-2"><strong>Notas médicas:</strong> <?php echo nl2br(htmlspecialchars($animal["observaciones"])); ?></p>
            <?php endif; ?>
            <?php if (!empty($vacunas_arr)): ?>
            <strong>Vacunas recientes:</strong>
            <ul class="mb-0">
                <?php foreach ($vacunas_arr as $v): ?>
                <li><?php echo htmlspecialchars($v["nombre_vacuna"]); ?> — <?php echo date("d/m/Y", strtotime($v["fecha_aplicacion"])); ?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <hr>
        <p class="small text-muted">Por privacidad, esta ficha no muestra el nombre, dirección ni contacto del propietario.</p>
        <button class="btn btn-pc btn-pc-primario w-100" data-bs-toggle="modal" data-bs-target="#modalAviso">
            <i class="bi bi-megaphone-fill me-1"></i>Avisar al dueño
        </button>
    </div>
</div>

<!-- MODAL: Avisar al dueño -->
<div class="modal fade" id="modalAviso" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" action="avisar.php" class="modal-content pc-form">
      <div class="modal-header">
        <h5 class="modal-title">Avisar al dueño de <?php echo htmlspecialchars($animal["nombre"]); ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="t" value="<?php echo htmlspecialchars($token); ?>">
        <input type="hidden" name="lat" id="avisoLat" value="">
        <input type="hidden" name="lng" id="avisoLng" value="">
        <div class="mb-2"><label class="form-label">Tu nombre (opcional)</label><input type="text" name="nombre" class="form-control"></div>
        <div class="mb-2"><label class="form-label">Tu teléfono (opcional)</label><input type="text" name="telefono" class="form-control"></div>
        <div class="mb-2"><label class="form-label">¿Dónde encontraste a la mascota? *</label><input type="text" name="lugar" class="form-control" required></div>
        <div class="mb-2"><label class="form-label">Comentarios</label><textarea name="comentarios" class="form-control" rows="2" placeholder="Estado del animal, referencias del lugar..."></textarea></div>
        <p class="small text-muted mb-0"><i class="bi bi-geo-alt me-1"></i>Si lo permites, tu navegador compartirá tu ubicación actual para ayudar a localizar a la mascota más rápido.</p>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-pc btn-pc-verde"><i class="bi bi-send me-1"></i>Enviar aviso</button>
      </div>
    </form>
  </div>
</div>

<script>
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function (pos) {
        var lat = pos.coords.latitude, lng = pos.coords.longitude;
        var campoLat = document.getElementById('avisoLat');
        var campoLng = document.getElementById('avisoLng');
        if (campoLat) campoLat.value = lat;
        if (campoLng) campoLng.value = lng;
        <?php if ($id_lectura): ?>
        fetch('registrar_ubicacion.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id_lectura=<?php echo (int) $id_lectura; ?>&t=<?php echo urlencode($token); ?>&lat=' + lat + '&lng=' + lng
        }).catch(function () {});
        <?php endif; ?>
    });
}
</script>

<?php endif; ?>
<?php include("../includes/footer_publico.php"); ?>
