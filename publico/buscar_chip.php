<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
include("../config/conexion.php");
include("../includes/funciones.php");

$codigo = limpiar_dato($conexion, $_GET["codigo_tag"] ?? "");
$no_encontrado = false;

if ($codigo !== "") {
    $tag = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM tags_rfid WHERE codigo_tag='$codigo'"));
    if ($tag) {
        $token = obtener_token_publico($conexion, (int) $tag["id_animal"]);
        header("Location: ficha.php?t=" . urlencode($token));
        exit();
    }
    $no_encontrado = true;
}

$titulo_pagina = "Buscar por chip";
include("../includes/header_publico.php");
?>

<div class="pc-card animar">
    <div class="pc-card-body text-center py-5">
        <i class="bi bi-broadcast" style="font-size:3rem; color:var(--pc-primario);"></i>
        <h4 class="mt-3 mb-2">Encontré una mascota</h4>
        <p class="text-muted mb-4">Si la mascota tiene un chip de identificación, ingresa o escanea el código para ver su ficha y avisar al dueño.</p>

        <?php if ($no_encontrado): ?>
        <div class="alert alert-danger pc-alert d-inline-block text-start animar">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>No encontramos ninguna mascota con ese código de chip.
        </div>
        <?php endif; ?>

        <form method="GET" class="d-flex gap-2 justify-content-center flex-wrap">
            <input type="text" name="codigo_tag" class="form-control" style="max-width:320px" placeholder="Ej. RFID-TEMP-001" autofocus required value="<?php echo htmlspecialchars($codigo); ?>">
            <button class="btn btn-pc btn-pc-verde"><i class="bi bi-search me-1"></i>Buscar</button>
        </form>

        <hr class="my-4">
        <p class="small text-muted mb-2">¿No tiene chip o no conoces el código?</p>
        <a href="perdidos.php" class="btn btn-pc btn-pc-outline btn-sm"><i class="bi bi-search-heart me-1"></i>Ver reportes de perdidos y encontrados</a>
    </div>
</div>

<?php include("../includes/footer_publico.php"); ?>
