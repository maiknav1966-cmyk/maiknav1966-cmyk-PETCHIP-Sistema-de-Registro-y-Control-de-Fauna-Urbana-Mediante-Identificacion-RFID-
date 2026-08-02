<?php
// Variables esperadas antes de incluir este archivo:
// $raiz            -> ruta relativa hacia la carpeta principal ("" o "../")
// $pagina_activa   -> clave de la pagina activa para resaltar el menu
// $titulo_pagina   -> titulo mostrado en <title> y en el topbar
$raiz = $raiz ?? "";
$pagina_activa = $pagina_activa ?? "";
$titulo_pagina = $titulo_pagina ?? "PetChip";

// Notificaciones: ultimas acciones de la bitacora
$notificaciones = [];
if (isset($conexion)) {
    $res_notif = @mysqli_query($conexion, "SELECT accion, modulo, fecha_hora FROM bitacora ORDER BY id_bitacora DESC LIMIT 6");
    if ($res_notif) {
        while ($n = mysqli_fetch_assoc($res_notif)) $notificaciones[] = $n;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($titulo_pagina); ?> · PetChip</title>

<link rel="icon" href="<?php echo $raiz; ?>img/logo.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="<?php echo $raiz; ?>assets/css/style.css" rel="stylesheet">
</head>
<body>

<aside class="pc-sidebar" id="pcSidebar">
    <div class="marca">
        <img src="<?php echo $raiz; ?>img/logo.png" alt="PetChip">
        <span>PetChip</span>
    </div>
    <nav>
        <div class="nav-seccion">Principal</div>
        <a href="<?php echo $raiz; ?>menu.php" class="nav-link <?php echo $pagina_activa=='dashboard'?'activo':''; ?>"><i class="bi bi-grid-1x2-fill"></i> Inicio</a>
        <?php if (tiene_permiso('ver_estadisticas')): ?>
        <a href="<?php echo $raiz; ?>estadisticas/estadisticas.php" class="nav-link <?php echo $pagina_activa=='estadisticas'?'activo':''; ?>"><i class="bi bi-bar-chart-line-fill"></i> Estadísticas</a>
        <?php endif; ?>

        <div class="nav-seccion">Registro</div>
        <?php if (tiene_permiso('ver_duenos')): ?>
        <a href="<?php echo $raiz; ?>duenos/lista_duenos.php" class="nav-link <?php echo $pagina_activa=='duenos'?'activo':''; ?>"><i class="bi bi-people-fill"></i> Dueños</a>
        <?php endif; ?>
        <?php if (tiene_permiso('ver_animales')): ?>
        <a href="<?php echo $raiz; ?>perros/lista_perros.php" class="nav-link <?php echo $pagina_activa=='perros'?'activo':''; ?>"><i class="bi bi-heart-fill"></i> Mascotas</a>
        <?php endif; ?>
        <?php if (tiene_permiso('gestionar_tags')): ?>
        <a href="<?php echo $raiz; ?>tags/lista_tags.php" class="nav-link <?php echo $pagina_activa=='tags'?'activo':''; ?>"><i class="bi bi-tag-fill"></i> Chips de identificación</a>
        <?php endif; ?>
        <?php if (tiene_permiso('consultar_rfid')): ?>
        <a href="<?php echo $raiz; ?>consulta/buscar_tag.php" class="nav-link <?php echo $pagina_activa=='consulta'?'activo':''; ?>"><i class="bi bi-broadcast"></i> Encontrar mascota</a>
        <?php endif; ?>
        <?php if (tiene_permiso('ver_veterinarios')): ?>
        <a href="<?php echo $raiz; ?>veterinarios/veterinarios.php" class="nav-link <?php echo $pagina_activa=='veterinarios'?'activo':''; ?>"><i class="bi bi-heart-pulse-fill"></i> Veterinarios</a>
        <?php endif; ?>

        <?php if (tiene_permiso('gestionar_campanas') || tiene_permiso('gestionar_extravio')): ?>
        <div class="nav-seccion">Programas</div>
        <?php if (tiene_permiso('gestionar_campanas')): ?>
        <a href="<?php echo $raiz; ?>campanas/lista_campanas.php" class="nav-link <?php echo $pagina_activa=='campanas'?'activo':''; ?>"><i class="bi bi-calendar2-heart-fill"></i> Campañas</a>
        <?php endif; ?>
        <?php if (tiene_permiso('gestionar_extravio')): ?>
        <a href="<?php echo $raiz; ?>extravio/lista_extravio.php" class="nav-link <?php echo $pagina_activa=='extravio'?'activo':''; ?>"><i class="bi bi-search-heart"></i> Perdidos y encontrados</a>
        <?php endif; ?>
        <?php endif; ?>

        <?php if (tiene_permiso('ver_reportes') || tiene_permiso('ver_bitacora') || tiene_permiso('gestionar_usuarios') || tiene_permiso('ver_configuracion')): ?>
        <div class="nav-seccion">Administración</div>
        <?php if (tiene_permiso('ver_reportes')): ?>
        <a href="<?php echo $raiz; ?>reportes/reporte.php" class="nav-link <?php echo $pagina_activa=='reportes'?'activo':''; ?>"><i class="bi bi-file-earmark-bar-graph-fill"></i> Reportes</a>
        <?php endif; ?>
        <?php if (tiene_permiso('ver_bitacora')): ?>
        <a href="<?php echo $raiz; ?>bitacora/lista_bitacora.php" class="nav-link <?php echo $pagina_activa=='bitacora'?'activo':''; ?>"><i class="bi bi-clock-history"></i> Bitácora</a>
        <?php endif; ?>
        <?php if (tiene_permiso('gestionar_usuarios')): ?>
        <a href="<?php echo $raiz; ?>usuarios/usuarios.php" class="nav-link <?php echo $pagina_activa=='usuarios'?'activo':''; ?>"><i class="bi bi-person-gear"></i> Gestión de usuarios</a>
        <?php endif; ?>
        <?php if (tiene_permiso('ver_configuracion')): ?>
        <a href="<?php echo $raiz; ?>configuracion/configuracion.php" class="nav-link <?php echo $pagina_activa=='configuracion'?'activo':''; ?>"><i class="bi bi-gear-fill"></i> Configuración</a>
        <?php endif; ?>
        <?php endif; ?>
    </nav>
    <div class="salir-box">
        <a href="<?php echo $raiz; ?>logout.php" class="nav-link" style="background:rgba(255,255,255,.08)"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a>
    </div>
</aside>

<div class="pc-contenido">
    <header class="pc-topbar">
        <button class="pc-icon-btn pc-menu-toggle" id="pcMenuToggle"><i class="bi bi-list"></i></button>

        <div class="buscador">
            <i class="bi bi-search"></i>
            <input type="text" id="pcBuscadorGlobal" data-base="<?php echo $raiz; ?>" placeholder="Buscar animal, dueño o tag… (Enter)">
        </div>

        <div class="ms-auto d-flex align-items-center gap-2">
            <button class="pc-icon-btn" id="pcTemaToggle" title="Modo oscuro"><i class="bi bi-moon-stars"></i></button>

            <div class="dropdown">
                <button class="pc-icon-btn" data-bs-toggle="dropdown"><i class="bi bi-bell-fill"></i><?php if(count($notificaciones)>0): ?><span class="punto"></span><?php endif; ?></button>
                <div class="dropdown-menu dropdown-menu-end p-2" style="width:320px; max-height:360px; overflow:auto;">
                    <h6 class="dropdown-header">Actividad reciente</h6>
                    <?php if (empty($notificaciones)): ?>
                        <p class="text-muted small px-2">Sin actividad reciente.</p>
                    <?php else: foreach ($notificaciones as $n): ?>
                        <div class="dropdown-item small py-2">
                            <i class="bi bi-dot text-success"></i> <?php echo htmlspecialchars($n['accion']); ?>
                            <div class="text-muted" style="font-size:.72rem;"><?php echo tiempo_relativo($n['fecha_hora']); ?></div>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>

            <div class="dropdown">
                <button class="d-flex align-items-center gap-2 btn btn-light rounded-pill px-2 py-1" data-bs-toggle="dropdown" style="border:1px solid var(--pc-borde)">
                    <span class="d-flex align-items-center justify-content-center rounded-circle text-white" style="width:32px;height:32px;font-weight:600;background:var(--pc-primario);"><?php echo strtoupper(substr($_SESSION['usuario'] ?? 'U',0,1)); ?></span>
                    <span class="d-none d-md-inline small fw-semibold"><?php echo htmlspecialchars($_SESSION['usuario'] ?? ''); ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><span class="dropdown-item-text small text-muted">Rol: <?php echo htmlspecialchars(nombre_rol_legible($_SESSION['rol'] ?? 'autoridad')); ?></span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?php echo $raiz; ?>mi_cuenta.php"><i class="bi bi-key me-2"></i>Cambiar mi contraseña</a></li>
                    <li><a class="dropdown-item" href="<?php echo $raiz; ?>logout.php"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a></li>
                </ul>
            </div>
        </div>
    </header>

    <main class="pc-main">
    <?php if (!empty($_SESSION['pc_acceso_denegado'])): ?>
        <div class="alert alert-danger d-flex align-items-center gap-2 animar">
            <i class="bi bi-shield-exclamation fs-5"></i>
            <div><?php echo htmlspecialchars($_SESSION['pc_acceso_denegado']); unset($_SESSION['pc_acceso_denegado']); ?></div>
        </div>
    <?php endif; ?>
