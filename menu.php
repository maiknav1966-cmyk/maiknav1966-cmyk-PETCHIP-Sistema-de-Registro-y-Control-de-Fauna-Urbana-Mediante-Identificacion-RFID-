<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("config/conexion.php");
include("includes/funciones.php");
requerir_sesion("");

date_default_timezone_set("America/Mexico_City");
$fecha = date("d/m/Y");
$hora = date("H:i");

function contar($conexion, $sql) {
    $r = mysqli_query($conexion, $sql);
    if (!$r) return 0;
    $f = mysqli_fetch_row($r);
    return (int) $f[0];
}

$total_animales     = contar($conexion, "SELECT COUNT(*) FROM perros");
$total_duenos       = contar($conexion, "SELECT COUNT(*) FROM duenos");
$total_tags_activos = contar($conexion, "SELECT COUNT(*) FROM tags_rfid WHERE estado='Activo'");
$total_esterilizados= contar($conexion, "SELECT COUNT(*) FROM perros WHERE esterilizado=1");
$total_pendientes   = $total_animales - $total_esterilizados;
$total_perdidos     = contar($conexion, "SELECT COUNT(*) FROM reportes_extravio WHERE tipo='Perdido' AND estado='Activo'");
$total_campanas_activas = contar($conexion, "SELECT COUNT(*) FROM campanas_esterilizacion WHERE estado IN ('Programada','En curso')");

$total_perros_esp = contar($conexion, "SELECT COUNT(*) FROM perros WHERE especie='Perro'");
$total_gatos_esp  = contar($conexion, "SELECT COUNT(*) FROM perros WHERE especie='Gato'");
$total_otros_esp  = $total_animales - $total_perros_esp - $total_gatos_esp;

// Últimos animales registrados
$ultimos_animales = [];
$res_ua = mysqli_query($conexion, "SELECT p.id_perro, p.nombre, p.especie, p.foto, p.estado, p.fecha_registro, d.nombre AS dueno
                                    FROM perros p LEFT JOIN duenos d ON p.id_dueno = d.id_dueno
                                    ORDER BY p.id_perro DESC LIMIT 5");
if ($res_ua) while ($f = mysqli_fetch_assoc($res_ua)) $ultimos_animales[] = $f;

// Últimas lecturas RFID
$ultimas_lecturas = [];
$res_ul = mysqli_query($conexion, "SELECT l.fecha_hora, l.usuario, l.ubicacion, t.codigo_tag, p.id_perro, p.nombre AS animal
                                    FROM lecturas_rfid l
                                    INNER JOIN tags_rfid t ON l.id_tag = t.id_tag
                                    INNER JOIN perros p ON t.id_animal = p.id_perro
                                    ORDER BY l.id_lectura DESC LIMIT 5");
if ($res_ul) while ($f = mysqli_fetch_assoc($res_ul)) $ultimas_lecturas[] = $f;

// Registros de los últimos 6 meses
$meses_labels = []; $meses_datos = [];
for ($i = 5; $i >= 0; $i--) {
    $mes = date("Y-m", strtotime("-$i months"));
    $meses_labels[] = date("M Y", strtotime("-$i months"));
    $meses_datos[] = contar($conexion, "SELECT COUNT(*) FROM perros WHERE DATE_FORMAT(fecha_registro,'%Y-%m')='$mes'");
}

// Actividad reciente
$actividad = [];
$res_act = mysqli_query($conexion, "SELECT usuario, accion, modulo, fecha_hora FROM bitacora ORDER BY id_bitacora DESC LIMIT 8");
if ($res_act) while ($f = mysqli_fetch_assoc($res_act)) $actividad[] = $f;

$raiz = "";
$pagina_activa = "dashboard";
$titulo_pagina = "Inicio";
include("includes/header.php");
?>

<div class="pc-banner-bienvenida d-flex flex-wrap justify-content-between align-items-center mb-4 animar gap-3">
    <div>
        <h3 class="mb-1 text-white">Hola, <?php echo htmlspecialchars($_SESSION["usuario"]); ?> 👋</h3>
        <p class="text-white mb-0" style="opacity:.85">
            <i class="bi bi-calendar3 me-1"></i><?php echo $fecha; ?> &nbsp; <i class="bi bi-clock me-1"></i><?php echo $hora; ?>
            &nbsp; <span class="badge rounded-pill bg-white text-dark ms-1"><i class="bi bi-person-badge me-1"></i><?php echo htmlspecialchars(nombre_rol_legible($_SESSION['rol'] ?? 'autoridad')); ?></span>
        </p>
    </div>
    <?php if (tiene_permiso('crear_animales')): ?>
    <a href="perros/perros.php" class="btn btn-pc btn-pc-verde"><i class="bi bi-plus-circle me-1"></i> Registrar mascota</a>
    <?php endif; ?>
</div>

<!-- TARJETAS DE ESTADISTICAS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card bg-grad-verde animar animar-1">
            <i class="bi bi-heart-fill icono-fondo"></i>
            <div class="valor"><?php echo $total_animales; ?></div>
            <div class="etiqueta">Mascotas registradas</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card bg-grad-azul animar animar-2">
            <i class="bi bi-people-fill icono-fondo"></i>
            <div class="valor"><?php echo $total_duenos; ?></div>
            <div class="etiqueta">Dueños registrados</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card bg-grad-ambar animar animar-3">
            <i class="bi bi-tag-fill icono-fondo"></i>
            <div class="valor"><?php echo $total_tags_activos; ?></div>
            <div class="etiqueta">Chips activos</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card bg-grad-morado animar animar-4">
            <i class="bi bi-clipboard2-pulse-fill icono-fondo"></i>
            <div class="valor"><?php echo $total_esterilizados; ?></div>
            <div class="etiqueta">Esterilizados</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card bg-grad-cyan animar animar-5">
            <i class="bi bi-hourglass-split icono-fondo"></i>
            <div class="valor"><?php echo $total_pendientes; ?></div>
            <div class="etiqueta">Pendientes esterilizar</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card bg-grad-rojo animar animar-6">
            <i class="bi bi-exclamation-octagon-fill icono-fondo"></i>
            <div class="valor"><?php echo $total_perdidos; ?></div>
            <div class="etiqueta">Reportes de perdidos</div>
        </div>
    </div>
    <?php if (tiene_permiso('gestionar_campanas')): ?>
    <div class="col-6 col-lg-3">
        <div class="stat-card bg-grad-indigo animar animar-1">
            <i class="bi bi-calendar2-heart-fill icono-fondo"></i>
            <div class="valor"><?php echo $total_campanas_activas; ?></div>
            <div class="etiqueta">Campañas activas</div>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-7">
        <div class="pc-card h-100 animar">
            <div class="pc-card-header"><span><i class="bi bi-graph-up me-2"></i>Registros de animales (últimos 6 meses)</span></div>
            <div class="pc-card-body"><canvas id="graficoMeses" height="110"></canvas></div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="pc-card h-100 animar">
            <div class="pc-card-header"><span><i class="bi bi-pie-chart-fill me-2"></i>Perros vs Gatos</span></div>
            <div class="pc-card-body"><canvas id="graficoEspecie" height="180"></canvas></div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12">
        <h6 class="text-muted mb-3">ACCESOS RÁPIDOS</h6>
        <div class="row g-3">
            <?php if (tiene_permiso('crear_duenos')): ?>
            <div class="col-6 col-md-2">
                <a href="duenos/duenos.php" class="accion-rapida animar">
                    <div class="circulo bg-grad-azul"><i class="bi bi-person-plus-fill"></i></div>
                    <span class="small fw-semibold text-center">Nuevo dueño</span>
                </a>
            </div>
            <?php endif; ?>
            <?php if (tiene_permiso('crear_animales')): ?>
            <div class="col-6 col-md-2">
                <a href="perros/perros.php" class="accion-rapida animar animar-1">
                    <div class="circulo bg-grad-verde"><i class="bi bi-heart-fill"></i></div>
                    <span class="small fw-semibold text-center">Nuevo animal</span>
                </a>
            </div>
            <?php endif; ?>
            <?php if (tiene_permiso('gestionar_tags')): ?>
            <div class="col-6 col-md-2">
                <a href="tags/tags.php" class="accion-rapida animar animar-2">
                    <div class="circulo bg-grad-ambar"><i class="bi bi-tag-fill"></i></div>
                    <span class="small fw-semibold text-center">Nuevo tag</span>
                </a>
            </div>
            <?php endif; ?>
            <?php if (tiene_permiso('consultar_rfid')): ?>
            <div class="col-6 col-md-2">
                <a href="consulta/buscar_tag.php" class="accion-rapida animar animar-3">
                    <div class="circulo bg-grad-cyan"><i class="bi bi-broadcast"></i></div>
                    <span class="small fw-semibold text-center">Consultar RFID</span>
                </a>
            </div>
            <?php endif; ?>
            <?php if (tiene_permiso('gestionar_campanas')): ?>
            <div class="col-6 col-md-2">
                <a href="campanas/campanas.php" class="accion-rapida animar animar-4">
                    <div class="circulo bg-grad-morado"><i class="bi bi-calendar2-heart-fill"></i></div>
                    <span class="small fw-semibold text-center">Campaña</span>
                </a>
            </div>
            <?php endif; ?>
            <?php if (tiene_permiso('ver_reportes')): ?>
            <div class="col-6 col-md-2">
                <a href="reportes/reporte.php" class="accion-rapida animar animar-5">
                    <div class="circulo bg-grad-rojo"><i class="bi bi-file-earmark-bar-graph-fill"></i></div>
                    <span class="small fw-semibold text-center">Reportes</span>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="pc-card h-100 animar">
            <div class="pc-card-header"><span><i class="bi bi-heart-fill me-2"></i>Últimos animales registrados</span>
                <?php if (tiene_permiso('ver_animales')): ?>
                <a href="perros/lista_perros.php" class="small">Ver todos</a>
                <?php endif; ?>
            </div>
            <div class="pc-card-body">
                <?php if (empty($ultimos_animales)): ?>
                    <p class="text-muted mb-0">Aún no hay animales registrados.</p>
                <?php else: ?>
                    <?php foreach ($ultimos_animales as $a): ?>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <img src="<?php echo $a['foto'] ? 'uploads/perros/'.htmlspecialchars($a['foto']) : 'img/logo.png'; ?>" class="avatar-mini">
                            <div class="flex-grow-1">
                                <div class="fw-semibold"><?php echo htmlspecialchars($a['nombre']); ?> <span class="text-muted small fw-normal">· <?php echo htmlspecialchars($a['especie']); ?></span></div>
                                <div class="text-muted small">Dueño: <?php echo htmlspecialchars($a['dueno'] ?: '—'); ?> · <?php echo tiempo_relativo($a['fecha_registro']); ?></div>
                            </div>
                            <?php echo badge_estado($a['estado']); ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="pc-card h-100 animar">
            <div class="pc-card-header"><span><i class="bi bi-broadcast me-2"></i>Últimas lecturas RFID</span>
                <?php if (tiene_permiso('consultar_rfid')): ?>
                <a href="consulta/buscar_tag.php" class="small">Consultar RFID</a>
                <?php endif; ?>
            </div>
            <div class="pc-card-body">
                <?php if (empty($ultimas_lecturas)): ?>
                    <p class="text-muted mb-0">Aún no se han registrado lecturas de tags.</p>
                <?php else: ?>
                    <div class="timeline">
                        <?php foreach ($ultimas_lecturas as $l): ?>
                            <div class="timeline-item">
                                <div><strong><?php echo htmlspecialchars($l['codigo_tag']); ?></strong> — <?php echo htmlspecialchars($l['animal']); ?></div>
                                <div class="fecha"><?php echo tiempo_relativo($l['fecha_hora']); ?><?php if($l['ubicacion']) echo " · ".htmlspecialchars($l['ubicacion']); ?><?php if($l['usuario']) echo " · ".htmlspecialchars($l['usuario']); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-12">
        <div class="pc-card animar">
            <div class="pc-card-header"><span><i class="bi bi-activity me-2"></i>Actividad reciente</span>
                <?php if (tiene_permiso('ver_bitacora')): ?>
                <a href="bitacora/lista_bitacora.php" class="small">Ver bitácora completa</a>
                <?php endif; ?>
            </div>
            <div class="pc-card-body">
                <?php if (empty($actividad)): ?>
                    <p class="text-muted mb-0">Aún no hay actividad registrada.</p>
                <?php else: ?>
                    <div class="timeline">
                        <?php foreach ($actividad as $a): ?>
                            <div class="timeline-item">
                                <div><strong><?php echo htmlspecialchars($a['usuario']); ?></strong> — <?php echo htmlspecialchars($a['accion']); ?></div>
                                <div class="fecha"><?php echo tiempo_relativo($a['fecha_hora']); ?><?php if($a['modulo']) echo " · ".htmlspecialchars($a['modulo']); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('graficoMeses'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($meses_labels); ?>,
        datasets: [{
            label: 'Mascotas registradas',
            data: <?php echo json_encode($meses_datos); ?>,
            backgroundColor: '#1E5FD9',
            borderRadius: 8
        }]
    },
    options: { plugins: { legend: { display:false } }, scales: { y: { beginAtZero:true, ticks:{precision:0} } } }
});

new Chart(document.getElementById('graficoEspecie'), {
    type: 'doughnut',
    data: {
        labels: ['Perros', 'Gatos', 'Otros'],
        datasets: [{
            data: [<?php echo $total_perros_esp; ?>, <?php echo $total_gatos_esp; ?>, <?php echo $total_otros_esp; ?>],
            backgroundColor: ['#1E5FD9', '#1C9756', '#FFB300']
        }]
    },
    options: { plugins: { legend: { position: 'bottom' } } }
});
</script>

<?php include("includes/footer.php"); ?>
