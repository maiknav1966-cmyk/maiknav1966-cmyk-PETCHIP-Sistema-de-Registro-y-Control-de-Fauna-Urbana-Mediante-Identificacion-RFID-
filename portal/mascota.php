<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion_dueno("../");

$id_dueno = (int) $_SESSION["dueno_id"];
$id = (int) ($_GET["id"] ?? 0);

$animal = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM perros WHERE id_perro=$id AND id_dueno=$id_dueno"));
if (!$animal) { header("Location: mis_mascotas.php"); exit(); }

// El dueño puede activar/desactivar que la ficha pública muestre datos médicos
if (isset($_POST["toggle_medica"])) {
    mysqli_query($conexion, "UPDATE perros SET compartir_info_medica = 1 - compartir_info_medica WHERE id_perro=$id");
    header("Location: mascota.php?id=$id"); exit();
}

$tag = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM tags_rfid WHERE id_animal=$id"));
$vacunas = mysqli_query($conexion, "SELECT * FROM vacunas WHERE id_animal=$id ORDER BY fecha_aplicacion DESC");

$token_publico = obtener_token_publico($conexion, $id);
$url_publica = url_publica_animal($token_publico);

$lecturas_arr = [];
if ($tag) {
    $res_lec = mysqli_query($conexion, "SELECT * FROM lecturas_rfid WHERE id_tag=" . (int) $tag["id_tag"] . " ORDER BY fecha_hora DESC LIMIT 20");
    while ($row = mysqli_fetch_assoc($res_lec)) $lecturas_arr[] = $row;
}
$avisos_arr = [];
$res_avi = mysqli_query($conexion, "SELECT * FROM avisos_encontrado WHERE id_perro=$id ORDER BY fecha_registro DESC LIMIT 20");
while ($row = mysqli_fetch_assoc($res_avi)) $avisos_arr[] = $row;

$puntos_mapa = [];
foreach ($lecturas_arr as $l) {
    if ($l["lat"] && $l["lng"]) {
        $puntos_mapa[] = ["lat" => (float) $l["lat"], "lng" => (float) $l["lng"], "detalle" => "Escaneo · " . date("d/m/Y H:i", strtotime($l["fecha_hora"]))];
    }
}
foreach ($avisos_arr as $a) {
    if ($a["lat"] && $a["lng"]) {
        $puntos_mapa[] = ["lat" => (float) $a["lat"], "lng" => (float) $a["lng"], "detalle" => "Aviso · " . htmlspecialchars($a["lugar"] ?: "")];
    }
}

$raiz = "../";
$pagina_activa = "mascotas";
$titulo_pagina = $animal["nombre"];
include("../includes/header_dueno.php");
?>

<div class="mb-3 animar">
    <a href="mis_mascotas.php" class="text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Volver a mis mascotas</a>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="pc-card text-center animar pc-imprimible">
            <div class="pc-card-body">
                <img src="<?php echo $animal['foto'] ? '../uploads/perros/'.htmlspecialchars($animal['foto']) : '../img/logo.png'; ?>" class="rounded-4 mb-3" style="width:150px;height:150px;object-fit:cover;">
                <h4 class="mb-1"><?php echo htmlspecialchars($animal['nombre']); ?></h4>
                <p class="text-muted mb-2"><?php echo htmlspecialchars($animal['especie'] ?: 'Perro'); ?> · <?php echo htmlspecialchars($animal['raza'] ?: 'Sin raza'); ?></p>
                <?php echo badge_estado($animal['estado'] ?? 'Activo'); ?>
                <div class="row text-start mt-3 g-2 small">
                    <div class="col-6"><strong>Sexo:</strong> <?php echo htmlspecialchars($animal['sexo'] ?: '—'); ?></div>
                    <div class="col-6"><strong>Edad:</strong> <?php echo htmlspecialchars($animal['edad'] ?: '—'); ?> años</div>
                    <div class="col-6"><strong>Color:</strong> <?php echo htmlspecialchars($animal['color'] ?: '—'); ?></div>
                    <div class="col-6"><strong>Tamaño:</strong> <?php echo htmlspecialchars($animal['tamano'] ?: '—'); ?></div>
                </div>
            </div>
        </div>

        <div class="pc-card mt-3 animar no-imprimir">
            <div class="pc-card-header"><i class="bi bi-qr-code me-2"></i>Código QR de identificación</div>
            <div class="pc-card-body text-center">
                <div id="qrAnimal" class="d-inline-block mb-2"></div>
                <p class="small text-muted mb-3">Escanéalo para ver la ficha pública (sin tus datos personales)</p>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-pc btn-pc-outline btn-sm" id="btnDescargarQR"><i class="bi bi-download me-1"></i>Descargar PNG</button>
                    <button type="button" class="btn btn-pc btn-pc-outline btn-sm" onclick="window.print()"><i class="bi bi-printer me-1"></i>Imprimir / Guardar como PDF</button>
                    <form method="POST">
                        <button type="submit" name="toggle_medica" class="btn btn-pc btn-pc-outline btn-sm w-100">
                            <i class="bi bi-<?php echo $animal['compartir_info_medica'] ? 'eye-slash' : 'eye'; ?> me-1"></i>
                            <?php echo $animal['compartir_info_medica'] ? 'Ocultar info médica del público' : 'Mostrar info médica al público'; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="pc-card animar">
            <div class="pc-card-header"><i class="bi bi-syringe me-2"></i>Vacunas</div>
            <div class="pc-card-body">
                <?php if (mysqli_num_rows($vacunas) === 0): ?>
                    <p class="text-muted mb-0">Sin vacunas registradas.</p>
                <?php else: ?>
                    <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Vacuna</th><th>Aplicación</th><th>Próxima</th><th>Veterinario</th></tr></thead>
                        <tbody>
                        <?php while ($v = mysqli_fetch_assoc($vacunas)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($v['nombre_vacuna']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($v['fecha_aplicacion'])); ?></td>
                            <td><?php echo $v['proxima_dosis'] ? date('d/m/Y', strtotime($v['proxima_dosis'])) : '—'; ?></td>
                            <td><?php echo htmlspecialchars($v['veterinario'] ?: '—'); ?></td>
                        </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="pc-card mt-3 animar no-imprimir">
            <div class="pc-card-header"><i class="bi bi-qr-code-scan me-2"></i>Historial de ubicaciones y escaneos</div>
            <div class="pc-card-body">
                <?php if (empty($lecturas_arr) && empty($avisos_arr)): ?>
                    <p class="text-muted mb-0">Aún no se ha escaneado el código QR de tu mascota.</p>
                <?php else: ?>
                    <div id="mapaEscaneos" style="height:240px;border-radius:14px;overflow:hidden;" class="mb-3"></div>
                    <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Fecha</th><th>Origen</th><th>Detalle</th></tr></thead>
                        <tbody>
                        <?php foreach ($lecturas_arr as $l): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($l['fecha_hora'])); ?></td>
                                <td><span class="badge rounded-pill <?php echo $l['origen'] === 'publico' ? 'bg-warning-subtle text-warning' : 'bg-secondary-subtle text-secondary'; ?> px-2 py-1"><?php echo $l['origen'] === 'publico' ? 'Escaneo público' : 'Sistema'; ?></span></td>
                                <td class="small"><?php echo htmlspecialchars($l['ubicacion'] ?: '—'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php foreach ($avisos_arr as $a): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($a['fecha_registro'])); ?></td>
                                <td><span class="badge rounded-pill bg-success-subtle text-success px-2 py-1">Aviso al dueño</span></td>
                                <td class="small">
                                    <?php echo htmlspecialchars($a['lugar'] ?: '—'); ?>
                                    <?php if ($a['nombre_reportante'] || $a['telefono_reportante']): ?>
                                        · <?php echo htmlspecialchars($a['nombre_reportante']); ?><?php echo $a['telefono_reportante'] ? ' (' . htmlspecialchars($a['telefono_reportante']) . ')' : ''; ?>
                                    <?php endif; ?>
                                    <?php if ($a['comentarios']): ?><br><span class="text-muted"><?php echo htmlspecialchars($a['comentarios']); ?></span><?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .portal-nav, .no-imprimir, .portal-main .text-center.text-muted.small { display: none !important; }
    body { background: #fff !important; }
}
</style>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
generarQR("qrAnimal", "<?php echo htmlspecialchars($url_publica, ENT_QUOTES); ?>");

document.getElementById('btnDescargarQR')?.addEventListener('click', function () {
    setTimeout(function () {
        const canvas = document.querySelector('#qrAnimal canvas');
        if (!canvas) return;
        const enlace = document.createElement('a');
        enlace.download = 'qr_<?php echo preg_replace("/[^a-zA-Z0-9_-]/", "_", $animal["nombre"]); ?>.png';
        enlace.href = canvas.toDataURL('image/png');
        enlace.click();
    }, 150);
});

const puntosEscaneo = <?php echo json_encode($puntos_mapa); ?>;
const mapaEscaneosEl = document.getElementById('mapaEscaneos');
if (mapaEscaneosEl) {
    if (puntosEscaneo.length) {
        const mapaE = L.map('mapaEscaneos');
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(mapaE);
        const bounds = [];
        puntosEscaneo.forEach(function (p) {
            L.marker([p.lat, p.lng]).addTo(mapaE).bindPopup(p.detalle);
            bounds.push([p.lat, p.lng]);
        });
        mapaE.fitBounds(bounds, { maxZoom: 15 });
    } else {
        mapaEscaneosEl.outerHTML = '<p class="text-muted small mb-3">Ningún escaneo compartió ubicación todavía.</p>';
    }
}
</script>

<?php include("../includes/footer_dueno.php"); ?>
