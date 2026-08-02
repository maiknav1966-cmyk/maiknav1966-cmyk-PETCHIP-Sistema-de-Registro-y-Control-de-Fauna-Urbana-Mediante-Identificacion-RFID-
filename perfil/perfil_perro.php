<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("ver_animales", "../");

$id = (int) ($_GET["id"] ?? 0);

// Registrar nueva vacuna
if (isset($_POST["guardar_vacuna"])) {
    requerir_permiso("editar_animales", "../");
    $nombre_vacuna = limpiar_dato($conexion, $_POST["nombre_vacuna"]);
    $fecha_aplicacion = limpiar_dato($conexion, $_POST["fecha_aplicacion"]);
    $proxima_fecha = $_POST["proxima_fecha"] !== "" ? "'".limpiar_dato($conexion, $_POST["proxima_fecha"])."'" : "NULL";
    $veterinario = limpiar_dato($conexion, $_POST["veterinario"]);
    $id_veterinario = !empty($_POST["id_veterinario"]) ? (int) $_POST["id_veterinario"] : null;
    if ($id_veterinario && $veterinario === "") {
        $vf = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT nombre FROM veterinarios WHERE id_veterinario=$id_veterinario"));
        if ($vf) $veterinario = limpiar_dato($conexion, $vf["nombre"]);
    }
    $id_veterinario_sql = $id_veterinario ? $id_veterinario : "NULL";
    mysqli_query($conexion, "INSERT INTO vacunas(id_animal, nombre_vacuna, fecha_aplicacion, proxima_dosis, veterinario, id_veterinario)
        VALUES($id, '$nombre_vacuna', '$fecha_aplicacion', $proxima_fecha, '$veterinario', $id_veterinario_sql)");
    registrar_bitacora($conexion, "Registró la vacuna \"$nombre_vacuna\" a un animal", "Vacunas");
    header("Location: perfil_perro.php?id=$id"); exit();
}

// Registrar historial veterinario
if (isset($_POST["guardar_historial"])) {
    requerir_permiso("editar_animales", "../");
    $fecha = limpiar_dato($conexion, $_POST["fecha"]);
    $motivo = limpiar_dato($conexion, $_POST["motivo"]);
    $diagnostico = limpiar_dato($conexion, $_POST["diagnostico"]);
    $tratamiento = limpiar_dato($conexion, $_POST["tratamiento"]);
    $veterinario = limpiar_dato($conexion, $_POST["veterinario_h"]);
    $id_veterinario = !empty($_POST["id_veterinario_h"]) ? (int) $_POST["id_veterinario_h"] : null;
    if ($id_veterinario && $veterinario === "") {
        $vf = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT nombre FROM veterinarios WHERE id_veterinario=$id_veterinario"));
        if ($vf) $veterinario = limpiar_dato($conexion, $vf["nombre"]);
    }
    $id_veterinario_sql = $id_veterinario ? $id_veterinario : "NULL";
    mysqli_query($conexion, "INSERT INTO historial_veterinario(id_animal, fecha, motivo, diagnostico, tratamiento, veterinario, id_veterinario)
        VALUES($id, '$fecha', '$motivo', '$diagnostico', '$tratamiento', '$veterinario', $id_veterinario_sql)");
    registrar_bitacora($conexion, "Agregó historial veterinario a un animal", "Historial veterinario");
    header("Location: perfil_perro.php?id=$id"); exit();
}

// Regenerar el token público (invalida el QR anterior)
if (isset($_POST["regenerar_qr"])) {
    requerir_permiso("editar_animales", "../");
    regenerar_token_publico($conexion, $id);
    registrar_bitacora($conexion, "Regeneró el código QR de un animal", "QR");
    header("Location: perfil_perro.php?id=$id"); exit();
}

// Mostrar/ocultar información médica en la ficha pública
if (isset($_POST["toggle_medica"])) {
    requerir_permiso("editar_animales", "../");
    mysqli_query($conexion, "UPDATE perros SET compartir_info_medica = 1 - compartir_info_medica WHERE id_perro=$id");
    header("Location: perfil_perro.php?id=$id"); exit();
}

$animal = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT perros.*, duenos.nombre AS dueno_nombre, duenos.telefono AS dueno_telefono, duenos.id_dueno
    FROM perros INNER JOIN duenos ON perros.id_dueno = duenos.id_dueno WHERE perros.id_perro=$id"));

if (!$animal) { header("Location: ../perros/lista_perros.php"); exit(); }

$tag = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM tags_rfid WHERE id_animal=$id"));
$vacunas = mysqli_query($conexion, "SELECT * FROM vacunas WHERE id_animal=$id ORDER BY fecha_aplicacion DESC");
$historial = mysqli_query($conexion, "SELECT * FROM historial_veterinario WHERE id_animal=$id ORDER BY fecha DESC");

$veterinarios_activos = [];
$res_vet = mysqli_query($conexion, "SELECT id_veterinario, nombre, especialidad FROM veterinarios WHERE activo=1 ORDER BY nombre");
if ($res_vet) while ($v = mysqli_fetch_assoc($res_vet)) $veterinarios_activos[] = $v;

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
        $puntos_mapa[] = ["lat" => (float) $a["lat"], "lng" => (float) $a["lng"], "detalle" => "Aviso al dueño · " . htmlspecialchars($a["lugar"] ?: "")];
    }
}

$raiz = "../";
$pagina_activa = "perros";
$titulo_pagina = "Perfil de " . $animal['nombre'];
include("../includes/header.php");
?>

<div class="mb-4 animar">
    <a href="../perros/lista_perros.php" class="text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Volver al listado</a>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="pc-card text-center animar">
            <div class="pc-card-body">
                <img src="<?php echo $animal['foto'] ? '../uploads/perros/'.htmlspecialchars($animal['foto']) : '../img/logo.png'; ?>" class="rounded-4 mb-3" style="width:160px;height:160px;object-fit:cover;">
                <h4 class="mb-1"><?php echo htmlspecialchars($animal['nombre']); ?></h4>
                <p class="text-muted mb-2"><?php echo htmlspecialchars($animal['especie'] ?: 'Perro'); ?> · <?php echo htmlspecialchars($animal['raza'] ?: 'Sin raza'); ?></p>
                <?php echo badge_estado($animal['estado'] ?? 'Activo'); ?>
                <div class="mt-3 d-flex justify-content-center gap-2">
                    <?php if (tiene_permiso('editar_animales')): ?>
                    <a href="../perros/editar_perro.php?id=<?php echo $id; ?>" class="btn btn-pc btn-pc-outline btn-sm"><i class="bi bi-pencil me-1"></i>Editar</a>
                    <?php endif; ?>
                    <?php if (tiene_permiso('ver_reportes')): ?>
                    <a href="../reportes/pdf_ficha_animal.php?id=<?php echo $id; ?>" target="_blank" class="btn btn-pc btn-pc-primario btn-sm"><i class="bi bi-printer me-1"></i>Imprimir / Guardar como PDF</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="pc-card mt-3 animar">
            <div class="pc-card-header"><i class="bi bi-qr-code me-2"></i>Código QR de identificación</div>
            <div class="pc-card-body text-center">
                <div id="qrAnimal" class="d-inline-block mb-2"></div>
                <p class="small text-muted mb-3">Al escanearlo se abre la ficha pública de <?php echo htmlspecialchars($animal['nombre']); ?> (sin datos del dueño)</p>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-pc btn-pc-outline btn-sm" id="btnDescargarQR"><i class="bi bi-download me-1"></i>Descargar PNG</button>
                    <button type="button" class="btn btn-pc btn-pc-outline btn-sm" onclick="window.open('<?php echo htmlspecialchars($url_publica, ENT_QUOTES); ?>','_blank')"><i class="bi bi-box-arrow-up-right me-1"></i>Ver ficha pública</button>
                    <?php if (tiene_permiso('editar_animales')): ?>
                    <form method="POST" onsubmit="return confirm('El QR actual dejará de funcionar. ¿Regenerar de todas formas?');">
                        <button type="submit" name="regenerar_qr" class="btn btn-pc btn-pc-outline btn-sm w-100 text-danger"><i class="bi bi-arrow-repeat me-1"></i>Regenerar código</button>
                    </form>
                    <form method="POST" class="mt-1">
                        <button type="submit" name="toggle_medica" class="btn btn-pc btn-pc-outline btn-sm w-100">
                            <i class="bi bi-<?php echo $animal['compartir_info_medica'] ? 'eye-slash' : 'eye'; ?> me-1"></i>
                            <?php echo $animal['compartir_info_medica'] ? 'Ocultar info médica en la ficha pública' : 'Mostrar info médica en la ficha pública'; ?>
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="pc-card mt-3 animar">
            <div class="pc-card-header"><i class="bi bi-person-fill me-2"></i>Dueño</div>
            <div class="pc-card-body small">
                <p class="mb-1"><strong><?php echo htmlspecialchars($animal['dueno_nombre']); ?></strong></p>
                <p class="mb-2 text-muted"><?php echo htmlspecialchars($animal['dueno_telefono']); ?></p>
                <a href="perfil_dueno.php?id=<?php echo $animal['id_dueno']; ?>" class="btn btn-pc btn-pc-outline btn-sm w-100">Ver perfil del dueño</a>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="pc-card h-100 animar">
                    <div class="pc-card-header"><i class="bi bi-info-circle me-2"></i>Características</div>
                    <div class="pc-card-body small">
                        <p class="mb-2"><strong>Sexo:</strong> <?php echo htmlspecialchars($animal['sexo'] ?: '—'); ?></p>
                        <p class="mb-2"><strong>Edad:</strong> <?php echo htmlspecialchars($animal['edad'] ?: '—'); ?> años</p>
                        <p class="mb-2"><strong>Fecha de nacimiento:</strong> <?php echo $animal['fecha_nacimiento'] ? date('d/m/Y', strtotime($animal['fecha_nacimiento'])) : '—'; ?></p>
                        <p class="mb-2"><strong>Color:</strong> <?php echo htmlspecialchars($animal['color'] ?: '—'); ?></p>
                        <p class="mb-2"><strong>Peso:</strong> <?php echo htmlspecialchars($animal['peso'] ?: '—'); ?> kg</p>
                        <p class="mb-2"><strong>Tamaño:</strong> <?php echo htmlspecialchars($animal['tamano'] ?: '—'); ?></p>
                        <p class="mb-0"><strong>Colonia:</strong> <?php echo htmlspecialchars($animal['colonia'] ?: '—'); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="pc-card h-100 animar">
                    <div class="pc-card-header"><i class="bi bi-tag-fill me-2"></i>Identificación RFID</div>
                    <div class="pc-card-body small">
                        <?php if ($tag): ?>
                            <p class="mb-2"><strong>Código:</strong> <?php echo htmlspecialchars($tag['codigo_tag']); ?></p>
                            <p class="mb-2"><strong>Asignado el:</strong> <?php echo date('d/m/Y', strtotime($tag['fecha_asignacion'])); ?></p>
                            <p class="mb-2"><strong>Estado:</strong> <?php echo badge_estado($tag['estado'] ?? 'Activo'); ?></p>
                            <p class="mb-0"><strong>Esterilizado:</strong> <?php echo $animal['esterilizado'] ? 'Sí' : 'Pendiente'; ?></p>
                        <?php else: ?>
                            <p class="text-muted mb-2">Esta mascota aún no tiene un chip de identificación asignado.</p>
                            <a href="../tags/tags.php" class="btn btn-pc btn-pc-verde btn-sm">Asignar Tag</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if ($animal['observaciones']): ?>
            <div class="col-12">
                <div class="pc-card animar">
                    <div class="pc-card-header"><i class="bi bi-chat-square-text-fill me-2"></i>Observaciones</div>
                    <div class="pc-card-body small"><?php echo nl2br(htmlspecialchars($animal['observaciones'])); ?></div>
                </div>
            </div>
            <?php endif; ?>

            <!-- VACUNAS -->
            <div class="col-12">
                <div class="pc-card animar">
                    <div class="pc-card-header">
                        <span><i class="bi bi-shield-plus me-2"></i>Historial de vacunas</span>
                        <?php if (tiene_permiso('editar_animales')): ?>
                        <button class="btn btn-sm btn-pc btn-pc-verde" data-bs-toggle="modal" data-bs-target="#modalVacuna"><i class="bi bi-plus-lg"></i> Agregar</button>
                        <?php endif; ?>
                    </div>
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
            </div>

            <!-- HISTORIAL VETERINARIO / LINEA DE TIEMPO -->
            <div class="col-12">
                <div class="pc-card animar">
                    <div class="pc-card-header">
                        <span><i class="bi bi-clipboard2-pulse me-2"></i>Línea de tiempo veterinaria</span>
                        <?php if (tiene_permiso('editar_animales')): ?>
                        <button class="btn btn-sm btn-pc btn-pc-verde" data-bs-toggle="modal" data-bs-target="#modalHistorial"><i class="bi bi-plus-lg"></i> Agregar</button>
                        <?php endif; ?>
                    </div>
                    <div class="pc-card-body">
                        <?php if (mysqli_num_rows($historial) === 0): ?>
                            <p class="text-muted mb-0">Sin eventos veterinarios registrados.</p>
                        <?php else: ?>
                        <div class="timeline">
                            <?php while ($h = mysqli_fetch_assoc($historial)): ?>
                            <div class="timeline-item">
                                <div><strong><?php echo htmlspecialchars($h['motivo']); ?></strong></div>
                                <div class="fecha"><?php echo date('d/m/Y', strtotime($h['fecha'])); ?><?php echo $h['veterinario'] ? ' · '.htmlspecialchars($h['veterinario']) : ''; ?></div>
                                <?php if ($h['diagnostico']): ?><div class="small mt-1"><strong>Diagnóstico:</strong> <?php echo htmlspecialchars($h['diagnostico']); ?></div><?php endif; ?>
                                <?php if ($h['tratamiento']): ?><div class="small"><strong>Tratamiento:</strong> <?php echo htmlspecialchars($h['tratamiento']); ?></div><?php endif; ?>
                            </div>
                            <?php endwhile; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- HISTORIAL DE ESCANEOS DEL QR -->
            <div class="col-12">
                <div class="pc-card animar">
                    <div class="pc-card-header"><i class="bi bi-qr-code-scan me-2"></i>Historial de escaneos del QR</div>
                    <div class="pc-card-body">
                        <?php if (empty($lecturas_arr) && empty($avisos_arr)): ?>
                            <p class="text-muted mb-0">Aún no se ha escaneado el código QR de esta mascota.</p>
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
                                        <td class="small"><?php echo htmlspecialchars($l['ubicacion'] ?: '—'); ?><?php echo $l['usuario'] ? ' · ' . htmlspecialchars($l['usuario']) : ''; ?></td>
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
    </div>
</div>

<!-- MODAL VACUNA -->
<div class="modal fade" id="modalVacuna" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content pc-form necesita-validacion" novalidate>
      <div class="modal-header"><h5 class="modal-title">Agregar vacuna</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-2"><label class="form-label">Nombre de la vacuna *</label><input type="text" name="nombre_vacuna" class="form-control" required></div>
        <div class="mb-2"><label class="form-label">Fecha de aplicación *</label><input type="date" name="fecha_aplicacion" class="form-control" required></div>
        <div class="mb-2"><label class="form-label">Próxima fecha</label><input type="date" name="proxima_fecha" class="form-control"></div>
        <div class="mb-2">
            <label class="form-label">Veterinario</label>
            <select name="id_veterinario" class="form-select mb-1" onchange="this.form.veterinario.value = this.options[this.selectedIndex].dataset.nombre || '';">
                <option value="" data-nombre="">— Elegir del catálogo —</option>
                <?php foreach ($veterinarios_activos as $v): ?>
                <option value="<?php echo $v['id_veterinario']; ?>" data-nombre="<?php echo htmlspecialchars($v['nombre'], ENT_QUOTES); ?>"><?php echo htmlspecialchars($v['nombre']); ?><?php echo $v['especialidad'] ? ' · '.htmlspecialchars($v['especialidad']) : ''; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="veterinario" class="form-control" placeholder="O escribe el nombre manualmente">
        </div>
      </div>
      <div class="modal-footer"><button type="submit" name="guardar_vacuna" class="btn btn-pc btn-pc-verde">Guardar</button></div>
    </form>
  </div>
</div>

<!-- MODAL HISTORIAL -->
<div class="modal fade" id="modalHistorial" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content pc-form necesita-validacion" novalidate>
      <div class="modal-header"><h5 class="modal-title">Agregar evento veterinario</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-2"><label class="form-label">Fecha *</label><input type="date" name="fecha" class="form-control" required></div>
        <div class="mb-2"><label class="form-label">Motivo *</label><input type="text" name="motivo" class="form-control" required></div>
        <div class="mb-2"><label class="form-label">Diagnóstico</label><textarea name="diagnostico" class="form-control" rows="2"></textarea></div>
        <div class="mb-2"><label class="form-label">Tratamiento</label><textarea name="tratamiento" class="form-control" rows="2"></textarea></div>
        <div class="mb-2">
            <label class="form-label">Veterinario</label>
            <select name="id_veterinario_h" class="form-select mb-1" onchange="this.form.veterinario_h.value = this.options[this.selectedIndex].dataset.nombre || '';">
                <option value="" data-nombre="">— Elegir del catálogo —</option>
                <?php foreach ($veterinarios_activos as $v): ?>
                <option value="<?php echo $v['id_veterinario']; ?>" data-nombre="<?php echo htmlspecialchars($v['nombre'], ENT_QUOTES); ?>"><?php echo htmlspecialchars($v['nombre']); ?><?php echo $v['especialidad'] ? ' · '.htmlspecialchars($v['especialidad']) : ''; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="veterinario_h" class="form-control" placeholder="O escribe el nombre manualmente">
        </div>
      </div>
      <div class="modal-footer"><button type="submit" name="guardar_historial" class="btn btn-pc btn-pc-verde">Guardar</button></div>
    </form>
  </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="<?php echo $raiz; ?>assets/js/vendor/qrcode.min.js"></script>
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
        mapaEscaneosEl.outerHTML = '<p class="text-muted small mb-3">Ninguno de los escaneos registrados compartió su ubicación.</p>';
    }
}
</script>

<?php include("../includes/footer.php"); ?>
