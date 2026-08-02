<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("ver_animales", "../");

$q = isset($_GET['q']) ? limpiar_dato($conexion, $_GET['q']) : "";
$f_especie = isset($_GET['especie']) ? limpiar_dato($conexion, $_GET['especie']) : "";
$f_estado = isset($_GET['estado']) ? limpiar_dato($conexion, $_GET['estado']) : "";
$f_ester = isset($_GET['esterilizado']) ? $_GET['esterilizado'] : "";

$condiciones = [];
if ($q !== "") $condiciones[] = "(perros.nombre LIKE '%$q%' OR duenos.nombre LIKE '%$q%' OR perros.colonia LIKE '%$q%')";
if ($f_especie !== "") $condiciones[] = "perros.especie = '$f_especie'";
if ($f_estado !== "") $condiciones[] = "perros.estado = '$f_estado'";
if ($f_ester !== "") $condiciones[] = "perros.esterilizado = " . (int) $f_ester;
$where = $condiciones ? "WHERE " . implode(" AND ", $condiciones) : "";

$sql = "SELECT perros.*, duenos.nombre AS dueno
        FROM perros
        INNER JOIN duenos ON perros.id_dueno = duenos.id_dueno
        $where
        ORDER BY perros.id_perro DESC";

$resultado = mysqli_query($conexion, $sql);

$raiz = "../";
$pagina_activa = "perros";
$titulo_pagina = "Mascotas";
include("../includes/header.php");
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 animar gap-2">
    <div>
        <h4 class="mb-1"><i class="bi bi-heart-fill me-2 text-success"></i>Mascotas registradas</h4>
        <p class="text-muted mb-0"><?php echo mysqli_num_rows($resultado); ?> resultado(s)</p>
    </div>
    <a href="perros.php" class="btn btn-pc btn-pc-verde"><i class="bi bi-plus-circle me-1"></i>Registrar mascota</a>
</div>

<div class="pc-card mb-3 animar">
    <div class="pc-card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small mb-1">Buscar</label>
                <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" class="form-control" placeholder="Nombre, dueño o colonia">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Especie</label>
                <select name="especie" class="form-select">
                    <option value="">Todas</option>
                    <?php foreach (["Perro","Gato","Otro"] as $e): ?>
                    <option value="<?php echo $e; ?>" <?php echo $f_especie==$e?'selected':''; ?>><?php echo $e; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Estado</label>
                <select name="estado" class="form-select">
                    <option value="">Todos</option>
                    <?php foreach (["Activo","Perdido","Adoptado","Fallecido"] as $e): ?>
                    <option value="<?php echo $e; ?>" <?php echo $f_estado==$e?'selected':''; ?>><?php echo $e; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Esterilización</label>
                <select name="esterilizado" class="form-select">
                    <option value="">Todos</option>
                    <option value="1" <?php echo $f_ester==='1'?'selected':''; ?>>Esterilizado</option>
                    <option value="0" <?php echo $f_ester==='0'?'selected':''; ?>>Pendiente</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-pc btn-pc-verde w-100"><i class="bi bi-funnel me-1"></i>Filtrar</button>
                <a href="lista_perros.php" class="btn btn-pc btn-pc-outline"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="pc-tabla-wrap animar">
<table class="pc-tabla">
<thead>
<tr>
    <th></th><th>Nombre</th><th>Especie</th><th>Raza</th><th>Edad</th><th>Dueño</th><th>Esterilizado</th><th>Estado</th><th>Acciones</th>
</tr>
</thead>
<tbody>
<?php if (mysqli_num_rows($resultado) === 0): ?>
<tr><td colspan="9" class="text-center text-muted py-4">No se encontraron mascotas con esos filtros.</td></tr>
<?php endif; ?>
<?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
<tr>
    <td><img src="<?php echo $fila['foto'] ? '../uploads/perros/'.htmlspecialchars($fila['foto']) : '../img/logo.png'; ?>" class="avatar-mini"></td>
    <td class="fw-semibold"><?php echo htmlspecialchars($fila["nombre"]); ?></td>
    <td><?php echo htmlspecialchars($fila["especie"] ?: "Perro"); ?></td>
    <td><?php echo htmlspecialchars($fila["raza"] ?: "—"); ?></td>
    <td><?php echo htmlspecialchars($fila["edad"] ?: "—"); ?></td>
    <td><?php echo htmlspecialchars($fila["dueno"]); ?></td>
    <td><?php echo $fila["esterilizado"] ? '<span class="badge rounded-pill bg-success-subtle text-success px-3 py-2">Sí</span>' : '<span class="badge rounded-pill bg-warning-subtle text-warning px-3 py-2">Pendiente</span>'; ?></td>
    <td><?php echo badge_estado($fila["estado"] ?? "Activo"); ?></td>
    <td class="text-nowrap">
        <a href="../perfil/perfil_perro.php?id=<?php echo $fila['id_perro']; ?>" class="btn btn-sm btn-pc btn-pc-azul" title="Ver perfil"><i class="bi bi-eye"></i></a>
        <?php if (tiene_permiso('editar_animales')): ?>
        <a href="editar_perro.php?id=<?php echo $fila['id_perro']; ?>" class="btn btn-sm btn-pc btn-pc-outline" title="Editar"><i class="bi bi-pencil"></i></a>
        <?php endif; ?>
        <?php if (tiene_permiso('eliminar_animales')): ?>
        <a href="eliminar_perro.php?id=<?php echo $fila['id_perro']; ?>" class="btn btn-sm btn-pc btn-pc-rojo" title="Eliminar" onclick="return confirm('¿Eliminar este registro?')"><i class="bi bi-trash"></i></a>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

<div class="mt-3 animar">
    <a href="../reportes/reporte_perros.php" class="btn btn-pc btn-pc-outline"><i class="bi bi-file-earmark-arrow-down me-1"></i>Exportar este listado</a>
</div>

<?php include("../includes/footer.php"); ?>
