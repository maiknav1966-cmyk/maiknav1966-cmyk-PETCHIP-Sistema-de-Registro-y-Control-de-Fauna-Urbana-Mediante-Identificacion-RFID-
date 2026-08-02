<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("ver_veterinarios", "../");

$mensaje = ""; $tipo_mensaje = "success";
$puede_gestionar = tiene_permiso("gestionar_veterinarios");

// Crear veterinario
if (isset($_POST["crear"])) {
    requerir_permiso("gestionar_veterinarios", "../");

    $nombre = limpiar_dato($conexion, $_POST["nombre"]);
    $cedula = limpiar_dato($conexion, $_POST["cedula_profesional"]);
    $especialidad_sel = $_POST["especialidad"] ?? "";
    $especialidad_valor = ($especialidad_sel === "Otra") ? trim($_POST["especialidad_otra"] ?? "") : $especialidad_sel;
    $especialidad = limpiar_dato($conexion, $especialidad_valor);
    $telefono = limpiar_dato($conexion, $_POST["telefono"]);
    $correo = limpiar_dato($conexion, $_POST["correo"]);
    $id_usuario = $_POST["id_usuario"] !== "" ? (int) $_POST["id_usuario"] : "NULL";

    if ($nombre === "") {
        $tipo_mensaje = "danger";
        $mensaje = "El nombre del veterinario es obligatorio.";
    } else {
        $sql = "INSERT INTO veterinarios(nombre, cedula_profesional, especialidad, telefono, correo, id_usuario, activo)
                VALUES('$nombre', '$cedula', '$especialidad', '$telefono', '$correo', $id_usuario, 1)";
        if (mysqli_query($conexion, $sql)) {
            registrar_bitacora($conexion, "Registró al veterinario \"$nombre\"", "Veterinarios");
            $mensaje = "Veterinario registrado correctamente.";
        } else {
            $tipo_mensaje = "danger";
            $mensaje = "Error: " . mysqli_error($conexion);
        }
    }
}

// Activar / desactivar
if (isset($_GET["cambiar_estado"]) && $puede_gestionar) {
    $id = (int) $_GET["cambiar_estado"];
    $fila_v = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT nombre, activo FROM veterinarios WHERE id_veterinario=$id"));
    if ($fila_v) {
        $nuevo_estado = $fila_v["activo"] ? 0 : 1;
        mysqli_query($conexion, "UPDATE veterinarios SET activo=$nuevo_estado WHERE id_veterinario=$id");
        $accion = $nuevo_estado ? "activó" : "desactivó";
        registrar_bitacora($conexion, "Se $accion al veterinario \"{$fila_v['nombre']}\"", "Veterinarios");
        $mensaje = "El veterinario fue " . ($nuevo_estado ? "activado" : "desactivado") . " correctamente.";
    }
}

$resultado = mysqli_query($conexion, "SELECT v.*, u.usuario AS usuario_login FROM veterinarios v
    LEFT JOIN usuarios u ON u.id_usuario = v.id_usuario ORDER BY v.nombre ASC");

$usuarios_disponibles = mysqli_query($conexion, "SELECT id_usuario, usuario, nombre_completo FROM usuarios
    WHERE rol='veterinario' AND id_usuario NOT IN (SELECT IFNULL(id_usuario,0) FROM veterinarios WHERE id_usuario IS NOT NULL) ORDER BY usuario");

$raiz = "../";
$pagina_activa = "veterinarios";
$titulo_pagina = "Veterinarios";
include("../includes/header.php");
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 animar gap-2">
    <div>
        <h4 class="mb-1"><i class="bi bi-heart-pulse-fill me-2 text-success"></i>Gestión de veterinarios</h4>
        <p class="text-muted mb-0">Catálogo de médicos veterinarios que atienden a los animales registrados</p>
    </div>
    <?php if ($puede_gestionar): ?>
    <button type="button" class="btn btn-pc btn-pc-verde" data-bs-toggle="modal" data-bs-target="#modalCrearVet">
        <i class="bi bi-person-plus-fill me-1"></i>Nuevo veterinario
    </button>
    <?php endif; ?>
</div>

<?php if ($mensaje): ?>
<div class="alert alert-<?php echo $tipo_mensaje; ?> pc-alert alert-auto animar"><?php echo htmlspecialchars($mensaje); ?></div>
<?php endif; ?>

<div class="pc-tabla-wrap animar">
<table class="pc-tabla">
<thead>
<tr>
    <th>Nombre</th>
    <th>Cédula profesional</th>
    <th>Especialidad</th>
    <th>Contacto</th>
    <th>Cuenta de acceso</th>
    <th>Estado</th>
    <?php if ($puede_gestionar): ?><th>Acciones</th><?php endif; ?>
</tr>
</thead>
<tbody>
<?php if (mysqli_num_rows($resultado) === 0): ?>
<tr><td colspan="7" class="text-center text-muted py-4">Aún no hay veterinarios registrados.</td></tr>
<?php endif; ?>
<?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
<tr>
    <td class="fw-semibold"><?php echo htmlspecialchars($fila["nombre"]); ?></td>
    <td><?php echo htmlspecialchars($fila["cedula_profesional"] ?: "—"); ?></td>
    <td><?php echo htmlspecialchars($fila["especialidad"] ?: "General"); ?></td>
    <td class="small">
        <?php echo htmlspecialchars($fila["telefono"] ?: "—"); ?><br>
        <span class="text-muted"><?php echo htmlspecialchars($fila["correo"] ?: ""); ?></span>
    </td>
    <td>
        <?php if ($fila["usuario_login"]): ?>
            <span class="badge rounded-pill bg-info-subtle text-info px-3 py-2"><i class="bi bi-person-check-fill me-1"></i><?php echo htmlspecialchars($fila["usuario_login"]); ?></span>
        <?php else: ?>
            <span class="badge rounded-pill bg-light text-muted px-3 py-2">Sin acceso al sistema</span>
        <?php endif; ?>
    </td>
    <td>
        <?php if ((int) $fila["activo"] === 1): ?>
            <span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">Activo</span>
        <?php else: ?>
            <span class="badge rounded-pill bg-secondary-subtle text-secondary px-3 py-2">Inactivo</span>
        <?php endif; ?>
    </td>
    <?php if ($puede_gestionar): ?>
    <td class="text-nowrap">
        <a href="editar_veterinario.php?id=<?php echo $fila['id_veterinario']; ?>" class="btn btn-sm btn-pc btn-pc-outline" title="Editar"><i class="bi bi-pencil"></i></a>
        <a href="?cambiar_estado=<?php echo $fila['id_veterinario']; ?>" class="btn btn-sm btn-pc <?php echo $fila['activo'] ? 'btn-pc-rojo' : 'btn-pc-verde'; ?>" title="<?php echo $fila['activo'] ? 'Desactivar' : 'Activar'; ?>" onclick="return confirm('¿Confirmas este cambio de estado?')">
            <i class="bi <?php echo $fila['activo'] ? 'bi-slash-circle' : 'bi-check-circle'; ?>"></i>
        </a>
    </td>
    <?php endif; ?>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

<?php if ($puede_gestionar): ?>
<!-- MODAL CREAR VETERINARIO -->
<div class="modal fade" id="modalCrearVet" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content pc-form necesita-validacion" novalidate>
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-heart-pulse-fill me-2"></i>Nuevo veterinario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body row g-3">
        <div class="col-12">
            <label class="form-label">Nombre completo *</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Cédula profesional</label>
            <input type="text" name="cedula_profesional" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Especialidad</label>
            <select name="especialidad" class="form-select pc-select-otra" data-otro-target="especialidad_otra_crear">
                <option value="">Sin especificar</option>
                <?php foreach (especialidades_veterinarias() as $esp): ?>
                <option value="<?php echo htmlspecialchars($esp); ?>"><?php echo htmlspecialchars($esp); ?></option>
                <?php endforeach; ?>
                <option value="Otra">Otra (especificar)</option>
            </select>
        </div>
        <div class="col-md-6 d-none" id="especialidad_otra_crear">
            <label class="form-label">¿Cuál especialidad?</label>
            <input type="text" name="especialidad_otra" class="form-control" placeholder="Escribe la especialidad">
        </div>
        <div class="col-md-6">
            <label class="form-label">Teléfono</label>
            <input type="text" name="telefono" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Correo</label>
            <input type="email" name="correo" class="form-control">
        </div>
        <div class="col-12">
            <label class="form-label">Vincular a una cuenta de usuario (opcional)</label>
            <select name="id_usuario" class="form-select">
                <option value="">Sin cuenta de acceso al sistema</option>
                <?php while ($u = mysqli_fetch_assoc($usuarios_disponibles)): ?>
                <option value="<?php echo $u['id_usuario']; ?>"><?php echo htmlspecialchars($u['usuario']); ?><?php echo $u['nombre_completo'] ? ' — '.htmlspecialchars($u['nombre_completo']) : ''; ?></option>
                <?php endwhile; ?>
            </select>
            <div class="form-text">Solo se listan cuentas con rol "Veterinario" que aún no estén vinculadas.</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-pc btn-pc-outline" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" name="crear" class="btn btn-pc btn-pc-verde"><i class="bi bi-save me-1"></i>Guardar</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<script>
document.querySelectorAll('.pc-select-otra').forEach(function (sel) {
    var destino = document.getElementById(sel.dataset.otroTarget);
    if (!destino) return;
    var actualizar = function () {
        destino.classList.toggle('d-none', sel.value !== 'Otra');
    };
    sel.addEventListener('change', actualizar);
    actualizar();
});
</script>

<?php include("../includes/footer.php"); ?>
