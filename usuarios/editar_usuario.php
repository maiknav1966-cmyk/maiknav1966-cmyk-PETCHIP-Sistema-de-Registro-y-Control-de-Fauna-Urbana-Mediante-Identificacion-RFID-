<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("gestionar_usuarios", "../");

$id = (int) ($_GET["id"] ?? 0);
$fila = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM usuarios WHERE id_usuario=$id"));

if (!$fila) {
    header("Location: usuarios.php");
    exit();
}

$mensaje = ""; $tipo_mensaje = "success";
$es_uno_mismo = ($id === (int) $_SESSION["id_usuario"]);

if (isset($_POST["actualizar"])) {
    $nombre_completo = limpiar_dato($conexion, $_POST["nombre_completo"]);
    $rol_nuevo = limpiar_dato($conexion, $_POST["rol"]);
    $password_nueva = $_POST["contrasena"] ?? "";

    $roles_validos = ["administrador", "veterinario", "autoridad"];
    if (!in_array($rol_nuevo, $roles_validos, true)) {
        $rol_nuevo = $fila["rol"];
    }
    // Un administrador no puede quitarse su propio rol de administrador para evitar quedarse sin acceso.
    if ($es_uno_mismo) {
        $rol_nuevo = "administrador";
    }

    $sql = "UPDATE usuarios SET nombre_completo='$nombre_completo', rol='$rol_nuevo' WHERE id_usuario=$id";
    $ok = mysqli_query($conexion, $sql);

    if ($ok && $password_nueva !== "") {
        if (strlen($password_nueva) < 6) {
            $tipo_mensaje = "danger";
            $mensaje = "La contraseña debe tener al menos 6 caracteres. El resto de los datos sí se actualizó.";
        } else {
            $hash = password_hash($password_nueva, PASSWORD_DEFAULT);
            mysqli_query($conexion, "UPDATE usuarios SET contrasena='" . mysqli_real_escape_string($conexion, $hash) . "' WHERE id_usuario=$id");
            registrar_bitacora($conexion, "Restableció la contraseña de \"{$fila['usuario']}\"", "Usuarios");
        }
    }

    if ($ok && $mensaje === "") {
        registrar_bitacora($conexion, "Editó al usuario \"{$fila['usuario']}\" (rol: $rol_nuevo)", "Usuarios");
        $mensaje = "Usuario actualizado correctamente.";
        $fila = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM usuarios WHERE id_usuario=$id"));
    } elseif (!$ok) {
        $tipo_mensaje = "danger";
        $mensaje = "Error: " . mysqli_error($conexion);
    }
}

$raiz = "../";
$pagina_activa = "usuarios";
$titulo_pagina = "Editar usuario";
include("../includes/header.php");
?>

<div class="d-flex justify-content-between align-items-center mb-4 animar">
    <div>
        <h4 class="mb-1"><i class="bi bi-person-gear me-2 text-success"></i>Editar usuario: <?php echo htmlspecialchars($fila["usuario"]); ?></h4>
        <p class="text-muted mb-0">Cambia el rol, los datos o restablece la contraseña</p>
    </div>
    <a href="usuarios.php" class="btn btn-pc btn-pc-outline"><i class="bi bi-list-ul me-1"></i>Ver lista</a>
</div>

<?php if ($mensaje): ?>
<div class="alert alert-<?php echo $tipo_mensaje; ?> pc-alert alert-auto animar"><?php echo htmlspecialchars($mensaje); ?></div>
<?php endif; ?>

<div class="pc-card animar">
    <div class="pc-card-body">
        <form method="POST" class="pc-form row g-3 necesita-validacion" novalidate>

            <div class="col-md-6">
                <label class="form-label">Usuario</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($fila['usuario']); ?>" disabled>
                <div class="form-text">El nombre de usuario no puede cambiarse.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nombre completo</label>
                <input type="text" name="nombre_completo" class="form-control" value="<?php echo htmlspecialchars($fila['nombre_completo'] ?? ''); ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label">Rol *</label>
                <select name="rol" class="form-select" required <?php echo $es_uno_mismo ? 'disabled' : ''; ?>>
                    <option value="administrador" <?php echo $fila['rol']==='administrador'?'selected':''; ?>>Administrador</option>
                    <option value="veterinario" <?php echo $fila['rol']==='veterinario'?'selected':''; ?>>Veterinario</option>
                    <option value="autoridad" <?php echo (in_array($fila['rol'], ['autoridad','operador'], true))?'selected':''; ?>>Encargado</option>
                </select>
                <?php if ($es_uno_mismo): ?>
                    <div class="form-text">No puedes cambiar tu propio rol de administrador.</div>
                    <input type="hidden" name="rol" value="administrador">
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Restablecer contraseña</label>
                <input type="password" name="contrasena" class="form-control" minlength="6" placeholder="Dejar vacío para no cambiarla">
            </div>

            <div class="col-12 d-flex gap-2 mt-3">
                <button type="submit" name="actualizar" class="btn btn-pc btn-pc-verde"><i class="bi bi-save me-1"></i>Guardar cambios</button>
                <a href="usuarios.php" class="btn btn-pc btn-pc-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
