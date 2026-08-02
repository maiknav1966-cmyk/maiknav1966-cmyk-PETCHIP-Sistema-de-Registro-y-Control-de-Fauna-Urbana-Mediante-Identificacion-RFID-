<?php
include("../config/conexion.php");
include("../includes/funciones.php");

$filtro = $_GET["tipo"] ?? "";
$sql = "SELECT * FROM reportes_extravio WHERE estado='Activo'";
if ($filtro === "Perdido" || $filtro === "Encontrado") {
    $sql .= " AND tipo='" . limpiar_dato($conexion, $filtro) . "'";
}
$sql .= " ORDER BY fecha_registro DESC";

$lista = [];
$res = mysqli_query($conexion, $sql);
while ($r = mysqli_fetch_assoc($res)) $lista[] = $r;

$puntos = array_values(array_map(function ($r) {
    return [
        "lat" => (float) $r["lat"],
        "lng" => (float) $r["lng"],
        "titulo" => $r["nombre_animal"] ?: "Animal sin nombre",
        "tipo" => $r["tipo"],
    ];
}, array_filter($lista, function ($r) { return $r["lat"] && $r["lng"]; })));

$titulo_pagina = "Mascotas perdidas y encontradas";
include("../includes/header_publico.php");
?>

<div class="pc-card animar mb-3">
    <div class="pc-card-body">
        <h4 class="mb-1"><i class="bi bi-search-heart me-2 text-success"></i>Perdidos y encontrados</h4>
        <p class="text-muted mb-3">Reportes activos de la comunidad. Si reconoces a un animal, comunícate directamente al contacto indicado en su tarjeta.</p>
        <div class="btn-group mb-3">
            <a href="perdidos.php" class="btn btn-pc btn-pc-outline btn-sm <?php echo $filtro === "" ? "active" : ""; ?>">Todos</a>
            <a href="perdidos.php?tipo=Perdido" class="btn btn-pc btn-pc-outline btn-sm <?php echo $filtro === "Perdido" ? "active" : ""; ?>">Perdidos</a>
            <a href="perdidos.php?tipo=Encontrado" class="btn btn-pc btn-pc-outline btn-sm <?php echo $filtro === "Encontrado" ? "active" : ""; ?>">Encontrados</a>
        </div>
        <div id="mapaPerdidos" style="height:280px;border-radius:14px;overflow:hidden;"></div>
    </div>
</div>

<div class="row g-3">
<?php if (empty($lista)): ?>
    <div class="col-12"><div class="pc-card animar"><div class="pc-card-body text-center text-muted py-4">No hay reportes activos por el momento.</div></div></div>
<?php endif; ?>
<?php foreach ($lista as $r): ?>
    <div class="col-md-6">
        <div class="pc-card h-100 animar">
            <div class="pc-card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="badge rounded-pill <?php echo $r["tipo"] == "Perdido" ? "bg-danger-subtle text-danger" : "bg-info-subtle text-info"; ?> px-3 py-2"><?php echo htmlspecialchars($r["tipo"]); ?></span>
                    <?php if (!empty($r["recompensa"])): ?><span class="badge rounded-pill bg-warning-subtle text-warning px-3 py-2">Recompensa</span><?php endif; ?>
                </div>
                <?php if ($r["foto"]): ?>
                <img src="../uploads/perros/<?php echo htmlspecialchars($r["foto"]); ?>" class="rounded-3 mb-2" style="width:100%;height:170px;object-fit:cover;">
                <?php endif; ?>
                <h6 class="mb-1"><?php echo htmlspecialchars($r["nombre_animal"] ?: "Animal sin nombre"); ?></h6>
                <p class="small text-muted mb-1"><?php echo htmlspecialchars($r["especie"] ?: ""); ?></p>
                <p class="small mb-2"><?php echo htmlspecialchars($r["descripcion"]); ?></p>
                <p class="small text-muted mb-1"><i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($r["lugar"]); ?></p>
                <p class="small text-muted mb-2"><i class="bi bi-calendar3 me-1"></i><?php echo date("d/m/Y", strtotime($r["fecha"])); ?></p>
                <?php if (!empty($r["recompensa"])): ?>
                <p class="small mb-2"><i class="bi bi-cash-coin me-1"></i><?php echo htmlspecialchars($r["recompensa"]); ?></p>
                <?php endif; ?>
                <p class="small mb-0"><i class="bi bi-telephone me-1"></i><strong><?php echo htmlspecialchars($r["contacto"]); ?></strong></p>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>

<div class="text-center mt-3">
    <a href="../login.php" class="text-white-50 small"><i class="bi bi-box-arrow-in-right me-1"></i>Acceso para personal autorizado</a>
</div>

<script>
const puntosMapa = <?php echo json_encode($puntos); ?>;
window.addEventListener('load', function () {
    if (!document.getElementById('mapaPerdidos')) return;
    const mapa = L.map('mapaPerdidos').setView([18.9667, -98.7994], 12); // Ozumba, Edo. Méx.
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(mapa);
    if (puntosMapa.length) {
        const bounds = [];
        puntosMapa.forEach(function (p) {
            L.marker([p.lat, p.lng]).addTo(mapa).bindPopup('<strong>' + p.titulo + '</strong><br>' + p.tipo);
            bounds.push([p.lat, p.lng]);
        });
        mapa.fitBounds(bounds, { maxZoom: 14 });
    }
});
</script>

<?php include("../includes/footer_publico.php"); ?>
