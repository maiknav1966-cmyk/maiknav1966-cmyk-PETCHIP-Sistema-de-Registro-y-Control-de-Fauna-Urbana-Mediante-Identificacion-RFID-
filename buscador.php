<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("config/conexion.php");
include("includes/funciones.php");
requerir_sesion("");

$q = isset($_GET['q']) ? limpiar_dato($conexion, $_GET['q']) : "";

$animales = $q ? mysqli_query($conexion, "SELECT id_perro, nombre, especie FROM perros WHERE nombre LIKE '%$q%' LIMIT 10") : null;
$duenos = $q ? mysqli_query($conexion, "SELECT id_dueno, nombre FROM duenos WHERE nombre LIKE '%$q%' LIMIT 10") : null;
$tags = $q ? mysqli_query($conexion, "SELECT id_tag, codigo_tag FROM tags_rfid WHERE codigo_tag LIKE '%$q%' LIMIT 10") : null;

$raiz = ""; $pagina_activa = ""; $titulo_pagina = "Buscador global";
include("includes/header.php");
?>
<div class="mb-4 animar">
    <h4 class="mb-1"><i class="bi bi-search me-2 text-success"></i>Resultados para "<?php echo htmlspecialchars($q); ?>"</h4>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="pc-card animar"><div class="pc-card-header"><i class="bi bi-heart-fill me-2"></i>Mascotas</div>
        <div class="pc-card-body">
            <?php if (!$animales || mysqli_num_rows($animales)===0): ?><p class="text-muted mb-0">Sin resultados.</p><?php else: while($a=mysqli_fetch_assoc($animales)): ?>
                <a href="perfil/perfil_perro.php?id=<?php echo $a['id_perro']; ?>" class="d-block py-1 text-decoration-none"><i class="bi bi-chevron-right me-1"></i><?php echo htmlspecialchars($a['nombre']); ?> <span class="text-muted small">(<?php echo htmlspecialchars($a['especie']); ?>)</span></a>
            <?php endwhile; endif; ?>
        </div></div>
    </div>
    <div class="col-lg-4">
        <div class="pc-card animar"><div class="pc-card-header"><i class="bi bi-people-fill me-2"></i>Dueños</div>
        <div class="pc-card-body">
            <?php if (!$duenos || mysqli_num_rows($duenos)===0): ?><p class="text-muted mb-0">Sin resultados.</p><?php else: while($d=mysqli_fetch_assoc($duenos)): ?>
                <a href="perfil/perfil_dueno.php?id=<?php echo $d['id_dueno']; ?>" class="d-block py-1 text-decoration-none"><i class="bi bi-chevron-right me-1"></i><?php echo htmlspecialchars($d['nombre']); ?></a>
            <?php endwhile; endif; ?>
        </div></div>
    </div>
    <div class="col-lg-4">
        <div class="pc-card animar"><div class="pc-card-header"><i class="bi bi-tag-fill me-2"></i>Chips de identificación</div>
        <div class="pc-card-body">
            <?php if (!$tags || mysqli_num_rows($tags)===0): ?><p class="text-muted mb-0">Sin resultados.</p><?php else: while($t=mysqli_fetch_assoc($tags)): ?>
                <a href="consulta/resultado_tag.php?codigo_tag=<?php echo urlencode($t['codigo_tag']); ?>" class="d-block py-1 text-decoration-none"><i class="bi bi-chevron-right me-1"></i><?php echo htmlspecialchars($t['codigo_tag']); ?></a>
            <?php endwhile; endif; ?>
        </div></div>
    </div>
</div>
<?php include("includes/footer.php"); ?>
