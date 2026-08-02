<?php
// Variables esperadas: $raiz, $pagina_activa, $titulo_pagina
$raiz = $raiz ?? "";
$pagina_activa = $pagina_activa ?? "";
$titulo_pagina = $titulo_pagina ?? "Portal del dueño";
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($titulo_pagina); ?> · Portal PetChip</title>
<link rel="icon" href="<?php echo $raiz; ?>img/logo.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="<?php echo $raiz; ?>assets/css/style.css" rel="stylesheet">
<style>
    body { background: var(--pc-fondo); }
    .portal-nav { background: linear-gradient(160deg, var(--pc-navy-2), var(--pc-navy) 65%); }
    .portal-nav .nav-link { color: rgba(255,255,255,.8) !important; font-weight: 500; }
    .portal-nav .nav-link.activo, .portal-nav .nav-link:hover { color: #fff !important; }
    .portal-main { max-width: 1100px; margin: 0 auto; padding: 28px 18px 60px; }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg portal-nav mb-3 py-3">
  <div class="container portal-main px-3" style="padding:0!important;">
    <a class="navbar-brand d-flex align-items-center gap-2 text-white" href="<?php echo $raiz; ?>portal/mis_mascotas.php">
        <img src="<?php echo $raiz; ?>img/logo.png" style="width:34px;height:34px;">
        <span class="fw-semibold">Portal del dueño</span>
    </a>
    <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navPortal"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="navPortal">
        <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
            <li class="nav-item"><a href="<?php echo $raiz; ?>portal/mis_mascotas.php" class="nav-link <?php echo $pagina_activa=='mascotas'?'activo':''; ?>"><i class="bi bi-house-heart-fill me-1"></i>Inicio</a></li>
            <li class="nav-item"><a href="<?php echo $raiz; ?>portal/reportar_perdida.php" class="nav-link <?php echo $pagina_activa=='reportar'?'activo':''; ?>"><i class="bi bi-exclamation-octagon-fill me-1"></i>Reportar mascota perdida</a></li>
            <li class="nav-item"><a href="<?php echo $raiz; ?>portal/estado_reporte.php" class="nav-link <?php echo $pagina_activa=='estado_reporte'?'activo':''; ?>"><i class="bi bi-clipboard2-check-fill me-1"></i>Estado del reporte</a></li>
            <li class="nav-item"><a href="<?php echo $raiz; ?>portal/notificaciones.php" class="nav-link <?php echo $pagina_activa=='notificaciones'?'activo':''; ?>"><i class="bi bi-bell-fill me-1"></i>Notificaciones</a></li>
            <li class="nav-item"><a href="<?php echo $raiz; ?>portal/perfil.php" class="nav-link <?php echo $pagina_activa=='perfil'?'activo':''; ?>"><i class="bi bi-person-circle me-1"></i>Mi perfil</a></li>
            <li class="nav-item"><a href="<?php echo $raiz; ?>logout_dueno.php" class="nav-link"><i class="bi bi-box-arrow-right me-1"></i>Salir</a></li>
        </ul>
    </div>
  </div>
</nav>

<div class="portal-main">
<?php if (!empty($_SESSION['pc_acceso_denegado'])): ?>
    <div class="alert alert-danger d-flex align-items-center gap-2 animar">
        <i class="bi bi-shield-exclamation fs-5"></i>
        <div><?php echo htmlspecialchars($_SESSION['pc_acceso_denegado']); unset($_SESSION['pc_acceso_denegado']); ?></div>
    </div>
<?php endif; ?>
