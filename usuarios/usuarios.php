<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("gestionar_usuarios", "../");

$mensaje = ""; $tipo_mensaje = "success";

// Crear usuario
if (isset($_POST["crear"])) {
    $usuario_nuevo = limpiar_dato($conexion, $_POST["usuario"]);
    $nombre_completo = limpiar_dato($conexion, $_POST["nombre_completo"]);
    $rol_nuevo = limpiar_dato($conexion, $_POST["rol"]);
    $password_nueva = $_POST["contrasena"] ?? "";

    $roles_validos = ["administrador", "veterinario", "autoridad"];
    if (!in_array($rol_nuevo, $roles_validos, true)) {
        $rol_nuevo = "autoridad";
    }

    if ($usuario_nuevo === "" || strlen($password_nueva) < 6) {
        $tipo_mensaje = "danger";
        $mensaje = "El usuario es obligatorio y la contraseña debe tener al menos 6 caracteres.";
    } else {
        $existe = mysqli_query($conexion, "SELECT id_usuario FROM usuarios WHERE usuario='$usuario_nuevo'");
        if ($existe && mysqli_num_rows($existe) > 0) {
            $tipo_mensaje = "danger";
            $mensaje = "Ese nombre de usuario ya existe.";
        } else {
            $hash = password_hash($password_nueva, PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (usuario, contrasena, nombre_completo, rol, activo)
                    VALUES ('$usuario_nuevo', '" . mysqli_real_escape_string($conexion, $hash) . "', '$nombre_completo', '$rol_nuevo', 1)";
            if (mysqli_query($conexion, $sql)) {
                registrar_bitacora($conexion, "Creó al usuario \"$usuario_nuevo\" ($rol_nuevo)", "Usuarios");
                $mensaje = "Usuario creado correctamente.";
            } else {
                $tipo_mensaje = "danger";
                $mensaje = "Error: " . mysqli_error($conexion);
            }
        }
    }
}

// Activar / desactivar cuenta
if (isset($_GET["cambiar_estado"])) {
    $id = (int) $_GET["cambiar_estado"];
    if ($id === (int) $_SESSION["id_usuario"]) {
        $tipo_mensaje = "danger";
        $mensaje = "No puedes desactivar tu propia cuenta.";
    } else {
        $fila_u = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT usuario, activo FROM usuarios WHERE id_usuario=$id"));
        if ($fila_u) {
            $nuevo_estado = $fila_u["activo"] ? 0 : 1;
            mysqli_query($conexion, "UPDATE usuarios SET activo=$nuevo_estado WHERE id_usuario=$id");
            $accion = $nuevo_estado ? "activó" : "desactivó";
            registrar_bitacora($conexion, "Se $accion al usuario \"{$fila_u['usuario']}\"", "Usuarios");
            $mensaje = "El usuario fue " . ($nuevo_estado ? "activado" : "desactivado") . " correctamente.";
        }
    }
}

$resultado = mysqli_query($conexion, "SELECT * FROM usuarios ORDER BY id_usuario ASC");

$raiz = "../";
$pagina_activa = "usuarios";
$titulo_pagina = "Gestión de usuarios";
include("../includes/header.php");
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 animar gap-2">
    <div>
        <h4 class="mb-1"><i class="bi bi-person-gear me-2 text-success"></i>Gestión de usuarios</h4>
        <p class="text-muted mb-0">Administra cuentas, roles y accesos al sistema</p>
    </div>
    <button type="button" class="btn btn-pc btn-pc-verde" data-bs-toggle="modal" data-bs-target="#modalCrearUsuario">
        <i class="bi bi-person-plus-fill me-1"></i>Nuevo usuario
    </button>
</div>

<?php if ($mensaje): ?>
<div class="alert alert-<?php echo $tipo_mensaje; ?> pc-alert alert-auto animar"><?php echo htmlspecialchars($mensaje); ?></div>
<?php endif; ?>

<div class="pc-tabla-wrap animar">
<table class="pc-tabla">
<thead>
<tr>
    <th>Usuario</th>
    <th>Nombre completo</th>
    <th>Rol</th>
    <th>Estado</th>
    <th>Acciones</th>
</tr>
</thead>
<tbody>
<?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
<tr>
    <td class="fw-semibold"><?php echo htmlspecialchars($fila["usuario"]); ?></td>
    <td><?php echo htmlspecialchars($fila["nombre_completo"] ?: "—"); ?></td>
    <td><span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2"><?php echo htmlspecialchars(nombre_rol_legible($fila["rol"])); ?></span></td>
    <td>
        <?php if ((int) $fila["activo"] === 1): ?>
            <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">Activo</span>
        <?php else: ?>
            <span class="badge rounded-pill bg-secondary-subtle text-secondary px-3 py-2">Desactivado</span>
        <?php endif; ?>
    </td>
    <td class="text-nowrap">
        <a href="editar_usuario.php?id=<?php echo $fila['id_usuario']; ?>" class="btn btn-sm btn-pc btn-pc-outline" title="Editar / cambiar rol / restablecer contraseña"><i class="bi bi-pencil"></i></a>
        <?php if ((int) $fila["id_usuario"] !== (int) $_SESSION["id_usuario"]): ?>
        <a href="?cambiar_estado=<?php echo $fila['id_usuario']; ?>" class="btn btn-sm btn-pc <?php echo $fila['activo'] ? 'btn-pc-rojo' : 'btn-pc-verde'; ?>" title="<?php echo $fila['activo'] ? 'Desactivar' : 'Activar'; ?>" onclick="return confirm('¿Confirmas este cambio de estado?')">
            <i class="bi <?php echo $fila['activo'] ? 'bi-slash-circle' : 'bi-check-circle'; ?>"></i>
        </a>
        <?php else: ?>
        <span class="badge bg-light text-muted">Tu cuenta</span>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

<!-- MODAL CREAR USUARIO -->
<div class="modal fade" id="modalCrearUsuario" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content necesita-validacion" novalidate>
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Nuevo usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body row g-3">
        <div class="col-12">
            <label class="form-label">Nombre completo</label>
            <input type="text" name="nombre_completo" class="form-control">
        </div>
        <div class="col-12">
            <label class="form-label">Usuario *</label>
            <input type="text" name="usuario" class="form-control" required>
        </div>
        <div class="col-12">
            <label class="form-label">Contraseña *</label>
            <input type="password" name="contrasena" class="form-control" minlength="6" required>
            <div class="form-text">Mínimo 6 caracteres.</div>
        </div>
        <div class="col-12">
            <label class="form-label">Rol *</label>
            <select name="rol" class="form-select" required>
                <option value="administrador">Administrador</option>
                <option value="veterinario">Veterinario</option>
                <option value="autoridad" selected>Encargado</option>
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-pc btn-pc-outline" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" name="crear" class="btn btn-pc btn-pc-verde"><i class="bi bi-save me-1"></i>Crear usuario</button>
      </div>
    </form>
  </div>
</div>

<?php include("../includes/footer.php"); ?>
