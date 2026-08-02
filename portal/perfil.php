<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion_dueno("../");

$id_dueno = (int) $_SESSION["dueno_id"];
$mensaje = ""; $tipo_mensaje = "success";

// Actualizar datos personales (el dueño NO puede editar su nombre completo
// para conservar la identidad del registro oficial; solo datos de contacto).
if (isset($_POST["actualizar_datos"])) {
    $telefono = limpiar_dato($conexion, $_POST["telefono"]);
    $correo = limpiar_dato($conexion, $_POST["correo"]);
    $direccion = limpiar_dato($conexion, $_POST["direccion"]);
    $colonia = limpiar_dato($conexion, $_POST["colonia"]);
    $codigo_postal = limpiar_dato($conexion, $_POST["codigo_postal"]);

    $foto_nueva = subir_foto("foto", "../uploads/duenos");
    $foto_sql = $foto_nueva ? ", foto='" . mysqli_real_escape_string($conexion, $foto_nueva) . "'" : "";

    mysqli_query($conexion, "UPDATE duenos SET telefono='$telefono', correo='$correo',
        direccion='$direccion', colonia='$colonia', codigo_postal='$codigo_postal' $foto_sql
        WHERE id_dueno=$id_dueno");
    registrar_bitacora($conexion, "Actualizó sus datos personales en el portal", "Portal dueños");
    $mensaje = "Tus datos se actualizaron correctamente.";
}

// Cambiar contraseña del portal
if (isset($_POST["cambiar_clave"])) {
    $actual = $_POST["actual"] ?? "";
    $nueva = $_POST["nueva"] ?? "";
    $confirmar = $_POST["confirmar"] ?? "";

    $fila_clave = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT contrasena_portal FROM duenos WHERE id_dueno=$id_dueno"));
    $actual_valida = $fila_clave && !empty($fila_clave["contrasena_portal"]) && password_verify($actual, $fila_clave["contrasena_portal"]);

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
        mysqli_query($conexion, "UPDATE duenos SET contrasena_portal='" . mysqli_real_escape_string($conexion, $hash) . "' WHERE id_dueno=$id_dueno");
        registrar_bitacora($conexion, "Cambió su contraseña del portal", "Portal dueños");
        $mensaje = "Tu contraseña se actualizó correctamente.";
    }
}

$dueno = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM duenos WHERE id_dueno=$id_dueno"));

$raiz = "../";
$pagina_activa = "perfil";
$titulo_pagina = "Mi perfil";
include("../includes/header_dueno.php");
?>

<div class="mb-4 animar">
    <h4 class="mb-1"><i class="bi bi-person-circle me-2 text-success"></i>Mi perfil</h4>
    <p class="text-muted mb-0">Actualiza tus datos de contacto y tu contraseña de acceso al portal</p>
</div>

<?php if ($mensaje): ?>
<div class="alert alert-<?php echo $tipo_mensaje; ?> pc-alert alert-auto animar"><?php echo htmlspecialchars($mensaje); ?></div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="pc-card animar">
            <div class="pc-card-header"><i class="bi bi-card-list me-2"></i>Datos personales</div>
            <div class="pc-card-body">
                <form method="POST" enctype="multipart/form-data" class="pc-form row g-3 necesita-validacion" novalidate>
                    <div class="col-12">
                        <label class="form-label">Nombre completo</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($dueno['nombre']); ?>" disabled>
                        <div class="form-text">Si tu nombre está mal escrito, solicita la corrección al municipio.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" value="<?php echo htmlspecialchars($dueno['telefono'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Correo</label>
                        <input type="email" name="correo" class="form-control" value="<?php echo htmlspecialchars($dueno['correo'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion" class="form-control" value="<?php echo htmlspecialchars($dueno['direccion'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Colonia</label>
                        <input type="text" name="colonia" class="form-control" value="<?php echo htmlspecialchars($dueno['colonia'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Código postal</label>
                        <input type="text" name="codigo_postal" class="form-control" value="<?php echo htmlspecialchars($dueno['codigo_postal'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Fotografía de perfil</label>
                        <input type="file" name="foto" accept="image/*" class="form-control">
                    </div>
                    <div class="col-12 mt-2">
                        <button type="submit" name="actualizar_datos" class="btn btn-pc btn-pc-verde"><i class="bi bi-save me-1"></i>Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="pc-card animar">
            <div class="pc-card-header"><i class="bi bi-key me-2"></i>Cambiar contraseña</div>
            <div class="pc-card-body">
                <form method="POST" class="pc-form row g-3 necesita-validacion" novalidate>
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
                        <button type="submit" name="cambiar_clave" class="btn btn-pc btn-pc-verde"><i class="bi bi-shield-check me-1"></i>Actualizar contraseña</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer_dueno.php"); ?>
