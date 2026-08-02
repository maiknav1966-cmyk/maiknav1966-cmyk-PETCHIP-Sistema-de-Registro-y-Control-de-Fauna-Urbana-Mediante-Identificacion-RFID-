<?php
session_start();
include("config/conexion.php");
include("includes/funciones.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_SESSION["dueno_id"])) {
    header("Location: portal/mis_mascotas.php");
    exit();
}

$mensaje = "";

// Verifica que la migración del portal de dueños (Parte 6) ya esté aplicada.
// Sin esto, la consulta de abajo falla en silencio y el login parece "no hacer nada".
$columna_portal = mysqli_query($conexion, "SHOW COLUMNS FROM duenos LIKE 'usuario_portal'");
if (!$columna_portal || mysqli_num_rows($columna_portal) === 0) {
    $mensaje = "El portal del dueño aún no está configurado en esta instalación. Un administrador debe ejecutar el script database/actualizar_parte6.sql (o actualizar_todo.sql) en la base de datos.";
}

if (isset($_GET['bloqueado'])) {
    $mensaje = "Tu acceso al portal fue desactivado. Contacta al municipio para reactivarlo.";
}

if (isset($_POST['ingresar']) && $mensaje === "") {
    $usuario_post = limpiar_dato($conexion, $_POST['usuario']);
    $contrasena_post = $_POST['contrasena'] ?? "";

    $resultado = mysqli_query($conexion, "SELECT * FROM duenos WHERE usuario_portal='$usuario_post' LIMIT 1");
    $credenciales_validas = false;
    $fila = null;

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $fila = mysqli_fetch_assoc($resultado);
        if (!empty($fila["contrasena_portal"]) && password_verify($contrasena_post, $fila["contrasena_portal"])) {
            $credenciales_validas = true;
        }
    }

    if ($credenciales_validas) {
        if ((int) ($fila["portal_activo"] ?? 1) === 0) {
            $mensaje = "Tu acceso al portal está desactivado. Contacta al municipio.";
        } else {
            $_SESSION["dueno_id"] = $fila["id_dueno"];
            $_SESSION["dueno_nombre"] = $fila["nombre"];
            registrar_bitacora($conexion, "Inicio de sesión en el portal del dueño \"{$fila['nombre']}\"", "Portal dueños");
            header("Location: portal/mis_mascotas.php");
            exit();
        }
    } else {
        $mensaje = "Usuario o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Portal del dueño · PetChip</title>
<link rel="icon" href="img/logo.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="login-wrapper">
    <div class="login-card row g-0 animar">
        <div class="col-md-5 login-brand">
            <img src="img/logo_completo.png" alt="PetChip" class="logo-oficial">
            <p class="mb-0" style="opacity:.9">Portal del dueño: consulta tus mascotas, su código QR, notificaciones y el historial de ubicaciones.</p>
            <hr style="border-color:rgba(255,255,255,.25); margin:22px 0;">
            <ul class="list-unstyled small mb-0" style="opacity:.85; line-height:2;">
                <li><i class="bi bi-check-circle me-2"></i>Tus mascotas y su ficha pública</li>
                <li><i class="bi bi-check-circle me-2"></i>Notificaciones de escaneo del QR</li>
                <li><i class="bi bi-check-circle me-2"></i>Historial de ubicaciones</li>
            </ul>
        </div>
        <div class="col-md-7 login-form">
            <a href="portal.php" class="text-decoration-none small d-inline-flex align-items-center gap-1 mb-3" style="opacity:.85;"><i class="bi bi-arrow-left"></i> Volver a elegir acceso</a>
            <h3 class="mb-1">Portal del dueño</h3>
            <p class="text-muted mb-4">Ingresa con el usuario que te dio el municipio</p>

            <?php if ($mensaje): ?>
                <div class="alert alert-danger pc-alert d-flex align-items-center gap-2 pc-alert-shake">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="necesita-validacion" novalidate>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Usuario</label>
                    <div class="input-group pc-input-icon">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" name="usuario" class="form-control" placeholder="usuario del portal" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Contraseña</label>
                    <div class="input-group pc-input-icon">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="contrasena" id="passwordDueno" class="form-control" placeholder="••••••••" required>
                        <button type="button" class="input-group-text pc-toggle-pass" data-target="passwordDueno"><i class="bi bi-eye"></i></button>
                    </div>
                </div>
                <button type="submit" name="ingresar" class="btn btn-pc-primario w-100">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Entrar al portal
                </button>
            </form>

            <p class="text-center small mt-4 mb-0"><a href="login.php"><i class="bi bi-arrow-left me-1"></i>Soy personal del municipio</a></p>
            <p class="text-center small mt-2 mb-0"><a href="publico/perdidos.php" target="_blank"><i class="bi bi-search-heart me-1"></i>¿Perdiste o encontraste una mascota?</a></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
