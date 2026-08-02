<?php
session_start();
include("config/conexion.php");
include("includes/funciones.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_SESSION["usuario"])) {
    header("Location: menu.php");
    exit();
}

$mensaje = "";

$accesos_validos = [
    "administrador" => ["Administrador", "bi-shield-lock-fill"],
    "encargado"     => ["Encargado", "bi-person-badge-fill"],
    "veterinario"   => ["Veterinario", "bi-heart-pulse-fill"],
];
$acceso = $_GET['acceso'] ?? "";
$acceso_info = $accesos_validos[$acceso] ?? null;

if (isset($_GET['bloqueado'])) {
    $mensaje = "Tu cuenta fue desactivada. Contacta al administrador.";
}

if (isset($_POST['ingresar'])) {

    $usuario_post = limpiar_dato($conexion, $_POST['usuario']);
    $contrasena_post = $_POST['contrasena'] ?? "";

    $consulta = "SELECT * FROM usuarios WHERE usuario='$usuario_post' LIMIT 1";
    $resultado = mysqli_query($conexion, $consulta);

    $credenciales_validas = false;
    $fila = null;

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $fila = mysqli_fetch_assoc($resultado);
        $hash_guardado = $fila["contrasena"];

        // Contraseñas nuevas: verificadas con password_hash().
        $es_hash_valido = password_verify($contrasena_post, $hash_guardado);

        // Compatibilidad con la base de datos anterior (contraseña en texto plano).
        $es_texto_plano_valido = !$es_hash_valido
            && strlen($hash_guardado) < 60
            && hash_equals($hash_guardado, $contrasena_post);

        if ($es_hash_valido || $es_texto_plano_valido) {
            $credenciales_validas = true;

            // Si la contraseña aun estaba en texto plano o el algoritmo cambio,
            // se re-encripta automáticamente sin afectar al usuario.
            if ($es_texto_plano_valido || password_needs_rehash($hash_guardado, PASSWORD_DEFAULT)) {
                $nuevo_hash = password_hash($contrasena_post, PASSWORD_DEFAULT);
                $id_fila = (int) $fila["id_usuario"];
                mysqli_query($conexion, "UPDATE usuarios SET contrasena='" . mysqli_real_escape_string($conexion, $nuevo_hash) . "' WHERE id_usuario=$id_fila");
            }
        }
    }

    if ($credenciales_validas) {

        if ((int) ($fila["activo"] ?? 1) === 0) {
            $mensaje = "Tu cuenta está desactivada. Contacta al administrador.";
        } else {
            $_SESSION["usuario"] = $fila["usuario"];
            $_SESSION["id_usuario"] = $fila["id_usuario"];
            $_SESSION["rol"] = $fila["rol"] ?? "operador";
            $_SESSION["nombre_completo"] = $fila["nombre_completo"] ?? $fila["usuario"];

            registrar_bitacora($conexion, "Inicio de sesión", "Autenticación");

            header("Location: menu.php");
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
<title>Iniciar sesión · PetChip</title>
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
            <p class="mb-0" style="opacity:.9">Identificación inteligente y control de fauna urbana para el municipio de Ozumba, Estado de México.</p>
            <hr style="border-color:rgba(255,255,255,.25); margin:22px 0;">
            <ul class="list-unstyled small mb-0" style="opacity:.85; line-height:2;">
                <li><i class="bi bi-check-circle me-2"></i>Registro con Tag RFID</li>
                <li><i class="bi bi-check-circle me-2"></i>Control de esterilización y vacunas</li>
                <li><i class="bi bi-check-circle me-2"></i>Reportes municipales</li>
            </ul>
        </div>
        <div class="col-md-7 login-form">
            <a href="portal.php" class="text-decoration-none small d-inline-flex align-items-center gap-1 mb-3" style="opacity:.85;"><i class="bi bi-arrow-left"></i> Volver a elegir acceso</a>

            <?php if ($acceso_info): ?>
                <span class="badge rounded-pill mb-2 px-3 py-2" style="background:rgba(255,255,255,.18);"><i class="bi <?php echo $acceso_info[1]; ?> me-1"></i>Acceso: <?php echo $acceso_info[0]; ?></span>
            <?php endif; ?>

            <h3 class="mb-1">Bienvenido de nuevo</h3>
            <p class="text-muted mb-4">Ingresa tus credenciales para continuar</p>

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
                        <input type="text" name="usuario" class="form-control" placeholder="admin" required>
                        <div class="invalid-feedback">Escribe tu usuario.</div>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Contraseña</label>
                    <div class="input-group pc-input-icon">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="contrasena" id="passwordLogin" class="form-control" placeholder="••••••••" required>
                        <button type="button" class="input-group-text pc-toggle-pass" data-target="passwordLogin"><i class="bi bi-eye"></i></button>
                        <div class="invalid-feedback">Escribe tu contraseña.</div>
                    </div>
                </div>
                <button type="submit" name="ingresar" class="btn btn-pc-primario w-100">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar sesión
                </button>
            </form>

            <p class="text-center text-muted small mt-4 mb-0">Universidad Politécnica de Atlautla · Proyecto Integrador</p>
            <p class="text-center small mt-2 mb-0"><a href="login_dueno.php"><i class="bi bi-house-heart me-1"></i>¿Eres dueño de una mascota? Entra a tu portal</a></p>
            <p class="text-center small mt-2 mb-0"><a href="publico/perdidos.php" target="_blank"><i class="bi bi-search-heart me-1"></i>¿Perdiste o encontraste una mascota? Consulta aquí</a></p>
            <p class="text-center small mt-2 mb-0"><a href="publico/buscar_chip.php" target="_blank"><i class="bi bi-broadcast me-1"></i>¿Encontraste una mascota con chip? Búscala aquí</a></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
