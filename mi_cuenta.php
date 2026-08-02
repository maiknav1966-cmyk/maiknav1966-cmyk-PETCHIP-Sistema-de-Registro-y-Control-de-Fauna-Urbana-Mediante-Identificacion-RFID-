<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("config/conexion.php");
include("includes/funciones.php");
requerir_sesion("");

$mensaje = ""; $tipo_mensaje = "success";

if (isset($_POST["cambiar"])) {
    $actual = $_POST["actual"] ?? "";
    $nueva = $_POST["nueva"] ?? "";
    $confirmar = $_POST["confirmar"] ?? "";

    $id = (int) $_SESSION["id_usuario"];
    $fila = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT contrasena FROM usuarios WHERE id_usuario=$id"));

    $actual_valida = $fila && (
        password_verify($actual, $fila["contrasena"]) ||
        (strlen($fila["contrasena"]) < 60 && hash_equals($fila["contrasena"], $actual))
    );

    if (!$actual_valida) {
        $tipo_mensaje = "danger";
        $mensaje = "Tu contraseña actual no es correcta.";
    } elseif (strlen($nueva) < 6) {
        $tipo_mensaje = "danger";
        $mensaje = "La nueva contraseña debe tener al menos 6 caracteres.";
    } elseif ($nueva !== $confirmar) {
        $tipo_mensaje = "danger";
        $mensaje = "La confirmación no coincide con la nueva contraseña.";
    } else {
        $hash = password_hash($nueva, PASSWORD_DEFAULT);
        mysqli_query($conexion, "UPDATE usuarios SET contrasena='" . mysqli_real_escape_string($conexion, $hash) . "' WHERE id_usuario=$id");
        registrar_bitacora($conexion, "Cambió su propia contraseña", "Usuarios");
        $mensaje = "Tu contraseña se actualizó correctamente.";
    }
}

$raiz = "";
$pagina_activa = "";
$titulo_pagina = "Mi cuenta";
include("includes/header.php");
?>

<div class="mb-4 animar">
    <h4 class="mb-1"><i class="bi bi-key me-2 text-success"></i>Mi cuenta</h4>
    <p class="text-muted mb-0">Cambia tu contraseña de acceso a PetChip</p>
</div>

<?php if ($mensaje): ?>
<div class="alert alert-<?php echo $tipo_mensaje; ?> pc-alert alert-auto animar"><?php echo htmlspecialchars($mensaje); ?></div>
<?php endif; ?>

<div class="pc-card animar" style="max-width:520px;">
    <div class="pc-card-body">
        <form method="POST" class="pc-form row g-3 necesita-validacion" novalidate>
            <div class="col-12">
                <label class="form-label">Usuario</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($_SESSION['usuario']); ?>" disabled>
            </div>
            <div class="col-12">
                <label class="form-label">Contraseña actual *</label>
                <input type="password" name="actual" class="form-control" required>
            </div>
            <div class="col-12">
                <label class="form-label">Nueva contraseña *</label>
                <input type="password" name="nueva" class="form-control" minlength="6" required>
            </div>
            <div class="col-12">
                <label class="form-label">Confirmar nueva contraseña *</label>
                <input type="password" name="confirmar" class="form-control" minlength="6" required>
            </div>
            <div class="col-12 mt-2">
                <button type="submit" name="cambiar" class="btn btn-pc btn-pc-verde"><i class="bi bi-save me-1"></i>Actualizar contraseña</button>
            </div>
        </form>
    </div>
</div>

<?php include("includes/footer.php"); ?>
