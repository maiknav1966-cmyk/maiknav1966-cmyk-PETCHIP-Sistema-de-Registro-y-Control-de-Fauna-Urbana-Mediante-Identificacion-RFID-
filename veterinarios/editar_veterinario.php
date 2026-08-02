<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("gestionar_veterinarios", "../");

$id = (int) ($_GET["id"] ?? 0);
$fila = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM veterinarios WHERE id_veterinario=$id"));

if (!$fila) {
    header("Location: veterinarios.php");
    exit();
}

$mensaje = ""; $tipo_mensaje = "success";

if (isset($_POST["actualizar"])) {
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
        $mensaje = "El nombre es obligatorio.";
    } else {
        $sql = "UPDATE veterinarios SET nombre='$nombre', cedula_profesional='$cedula', especialidad='$especialidad',
                telefono='$telefono', correo='$correo', id_usuario=$id_usuario WHERE id_veterinario=$id";
        if (mysqli_query($conexion, $sql)) {
            registrar_bitacora($conexion, "Editó al veterinario \"$nombre\"", "Veterinarios");
            $mensaje = "Veterinario actualizado correctamente.";
            $fila = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT * FROM veterinarios WHERE id_veterinario=$id"));
        } else {
            $tipo_mensaje = "danger";
            $mensaje = "Error: " . mysqli_error($conexion);
        }
    }
}

$usuarios_disponibles = mysqli_query($conexion, "SELECT id_usuario, usuario, nombre_completo FROM usuarios
    WHERE rol='veterinario' AND (id_usuario NOT IN (SELECT IFNULL(id_usuario,0) FROM veterinarios WHERE id_usuario IS NOT NULL) OR id_usuario = " . (int) ($fila["id_usuario"] ?? 0) . ")
    ORDER BY usuario");

$raiz = "../";
$pagina_activa = "veterinarios";
$titulo_pagina = "Editar veterinario";
include("../includes/header.php");
?>

<div class="d-flex justify-content-between align-items-center mb-4 animar">
    <div>
        <h4 class="mb-1"><i class="bi bi-heart-pulse-fill me-2 text-success"></i>Editar: <?php echo htmlspecialchars($fila["nombre"]); ?></h4>
        <p class="text-muted mb-0">Actualiza los datos del veterinario</p>
    </div>
    <a href="veterinarios.php" class="btn btn-pc btn-pc-outline"><i class="bi bi-list-ul me-1"></i>Ver lista</a>
</div>

<?php if ($mensaje): ?>
<div class="alert alert-<?php echo $tipo_mensaje; ?> pc-alert alert-auto animar"><?php echo htmlspecialchars($mensaje); ?></div>
<?php endif; ?>

<div class="pc-card animar">
    <div class="pc-card-body">
        <form method="POST" class="pc-form row g-3 necesita-validacion" novalidate>
            <div class="col-md-6">
                <label class="form-label">Nombre completo *</label>
                <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($fila['nombre']); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Cédula profesional</label>
                <input type="text" name="cedula_profesional" class="form-control" value="<?php echo htmlspecialchars($fila['cedula_profesional'] ?? ''); ?>">
            </div>
            <?php
                $lista_especialidades = especialidades_veterinarias();
                $especialidad_actual = $fila['especialidad'] ?? '';
                $es_personalizada = $especialidad_actual !== '' && !in_array($especialidad_actual, $lista_especialidades, true);
            ?>
            <div class="col-md-6">
                <label class="form-label">Especialidad</label>
                <select name="especialidad" class="form-select pc-select-otra" data-otro-target="especialidad_otra_editar">
                    <option value="">Sin especificar</option>
                    <?php foreach ($lista_especialidades as $esp): ?>
                    <option value="<?php echo htmlspecialchars($esp); ?>" <?php echo ($especialidad_actual === $esp) ? 'selected' : ''; ?>><?php echo htmlspecialchars($esp); ?></option>
                    <?php endforeach; ?>
                    <option value="Otra" <?php echo $es_personalizada ? 'selected' : ''; ?>>Otra (especificar)</option>
                </select>
            </div>
            <div class="col-md-6 <?php echo $es_personalizada ? '' : 'd-none'; ?>" id="especialidad_otra_editar">
                <label class="form-label">¿Cuál especialidad?</label>
                <input type="text" name="especialidad_otra" class="form-control" value="<?php echo $es_personalizada ? htmlspecialchars($especialidad_actual) : ''; ?>" placeholder="Escribe la especialidad">
            </div>
            <div class="col-md-6">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control" value="<?php echo htmlspecialchars($fila['telefono'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Correo</label>
                <input type="email" name="correo" class="form-control" value="<?php echo htmlspecialchars($fila['correo'] ?? ''); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Cuenta de acceso vinculada</label>
                <select name="id_usuario" class="form-select">
                    <option value="">Sin cuenta de acceso al sistema</option>
                    <?php while ($u = mysqli_fetch_assoc($usuarios_disponibles)): ?>
                    <option value="<?php echo $u['id_usuario']; ?>" <?php echo ((int)$fila['id_usuario'] === (int)$u['id_usuario']) ? 'selected':''; ?>>
                        <?php echo htmlspecialchars($u['usuario']); ?><?php echo $u['nombre_completo'] ? ' — '.htmlspecialchars($u['nombre_completo']) : ''; ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-12 d-flex gap-2 mt-3">
                <button type="submit" name="actualizar" class="btn btn-pc btn-pc-verde"><i class="bi bi-save me-1"></i>Guardar cambios</button>
                <a href="veterinarios.php" class="btn btn-pc btn-pc-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>

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
