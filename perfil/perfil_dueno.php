<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("ver_duenos", "../");

$id = (int) ($_GET["id"] ?? 0);

if (isset($_POST["marcar_leidas"])) {
    mysqli_query($conexion, "UPDATE notificaciones SET leida=1 WHERE id_dueno=$id");
    header("Location: perfil_dueno.php?id=$id"); exit();
}

$clave_generada = "";
// Crear o restablecer el acceso al portal de autoservicio del dueño (solo Administrador/Veterinario)
if (isset($_POST["generar_portal"]) && tiene_permiso("editar_duenos")) {
    $usuario_portal = limpiar_dato($conexion, $_POST["usuario_portal"]);
    if ($usuario_portal === "") {
        $mensaje_portal = "Escribe un usuario para el portal.";
    } else {
        $existe = mysqli_query($conexion, "SELECT id_dueno FROM duenos WHERE usuario_portal='$usuario_portal' AND id_dueno<>$id");
        if ($existe && mysqli_num_rows($existe) > 0) {
            $mensaje_portal = "Ese nombre de usuario del portal ya está en uso.";
        } else {
            $clave_generada = substr(bin2hex(random_bytes(5)), 0, 8);
            $hash_portal = password_hash($clave_generada, PASSWORD_DEFAULT);
            mysqli_query($conexion, "UPDATE duenos SET usuario_portal='$usuario_portal', contrasena_portal='" . mysqli_real_escape_string($conexion, $hash_portal) . "', portal_activo=1 WHERE id_dueno=$id");
            registrar_bitacora($conexion, "Generó acceso al portal para el dueño \"$usuario_portal\"", "Portal dueños");
        }
    }
}
if (isset($_POST["revocar_portal"]) && tiene_permiso("editar_duenos")) {
    mysqli_query($conexion, "UPDATE duenos SET portal_activo=0 WHERE id_dueno=$id");
    registrar_bitacora($conexion, "Revocó el acceso al portal de un dueño", "Portal dueños");
    header("Location: perfil_dueno.php?id=$id"); exit();
}

$dueno = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM duenos WHERE id_dueno=$id"));
if (!$dueno) { header("Location: ../duenos/lista_duenos.php"); exit(); }

$mascotas = mysqli_query($conexion, "SELECT * FROM perros WHERE id_dueno=$id ORDER BY nombre");

$notifs_arr = [];
$res_n = mysqli_query($conexion, "SELECT * FROM notificaciones WHERE id_dueno=$id ORDER BY fecha_creacion DESC LIMIT 20");
while ($n = mysqli_fetch_assoc($res_n)) $notifs_arr[] = $n;
$no_leidas = count(array_filter($notifs_arr, function ($n) { return (int) $n["leida"] === 0; }));

$nombre_escapado = limpiar_dato($conexion, $dueno['nombre']);
$historial_dueno = mysqli_query($conexion, "SELECT usuario, accion, modulo, fecha_hora FROM bitacora
    WHERE accion LIKE '%$nombre_escapado%' ORDER BY id_bitacora DESC LIMIT 6");

$raiz = "../";
$pagina_activa = "duenos";
$titulo_pagina = "Perfil de " . $dueno['nombre'];
include("../includes/header.php");
?>

<div class="mb-4 animar">
    <a href="../duenos/lista_duenos.php" class="text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Volver al listado</a>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="pc-card text-center animar">
            <div class="pc-card-body">
                <img src="<?php echo $dueno['foto'] ? '../uploads/duenos/'.htmlspecialchars($dueno['foto']) : '../img/logo.png'; ?>" class="rounded-4 mb-3" style="width:150px;height:150px;object-fit:cover;">
                <h4 class="mb-1"><?php echo htmlspecialchars($dueno['nombre']); ?></h4>
                <p class="text-muted mb-3"><?php echo htmlspecialchars($dueno['colonia'] ?: ''); ?><?php echo $dueno['colonia'] ? ' · ' : ''; ?><?php echo htmlspecialchars($dueno['municipio']); ?></p>
                <?php if (tiene_permiso('editar_duenos')): ?>
                <a href="../duenos/editar_dueno.php?id=<?php echo $id; ?>" class="btn btn-pc btn-pc-outline btn-sm"><i class="bi bi-pencil me-1"></i>Editar</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="pc-card mt-3 animar">
            <div class="pc-card-header"><i class="bi bi-person-vcard me-2"></i>Datos de contacto</div>
            <div class="pc-card-body small">
                <p class="mb-2"><i class="bi bi-telephone me-2"></i><?php echo htmlspecialchars($dueno['telefono']); ?></p>
                <p class="mb-2"><i class="bi bi-envelope me-2"></i><?php echo htmlspecialchars($dueno['correo'] ?: '—'); ?></p>
                <p class="mb-2"><i class="bi bi-geo-alt me-2"></i><?php echo htmlspecialchars($dueno['direccion']); ?></p>
                <p class="mb-0"><i class="bi bi-mailbox me-2"></i>C.P. <?php echo htmlspecialchars($dueno['codigo_postal'] ?: '—'); ?></p>
                <p class="mb-0 mt-2 text-muted"><i class="bi bi-calendar-check me-2"></i>Registrado el <?php echo date('d/m/Y', strtotime($dueno['fecha_registro'])); ?></p>
            </div>
        </div>

        <?php if (tiene_permiso('editar_duenos')): ?>
        <div class="pc-card mt-3 animar">
            <div class="pc-card-header"><i class="bi bi-phone-fill me-2"></i>Portal de autoservicio</div>
            <div class="pc-card-body small">
                <?php if (!empty($mensaje_portal)): ?>
                <div class="alert alert-danger pc-alert py-2 px-3 small"><?php echo htmlspecialchars($mensaje_portal); ?></div>
                <?php endif; ?>
                <?php if ($clave_generada): ?>
                <div class="alert alert-success pc-alert py-2 px-3 small">
                    <strong>Acceso generado.</strong> Comparte estos datos con el dueño (solo se muestran una vez):<br>
                    Usuario: <strong><?php echo htmlspecialchars($dueno['usuario_portal']); ?></strong><br>
                    Contraseña: <strong><?php echo htmlspecialchars($clave_generada); ?></strong>
                </div>
                <?php endif; ?>

                <?php if (!empty($dueno['usuario_portal'])): ?>
                    <p class="mb-2">Usuario del portal: <strong><?php echo htmlspecialchars($dueno['usuario_portal']); ?></strong></p>
                    <p class="mb-2">Estado:
                        <?php if ((int)$dueno['portal_activo'] === 1): ?>
                            <span class="badge rounded-pill bg-success-subtle text-success px-2 py-1">Activo</span>
                        <?php else: ?>
                            <span class="badge rounded-pill bg-secondary-subtle text-secondary px-2 py-1">Revocado</span>
                        <?php endif; ?>
                    </p>
                <?php else: ?>
                    <p class="text-muted mb-2">Este dueño aún no tiene acceso al portal de autoservicio (para ver sus mascotas, notificaciones y ubicaciones sin necesidad del panel de staff).</p>
                <?php endif; ?>

                <form method="POST" class="d-flex gap-1 mb-1">
                    <input type="text" name="usuario_portal" class="form-control form-control-sm" placeholder="usuario del portal" value="<?php echo htmlspecialchars($dueno['usuario_portal'] ?? ''); ?>" required>
                    <button type="submit" name="generar_portal" class="btn btn-sm btn-pc btn-pc-verde text-nowrap"><i class="bi bi-key-fill me-1"></i><?php echo $dueno['usuario_portal'] ? 'Restablecer' : 'Crear acceso'; ?></button>
                </form>
                <?php if (!empty($dueno['usuario_portal']) && (int)$dueno['portal_activo'] === 1): ?>
                <form method="POST" onsubmit="return confirm('¿Revocar el acceso de este dueño al portal?');">
                    <button type="submit" name="revocar_portal" class="btn btn-sm btn-pc btn-pc-rojo w-100"><i class="bi bi-slash-circle me-1"></i>Revocar acceso</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-8">
        <div class="pc-card animar">
            <div class="pc-card-header">
                <span><i class="bi bi-heart-fill me-2"></i>Mascotas registradas (<?php echo mysqli_num_rows($mascotas); ?>)</span>
                <a href="../perros/perros.php" class="btn btn-sm btn-pc btn-pc-verde"><i class="bi bi-plus-lg"></i> Nueva mascota</a>
            </div>
            <div class="pc-card-body">
                <?php if (mysqli_num_rows($mascotas) === 0): ?>
                    <p class="text-muted mb-0">Este dueño aún no tiene mascotas registradas.</p>
                <?php else: ?>
                <div class="row g-3">
                <?php while ($m = mysqli_fetch_assoc($mascotas)): ?>
                    <div class="col-md-6">
                        <a href="perfil_perro.php?id=<?php echo $m['id_perro']; ?>" class="text-decoration-none text-reset">
                        <div class="d-flex align-items-center gap-3 p-2 rounded-3" style="border:1px solid var(--pc-borde);">
                            <img src="<?php echo $m['foto'] ? '../uploads/perros/'.htmlspecialchars($m['foto']) : '../img/logo.png'; ?>" class="avatar-mini" style="width:52px;height:52px;">
                            <div>
                                <div class="fw-semibold"><?php echo htmlspecialchars($m['nombre']); ?></div>
                                <div class="small text-muted"><?php echo htmlspecialchars($m['especie'] ?: 'Perro'); ?> · <?php echo badge_estado($m['estado'] ?? 'Activo'); ?></div>
                            </div>
                        </div>
                        </a>
                    </div>
                <?php endwhile; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="pc-card mt-3 animar">
            <div class="pc-card-header">
                <span><i class="bi bi-bell-fill me-2"></i>Notificaciones<?php if ($no_leidas > 0): ?> <span class="badge bg-danger rounded-pill ms-1"><?php echo $no_leidas; ?></span><?php endif; ?></span>
                <?php if ($no_leidas > 0): ?>
                <form method="POST"><button type="submit" name="marcar_leidas" class="btn btn-sm btn-pc btn-pc-outline">Marcar leídas</button></form>
                <?php endif; ?>
            </div>
            <div class="pc-card-body">
                <?php if (empty($notifs_arr)): ?>
                    <p class="text-muted mb-0">Sin notificaciones todavía. Aquí aparecerán los avisos cuando alguien escanee el QR de una mascota de este dueño o reporte haberla encontrado.</p>
                <?php else: ?>
                    <div class="timeline">
                    <?php foreach ($notifs_arr as $n): ?>
                        <div class="timeline-item">
                            <div><?php echo (int) $n['leida'] === 0 ? '<span class="badge bg-primary-subtle text-primary rounded-pill me-1">Nuevo</span>' : ''; ?><?php echo htmlspecialchars($n['mensaje']); ?></div>
                            <div class="fecha"><?php echo tiempo_relativo($n['fecha_creacion']); ?></div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (tiene_permiso('ver_bitacora') && mysqli_num_rows($historial_dueno) > 0): ?>
        <div class="pc-card mt-3 animar">
            <div class="pc-card-header"><i class="bi bi-clock-history me-2"></i>Historial</div>
            <div class="pc-card-body">
                <div class="timeline">
                    <?php while ($h = mysqli_fetch_assoc($historial_dueno)): ?>
                    <div class="timeline-item">
                        <div><strong><?php echo htmlspecialchars($h['usuario']); ?></strong> — <?php echo htmlspecialchars($h['accion']); ?></div>
                        <div class="fecha"><?php echo tiempo_relativo($h['fecha_hora']); ?><?php if($h['modulo']) echo " · ".htmlspecialchars($h['modulo']); ?></div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
