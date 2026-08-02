<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("gestionar_tags", "../");
 
$sql = "SELECT tags_rfid.id_tag, tags_rfid.codigo_tag, tags_rfid.fecha_asignacion, tags_rfid.estado,
               perros.id_perro, perros.nombre AS perro, perros.especie
        FROM tags_rfid
        INNER JOIN perros ON tags_rfid.id_animal = perros.id_perro
        ORDER BY tags_rfid.id_tag DESC";
 
$resultado = mysqli_query($conexion, $sql);
 
if ($resultado === false) {
    die("Error en la consulta de chips de identificación: " . mysqli_error($conexion));
}
 
$raiz = "../";
$pagina_activa = "tags";
$titulo_pagina = "Chips de identificación";
include("../includes/header.php");
?>
 
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 animar gap-2">
    <div>
        <h4 class="mb-1"><i class="bi bi-tag-fill me-2 text-success"></i>Chips de identificación registrados</h4>
        <p class="text-muted mb-0"><?php echo mysqli_num_rows($resultado); ?> tag(s) en el sistema</p>
    </div>
    <a href="tags.php" class="btn btn-pc btn-pc-verde"><i class="bi bi-plus-circle me-1"></i>Nuevo Tag</a>
</div>
 
<div class="pc-tabla-wrap animar">
<table class="pc-tabla">
<thead>
<tr><th>Código</th><th>Fecha de asignación</th><th>Animal</th><th>Especie</th><th>Estado</th><th>Acciones</th></tr>
</thead>
<tbody>
<?php if (mysqli_num_rows($resultado) === 0): ?>
<tr><td colspan="6" class="text-center text-muted py-4">No hay chips de identificación registrados.</td></tr>
<?php endif; ?>
<?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
<tr>
    <td class="fw-semibold"><i class="bi bi-tag me-1 text-success"></i><?php echo htmlspecialchars($fila["codigo_tag"]); ?></td>
    <td><?php echo date("d/m/Y", strtotime($fila["fecha_asignacion"])); ?></td>
    <td><?php echo htmlspecialchars($fila["perro"]); ?></td>
    <td><?php echo htmlspecialchars($fila["especie"] ?: "Perro"); ?></td>
    <td><?php echo badge_estado($fila["estado"] ?? "Activo"); ?></td>
    <td class="text-nowrap">
        <a href="../consulta/resultado_tag.php?codigo_tag=<?php echo urlencode($fila['codigo_tag']); ?>" class="btn btn-sm btn-pc btn-pc-azul" title="Consultar"><i class="bi bi-search"></i></a>
        <?php if (tiene_permiso('gestionar_tags')): ?>
        <a href="editar_tag.php?id=<?php echo $fila['id_tag']; ?>" class="btn btn-sm btn-pc btn-pc-outline" title="Editar"><i class="bi bi-pencil"></i></a>
        <?php endif; ?>
        <?php if (tiene_permiso('eliminar_tags')): ?>
        <a href="eliminar_tag.php?id=<?php echo $fila['id_tag']; ?>" class="btn btn-sm btn-pc btn-pc-rojo" title="Eliminar" onclick="return confirm('¿Eliminar este Tag?')"><i class="bi bi-trash"></i></a>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
 
<?php include("../includes/footer.php"); ?>
 