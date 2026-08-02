<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("ver_reportes", "../");

$raiz = "../"; $pagina_activa = "reportes"; $titulo_pagina = "Centro de reportes";
include("../includes/header.php");
?>
<div class="mb-4 animar">
    <h4 class="mb-1"><i class="bi bi-file-earmark-bar-graph-fill me-2 text-success"></i>Centro de reportes</h4>
    <p class="text-muted mb-0">Genera y exporta reportes municipales en PDF o Excel (CSV)</p>
</div>

<div class="row g-3">
    <div class="col-md-6 col-lg-4">
        <a href="reporte_perros.php" class="accion-rapida animar">
            <div class="circulo bg-grad-verde"><i class="bi bi-heart-fill"></i></div>
            <span class="fw-semibold">Mascotas registradas</span>
            <span class="small text-muted text-center">Listado completo con especie, estado y esterilización</span>
        </a>
    </div>
    <div class="col-md-6 col-lg-4">
        <a href="reporte_duenos.php" class="accion-rapida animar animar-1">
            <div class="circulo bg-grad-azul"><i class="bi bi-people-fill"></i></div>
            <span class="fw-semibold">Dueños registrados</span>
            <span class="small text-muted text-center">Directorio de propietarios responsables</span>
        </a>
    </div>
    <div class="col-md-6 col-lg-4">
        <a href="reporte_tags.php" class="accion-rapida animar animar-2">
            <div class="circulo bg-grad-ambar"><i class="bi bi-tag-fill"></i></div>
            <span class="fw-semibold">Chips de identificación</span>
            <span class="small text-muted text-center">Identificadores asignados y su estado</span>
        </a>
    </div>
    <div class="col-md-6 col-lg-4">
        <a href="reporte_esterilizacion.php" class="accion-rapida animar animar-3">
            <div class="circulo bg-grad-morado"><i class="bi bi-clipboard2-pulse-fill"></i></div>
            <span class="fw-semibold">Esterilizaciones</span>
            <span class="small text-muted text-center">Mascotas esterilizadas vs. pendientes</span>
        </a>
    </div>
    <div class="col-md-6 col-lg-4">
        <a href="reporte_vacunas.php" class="accion-rapida animar animar-4">
            <div class="circulo bg-grad-cyan"><i class="bi bi-shield-plus"></i></div>
            <span class="fw-semibold">Vacunas</span>
            <span class="small text-muted text-center">Historial de vacunación por animal</span>
        </a>
    </div>
    <?php if (tiene_permiso('gestionar_campanas')): ?>
    <div class="col-md-6 col-lg-4">
        <a href="pdf_campanas.php" target="_blank" class="accion-rapida animar animar-5">
            <div class="circulo bg-grad-indigo"><i class="bi bi-calendar2-heart-fill"></i></div>
            <span class="fw-semibold">Campañas</span>
            <span class="small text-muted text-center">Avance y estado de las jornadas de esterilización</span>
        </a>
    </div>
    <?php endif; ?>
    <?php if (tiene_permiso('gestionar_extravio')): ?>
    <div class="col-md-6 col-lg-4">
        <a href="pdf_extravio.php" target="_blank" class="accion-rapida animar animar-5">
            <div class="circulo bg-grad-rojo"><i class="bi bi-search-heart"></i></div>
            <span class="fw-semibold">Mascotas perdidas</span>
            <span class="small text-muted text-center">Reportes ciudadanos de extravío pendientes y resueltos</span>
        </a>
    </div>
    <?php endif; ?>
    <div class="col-md-6 col-lg-4">
        <a href="../estadisticas/estadisticas.php" class="accion-rapida animar animar-5">
            <div class="circulo bg-grad-rojo"><i class="bi bi-bar-chart-line-fill"></i></div>
            <span class="fw-semibold">Estadísticas generales</span>
            <span class="small text-muted text-center">Gráficas del panorama municipal</span>
        </a>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
