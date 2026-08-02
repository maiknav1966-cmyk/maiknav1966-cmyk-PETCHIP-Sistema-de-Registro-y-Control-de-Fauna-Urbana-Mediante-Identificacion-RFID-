<?php
session_start();
include("includes/funciones.php");

// Si ya hay una sesión activa (staff o dueño), enviarlo directo a su panel.
if (isset($_SESSION["usuario"])) { header("Location: menu.php"); exit(); }
if (isset($_SESSION["dueno_id"])) { header("Location: portal/inicio.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PetChip · ¿Cómo quieres entrar?</title>
<link rel="icon" href="img/logo.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
<style>
.portal-wrapper{
    min-height:100vh; padding:40px 20px; position:relative; overflow:hidden;
    background:
        linear-gradient(160deg, rgba(10,14,28,.85), rgba(18,62,153,.55) 55%, rgba(20,116,63,.55)),
        url('img/fondo_login.png') center/cover no-repeat fixed;
}
.portal-header{ text-align:center; color:#fff; max-width:720px; margin:0 auto 36px; position:relative; z-index:1; }
.portal-header img{ width:78px; height:78px; object-fit:contain; margin-bottom:14px; filter:drop-shadow(0 4px 10px rgba(0,0,0,.4)); }
.portal-header p{ opacity:.88; }
.portal-grid{ max-width:1180px; margin:0 auto; position:relative; z-index:1; }
.portal-tarjeta{
    border-radius:22px; border:1px solid rgba(255,255,255,.25);
    background:rgba(255,255,255,.14); backdrop-filter:blur(18px) saturate(150%);
    -webkit-backdrop-filter:blur(18px) saturate(150%);
    color:#fff; padding:28px 24px; height:100%;
    display:flex; flex-direction:column; text-decoration:none;
    transition:transform .25s ease, box-shadow .25s ease, background .25s ease;
    box-shadow:0 15px 40px rgba(0,0,0,.25);
}
.portal-tarjeta:hover{ transform:translateY(-6px); background:rgba(255,255,255,.22); color:#fff; box-shadow:0 22px 50px rgba(0,0,0,.35); }
.portal-icono{
    width:58px; height:58px; border-radius:16px; display:flex; align-items:center; justify-content:center;
    font-size:1.6rem; margin-bottom:16px;
}
.portal-tarjeta h5{ font-family:'Poppins',sans-serif; margin-bottom:8px; }
.portal-tarjeta p{ opacity:.88; font-size:.92rem; margin-bottom:14px; flex-grow:1; }
.portal-tarjeta .portal-ir{ font-size:.85rem; font-weight:600; display:flex; align-items:center; gap:6px; }
.portal-extra a{ color:rgba(255,255,255,.85); text-decoration:underline; text-decoration-color:rgba(255,255,255,.4); }
.portal-extra a:hover{ color:#fff; }
</style>
</head>
<body>

<div class="portal-wrapper">
    <div class="portal-header">
        <img src="img/logo.png" alt="PetChip">
        <h2 class="mb-2">¿Cómo quieres entrar?</h2>
        <p class="mb-0">Selecciona tu tipo de acceso al sistema de control de fauna urbana de Ozumba.</p>
    </div>

    <div class="portal-grid row g-4">

        <div class="col-sm-6 col-lg-4">
            <a href="login.php?acceso=administrador" class="portal-tarjeta animar">
                <div class="portal-icono bg-grad-morado"><i class="bi bi-shield-lock-fill"></i></div>
                <h5>Administrador</h5>
                <p>Control total del sistema: usuarios, configuración, reportes, estadísticas y bitácora.</p>
                <span class="portal-ir">Entrar <i class="bi bi-arrow-right"></i></span>
            </a>
        </div>

        <div class="col-sm-6 col-lg-4">
            <a href="login.php?acceso=encargado" class="portal-tarjeta animar">
                <div class="portal-icono bg-grad-azul"><i class="bi bi-person-badge-fill"></i></div>
                <h5>Encargado</h5>
                <p>Registro de mascotas, dueños, chips de identificación y atención de extravíos en campo.</p>
                <span class="portal-ir">Entrar <i class="bi bi-arrow-right"></i></span>
            </a>
        </div>

        <div class="col-sm-6 col-lg-4">
            <a href="login.php?acceso=veterinario" class="portal-tarjeta animar">
                <div class="portal-icono bg-grad-verde"><i class="bi bi-heart-pulse-fill"></i></div>
                <h5>Veterinario</h5>
                <p>Historial clínico, vacunas, esterilización y campañas de las mascotas registradas.</p>
                <span class="portal-ir">Entrar <i class="bi bi-arrow-right"></i></span>
            </a>
        </div>

        <div class="col-sm-6 col-lg-4">
            <a href="login_dueno.php" class="portal-tarjeta animar">
                <div class="portal-icono bg-grad-ambar"><i class="bi bi-house-heart-fill"></i></div>
                <h5>Dueño de mascota</h5>
                <p>Consulta el expediente de tus mascotas, sus vacunas, su chip y su código QR.</p>
                <span class="portal-ir">Entrar a mi portal <i class="bi bi-arrow-right"></i></span>
            </a>
        </div>

        <div class="col-sm-6 col-lg-4">
            <a href="publico/buscar_chip.php" class="portal-tarjeta animar" target="_blank">
                <div class="portal-icono bg-grad-cyan"><i class="bi bi-search-heart"></i></div>
                <h5>Encontré una mascota</h5>
                <p>¿Encontraste un animal con chip? Búscalo aquí y avisa a su dueño, sin necesidad de cuenta.</p>
                <span class="portal-ir">Buscar mascota <i class="bi bi-arrow-right"></i></span>
            </a>
        </div>

        <div class="col-sm-6 col-lg-4">
            <a href="publico/perdidos.php" class="portal-tarjeta animar" target="_blank">
                <div class="portal-icono bg-grad-rojo"><i class="bi bi-geo-alt-fill"></i></div>
                <h5>Perdidos y encontrados</h5>
                <p>Consulta el mapa de reportes de mascotas perdidas o encontradas en el municipio.</p>
                <span class="portal-ir">Ver mapa <i class="bi bi-arrow-right"></i></span>
            </a>
        </div>

    </div>

    <div class="portal-extra text-center small mt-4">
        <p class="mb-0">Universidad Politécnica de Atlautla · Proyecto Integrador</p>
    </div>

    <div class="portal-creditos text-center mt-4">
        <p class="fw-semibold mb-2" style="color:#fff; opacity:.9;">🐾 Integrantes del proyecto 🐾</p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <span class="portal-credito-chip">🐾 Alcantara Romero Emmanuel</span>
            <span class="portal-credito-chip">🐾 Esteban Emilio Cardenas Argüelles</span>
            <span class="portal-credito-chip">🐾 Miguel Esteban Galicia Casaroja</span>
            <span class="portal-credito-chip">🐾 Rosa Maria Martinez Roa</span>
            <span class="portal-credito-chip">🐾 Victor Mario Nava Sanchez</span>
        </div>
    </div>
</div>

<style>
.portal-credito-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.25);
    color: #fff;
    padding: 6px 14px;
    border-radius: 999px;
    font-size: .82rem;
    backdrop-filter: blur(2px);
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
