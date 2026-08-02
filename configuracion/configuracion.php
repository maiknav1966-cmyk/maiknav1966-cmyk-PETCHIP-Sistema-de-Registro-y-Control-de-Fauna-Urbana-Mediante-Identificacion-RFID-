<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("ver_configuracion", "../");

// Version del sistema (constante, se muestra en toda la app y en los reportes PDF)
define("PETCHIP_VERSION", "PetChip 2.0");
define("PETCHIP_MUNICIPIO", "Ozumba, Estado de México");

function contar_config($conexion, $sql) {
    $r = mysqli_query($conexion, $sql);
    if (!$r) return 0;
    $f = mysqli_fetch_row($r);
    return (int) $f[0];
}

$total_animales  = contar_config($conexion, "SELECT COUNT(*) FROM perros");
$total_duenos     = contar_config($conexion, "SELECT COUNT(*) FROM duenos");
$total_usuarios   = contar_config($conexion, "SELECT COUNT(*) FROM usuarios");
$total_usuarios_activos = contar_config($conexion, "SELECT COUNT(*) FROM usuarios WHERE activo=1");
$total_tags       = contar_config($conexion, "SELECT COUNT(*) FROM tags_rfid");
$total_campanas   = contar_config($conexion, "SELECT COUNT(*) FROM campanas_esterilizacion");
$total_veterinarios = contar_config($conexion, "SELECT COUNT(*) FROM veterinarios WHERE activo=1");
$total_duenos_portal = contar_config($conexion, "SELECT COUNT(*) FROM duenos WHERE usuario_portal IS NOT NULL AND portal_activo=1");

// Estado de la base de datos
$bd_estado_ok = isset($conexion) && !$conexion->connect_error;
$bd_nombre = "petchip";
$r_tablas = mysqli_query($conexion, "SHOW TABLES");
$total_tablas = $r_tablas ? mysqli_num_rows($r_tablas) : 0;

// Info del servidor
$version_php   = phpversion();
$version_mysql = mysqli_get_server_info($conexion);
$software_srv  = $_SERVER['SERVER_SOFTWARE'] ?? 'No disponible';
$so_servidor   = PHP_OS;

// Sesion
$duracion_sesion_min = round(((int) ini_get('session.gc_maxlifetime')) / 60);
$nombre_sesion = session_name();

// Mensaje de prueba de conexion a BD (boton "Probar conexion")
$mensaje = ""; $tipo_mensaje = "success";
if (isset($_POST["probar_conexion"])) {
    if (!$conexion->connect_error) {
        $mensaje = "Conexión a la base de datos \"$bd_nombre\" verificada correctamente.";
    } else {
        $tipo_mensaje = "danger";
        $mensaje = "No fue posible conectar a la base de datos.";
    }
}

$raiz = "../"; $pagina_activa = "configuracion"; $titulo_pagina = "Configuración";
include("../includes/header.php");
?>
<div class="mb-4 animar">
    <h4 class="mb-1"><i class="bi bi-gear-fill me-2 text-success"></i>Configuración del sistema</h4>
    <p class="text-muted mb-0">Información general, estado del servidor y preferencias — solo Administrador</p>
</div>

<?php if ($mensaje): ?>
<div class="alert alert-<?php echo $tipo_mensaje; ?> pc-alert alert-auto d-flex align-items-center gap-2 animar">
    <i class="bi bi-info-circle-fill"></i> <?php echo htmlspecialchars($mensaje); ?>
</div>
<?php endif; ?>

<!-- TARJETAS RESUMEN -->
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
            <i class="bi bi-person-gear icono-fondo"></i>
            <div class="valor"><?php echo $total_usuarios; ?></div>
            <div class="etiqueta">Usuarios (<?php echo $total_usuarios_activos; ?> activos)</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card bg-grad-morado animar animar-4">
            <i class="bi bi-hdd-network-fill icono-fondo"></i>
            <div class="valor"><?php echo $bd_estado_ok ? 'En línea' : 'Error'; ?></div>
            <div class="etiqueta">Estado de la base de datos</div>
        </div>
    </div>
</div>

<!-- ACCESOS RAPIDOS DE ADMINISTRACION -->
<div class="mb-4 animar">
    <h6 class="mb-3"><i class="bi bi-speedometer2 me-2 text-success"></i>Panel de administración — accesos rápidos</h6>
    <div class="row g-3">
        <div class="col-6 col-lg-3">
            <a href="../usuarios/usuarios.php" class="text-decoration-none text-reset">
            <div class="pc-card h-100 animar animar-1"><div class="pc-card-body text-center">
                <i class="bi bi-person-gear fs-2 text-success"></i>
                <div class="fw-semibold mt-2">Usuarios</div>
                <div class="small text-muted"><?php echo $total_usuarios; ?> cuentas de staff</div>
            </div></div></a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="../veterinarios/veterinarios.php" class="text-decoration-none text-reset">
            <div class="pc-card h-100 animar animar-2"><div class="pc-card-body text-center">
                <i class="bi bi-heart-pulse-fill fs-2 text-success"></i>
                <div class="fw-semibold mt-2">Veterinarios</div>
                <div class="small text-muted"><?php echo $total_veterinarios; ?> activos</div>
            </div></div></a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="../duenos/lista_duenos.php" class="text-decoration-none text-reset">
            <div class="pc-card h-100 animar animar-3"><div class="pc-card-body text-center">
                <i class="bi bi-phone-fill fs-2 text-success"></i>
                <div class="fw-semibold mt-2">Portal de dueños</div>
                <div class="small text-muted"><?php echo $total_duenos_portal; ?> con acceso activo</div>
            </div></div></a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="../bitacora/lista_bitacora.php" class="text-decoration-none text-reset">
            <div class="pc-card h-100 animar animar-4"><div class="pc-card-body text-center">
                <i class="bi bi-journal-text fs-2 text-success"></i>
                <div class="fw-semibold mt-2">Bitácora</div>
                <div class="small text-muted">Actividad del sistema</div>
            </div></div></a>
        </div>
    </div>
</div>
    <div class="col-lg-6">
        <div class="pc-card h-100 animar">
            <div class="pc-card-body">
                <h6 class="mb-3"><i class="bi bi-info-circle-fill me-2 text-success"></i>Información del sistema</h6>
                <table class="table table-sm mb-0">
                    <tbody>
                        <tr><td class="text-muted">Sistema</td><td class="text-end fw-semibold">PetChip — Control de Fauna Urbana</td></tr>
                        <tr><td class="text-muted">Versión</td><td class="text-end"><span class="badge rounded-pill bg-success-subtle text-success px-3 py-2"><?php echo PETCHIP_VERSION; ?></span></td></tr>
                        <tr><td class="text-muted">Municipio</td><td class="text-end fw-semibold"><?php echo PETCHIP_MUNICIPIO; ?></td></tr>
                        <tr><td class="text-muted">Chips emitidos</td><td class="text-end fw-semibold"><?php echo $total_tags; ?></td></tr>
                        <tr><td class="text-muted">Campañas registradas</td><td class="text-end fw-semibold"><?php echo $total_campanas; ?></td></tr>
                        <tr><td class="text-muted">Fecha y hora del servidor</td><td class="text-end fw-semibold"><?php echo date('d/m/Y H:i'); ?></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- BASE DE DATOS -->
    <div class="col-lg-6">
        <div class="pc-card h-100 animar animar-1">
            <div class="pc-card-body">
                <h6 class="mb-3"><i class="bi bi-database-fill-check me-2 text-success"></i>Base de datos</h6>
                <table class="table table-sm mb-3">
                    <tbody>
                        <tr><td class="text-muted">Nombre de la base</td><td class="text-end fw-semibold"><?php echo htmlspecialchars($bd_nombre); ?></td></tr>
                        <tr><td class="text-muted">Estado</td><td class="text-end"><?php echo $bd_estado_ok ? badge_estado('Activo') : badge_estado('Perdido'); ?></td></tr>
                        <tr><td class="text-muted">Tablas detectadas</td><td class="text-end fw-semibold"><?php echo $total_tablas; ?></td></tr>
                        <tr><td class="text-muted">Motor</td><td class="text-end fw-semibold">MySQL / MariaDB</td></tr>
                    </tbody>
                </table>
                <form method="POST">
                    <button type="submit" name="probar_conexion" class="btn btn-pc btn-pc-outline btn-sm"><i class="bi bi-arrow-repeat me-1"></i>Probar conexión</button>
                </form>
            </div>
        </div>
    </div>

    <!-- SERVIDOR -->
    <div class="col-lg-6">
        <div class="pc-card h-100 animar animar-2">
            <div class="pc-card-body">
                <h6 class="mb-3"><i class="bi bi-server me-2 text-success"></i>Información del servidor</h6>
                <table class="table table-sm mb-0">
                    <tbody>
                        <tr><td class="text-muted">Versión de PHP</td><td class="text-end fw-semibold"><?php echo htmlspecialchars($version_php); ?></td></tr>
                        <tr><td class="text-muted">Versión de MySQL</td><td class="text-end fw-semibold"><?php echo htmlspecialchars($version_mysql); ?></td></tr>
                        <tr><td class="text-muted">Software del servidor</td><td class="text-end fw-semibold"><?php echo htmlspecialchars($software_srv); ?></td></tr>
                        <tr><td class="text-muted">Sistema operativo</td><td class="text-end fw-semibold"><?php echo htmlspecialchars($so_servidor); ?></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SESION Y APARIENCIA -->
    <div class="col-lg-6">
        <div class="pc-card h-100 animar animar-3">
            <div class="pc-card-body">
                <h6 class="mb-3"><i class="bi bi-sliders me-2 text-success"></i>Sesión y apariencia</h6>
                <table class="table table-sm mb-3">
                    <tbody>
                        <tr><td class="text-muted">Nombre de sesión</td><td class="text-end fw-semibold"><?php echo htmlspecialchars($nombre_sesion); ?></td></tr>
                        <tr><td class="text-muted">Duración de sesión (servidor)</td><td class="text-end fw-semibold">~<?php echo $duracion_sesion_min; ?> min</td></tr>
                        <tr><td class="text-muted">Tu usuario</td><td class="text-end fw-semibold"><?php echo htmlspecialchars($_SESSION['usuario']); ?></td></tr>
                        <tr><td class="text-muted">Tu rol</td><td class="text-end fw-semibold"><?php echo nombre_rol_legible($_SESSION['rol']); ?></td></tr>
                    </tbody>
                </table>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="small text-muted mb-0"><i class="bi bi-moon-stars me-1"></i>Modo oscuro</span>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="pcTemaToggleConfig" style="width:2.6em;height:1.4em;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ROLES Y PERMISOS -->
    <div class="col-12">
        <div class="pc-card animar animar-4">
            <div class="pc-card-body">
                <h6 class="mb-3"><i class="bi bi-shield-lock-fill me-2 text-success"></i>Roles y permisos del sistema</h6>
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <div class="p-3 rounded-4 bg-success-subtle h-100">
                            <div class="fw-semibold text-success"><i class="bi bi-person-badge-fill me-1"></i>Administrador</div>
                            <div class="small text-muted mt-1">Acceso total: usuarios, veterinarios, configuración, bitácora y todos los módulos.</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 rounded-4 bg-info-subtle h-100">
                            <div class="fw-semibold text-info"><i class="bi bi-heart-pulse-fill me-1"></i>Veterinario</div>
                            <div class="small text-muted mt-1">Gestiona animales, dueños, vacunas, historial médico, campañas y reportes.</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 rounded-4 bg-warning-subtle h-100">
                            <div class="fw-semibold text-warning"><i class="bi bi-shield-check me-1"></i>Encargado</div>
                            <div class="small text-muted mt-1">Encuentra mascotas, registra dueños y avisos de extravío en campo. Sin acceso a configuración.</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 rounded-4 bg-secondary-subtle h-100">
                            <div class="fw-semibold text-secondary"><i class="bi bi-phone-fill me-1"></i>Dueño</div>
                            <div class="small text-muted mt-1">Portal de autoservicio independiente: ve sus mascotas, QR, notificaciones y ubicaciones.</div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                <table class="table table-sm mb-0 align-middle">
                    <thead><tr><th>Permiso</th><th class="text-center">Administrador</th><th class="text-center">Veterinario</th><th class="text-center">Encargado</th></tr></thead>
                    <tbody>
                    <?php
                    $permisos_legibles = [
                        "crear_animales" => "Crear animales", "eliminar_animales" => "Eliminar animales",
                        "editar_duenos" => "Editar dueños", "eliminar_duenos" => "Eliminar dueños",
                        "gestionar_tags" => "Gestionar tags RFID", "gestionar_campanas" => "Gestionar campañas",
                        "gestionar_extravio" => "Registrar extravío/avisos", "gestionar_veterinarios" => "Administrar veterinarios",
                        "ver_estadisticas" => "Ver estadísticas", "gestionar_usuarios" => "Gestionar usuarios",
                    ];
                    $mapa_permisos = permisos_mapa();
                    foreach ($permisos_legibles as $clave => $etiqueta):
                        $roles_con_permiso = $mapa_permisos[$clave] ?? [];
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($etiqueta); ?></td>
                        <td class="text-center"><?php echo in_array('administrador', $roles_con_permiso, true) ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-dash text-muted"></i>'; ?></td>
                        <td class="text-center"><?php echo in_array('veterinario', $roles_con_permiso, true) ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-dash text-muted"></i>'; ?></td>
                        <td class="text-center"><?php echo (in_array('autoridad', $roles_con_permiso, true) || in_array('operador', $roles_con_permiso, true)) ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-dash text-muted"></i>'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ACERCA DEL SISTEMA -->
    <div class="col-12">
        <div class="pc-card animar animar-4">
            <div class="pc-card-body text-center py-4">
                <img src="../img/logo_completo.png" alt="PetChip" style="max-width:220px; width:100%; height:auto;" class="mb-3">
                <p class="text-muted mb-0">Sistema de identificación y control de fauna urbana con RFID.</p>
                <p class="text-muted mb-0">Desarrollado para el H. Ayuntamiento de <?php echo PETCHIP_MUNICIPIO; ?> · <?php echo PETCHIP_VERSION; ?></p>
            </div>
        </div>
    </div>
</div>

<script>
// Sincroniza el switch de modo oscuro de esta pantalla con el de la barra superior
document.addEventListener("DOMContentLoaded", function () {
    const swConfig = document.getElementById("pcTemaToggleConfig");
    if (!swConfig) return;
    swConfig.checked = document.documentElement.getAttribute("data-tema") === "oscuro";
    swConfig.addEventListener("change", function () {
        const btnNavbar = document.getElementById("pcTemaToggle");
        if (btnNavbar) btnNavbar.click();
    });
});
</script>

<?php include("../includes/footer.php"); ?>
