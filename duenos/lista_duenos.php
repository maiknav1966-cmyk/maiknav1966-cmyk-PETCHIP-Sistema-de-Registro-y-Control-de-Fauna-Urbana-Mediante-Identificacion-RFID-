<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("ver_duenos", "../");

$buscar = isset($_GET['q']) ? limpiar_dato($conexion, $_GET['q']) : "";
$where = $buscar ? "WHERE d.nombre LIKE '%$buscar%' OR d.telefono LIKE '%$buscar%' OR d.colonia LIKE '%$buscar%'" : "";

$sql = "SELECT d.*, (SELECT COUNT(*) FROM perros p WHERE p.id_dueno = d.id_dueno) AS total_mascotas
        FROM duenos d $where
        ORDER BY d.id_dueno DESC";
$resultado = mysqli_query($conexion, $sql);

$raiz = "../";
$pagina_activa = "duenos";
$titulo_pagina = "Dueños";
include("../includes/header.php");
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 animar gap-2">
    <div>
        <h4 class="mb-1"><i class="bi bi-people-fill me-2 text-success"></i>Dueños registrados</h4>
        <p class="text-muted mb-0"><?php echo mysqli_num_rows($resultado); ?> propietario(s) en el sistema</p>
    </div>
    <div class="d-flex gap-2">
        <form class="d-flex" method="GET">
            <input type="text" name="q" value="<?php echo htmlspecialchars($buscar); ?>" class="form-control me-2" placeholder="Buscar por nombre, teléfono o colonia">
            <button class="btn btn-pc btn-pc-outline"><i class="bi bi-search"></i></button>
        </form>
        <a href="duenos.php" class="btn btn-pc btn-pc-verde text-nowrap"><i class="bi bi-person-plus-fill me-1"></i>Nuevo</a>
    </div>
</div>

<div class="pc-tabla-wrap animar">
<table class="pc-tabla">
<thead>
<tr>
    <th></th>
    <th>Nombre</th>
    <th>Teléfono</th>
    <th>Correo</th>
    <th>Colonia / Municipio</th>
    <th>Mascotas</th>
    <th>Acciones</th>
</tr>
</thead>
<tbody>
<?php if (mysqli_num_rows($resultado) === 0): ?>
<tr><td colspan="7" class="text-center text-muted py-4">No se encontraron dueños registrados.</td></tr>
<?php endif; ?>
<?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
<tr>
    <td><img src="<?php echo $fila['foto'] ? '../uploads/duenos/'.htmlspecialchars($fila['foto']) : '../img/logo.png'; ?>" class="avatar-mini"></td>
    <td class="fw-semibold"><?php echo htmlspecialchars($fila["nombre"]); ?></td>
    <td><?php echo htmlspecialchars($fila["telefono"]); ?></td>
    <td><?php echo htmlspecialchars($fila["correo"] ?: "—"); ?></td>
    <td><?php echo htmlspecialchars(($fila["colonia"] ?: "—") . " · " . $fila["municipio"]); ?></td>
    <td><span class="badge rounded-pill bg-success-subtle text-success px-3 py-2"><?php echo $fila['total_mascotas']; ?></span></td>
    <td class="text-nowrap">
        <a href="../perfil/perfil_dueno.php?id=<?php echo $fila['id_dueno']; ?>" class="btn btn-sm btn-pc btn-pc-azul" title="Ver perfil"><i class="bi bi-eye"></i></a>
        <?php if (tiene_permiso('editar_duenos')): ?>
        <a href="editar_dueno.php?id=<?php echo $fila['id_dueno']; ?>" class="btn btn-sm btn-pc btn-pc-outline" title="Editar"><i class="bi bi-pencil"></i></a>
        <?php endif; ?>
        <?php if (tiene_permiso('eliminar_duenos')): ?>
        <a href="eliminar_dueno.php?id=<?php echo $fila['id_dueno']; ?>" class="btn btn-sm btn-pc btn-pc-rojo" title="Eliminar" onclick="return confirm('¿Eliminar a este dueño y sus mascotas asociadas?')"><i class="bi bi-trash"></i></a>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

<?php include("../includes/footer.php"); ?>
