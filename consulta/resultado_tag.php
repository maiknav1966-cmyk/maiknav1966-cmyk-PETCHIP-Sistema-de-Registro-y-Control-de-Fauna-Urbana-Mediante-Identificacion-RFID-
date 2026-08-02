<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("consultar_rfid", "../");

$codigo_tag = limpiar_dato($conexion, $_GET["codigo_tag"] ?? "");

$raiz = "../";
$pagina_activa = "consulta";
$titulo_pagina = "Resultado de consulta";

$sql = "SELECT tags_rfid.*, perros.*, duenos.nombre AS dueno_nombre, duenos.telefono AS dueno_telefono,
               duenos.correo AS dueno_correo, duenos.direccion AS dueno_direccion, duenos.colonia AS dueno_colonia,
               duenos.id_dueno
        FROM tags_rfid
        INNER JOIN perros ON tags_rfid.id_animal = perros.id_perro
        INNER JOIN duenos ON perros.id_dueno = duenos.id_dueno
        WHERE tags_rfid.codigo_tag = '$codigo_tag'";
$resultado = mysqli_query($conexion, $sql);
$info = $resultado ? mysqli_fetch_assoc($resultado) : null;

if ($info) {
    // Registrar la lectura y en la bitácora
    $id_tag_actual = (int) $info["id_tag"];
    $usuario_sesion = limpiar_dato($conexion, $_SESSION["usuario"]);
    mysqli_query($conexion, "INSERT INTO lecturas_rfid(id_tag, ubicacion, usuario) VALUES($id_tag_actual, 'Consulta en sistema', '$usuario_sesion')");
    registrar_bitacora($conexion, "Buscó la mascota con chip \"$codigo_tag\"", "Encontrar mascota");
}

include("../includes/header.php");
?>

<div class="mb-4 animar">
    <a href="buscar_tag.php" class="text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Nueva búsqueda</a>
    <h4 class="mt-2 mb-0"><i class="bi bi-broadcast me-2 text-success"></i>Resultado para: <?php echo htmlspecialchars($codigo_tag); ?></h4>
</div>

<?php if (!$info): ?>
    <div class="alert alert-danger pc-alert animar"><i class="bi bi-exclamation-octagon-fill me-2"></i>No se encontró ninguna mascota vinculada a ese chip de identificación.</div>
<?php else: ?>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="pc-card text-center animar">
            <div class="pc-card-body">
                <img src="<?php echo $info['foto'] ? '../uploads/perros/'.htmlspecialchars($info['foto']) : '../img/logo.png'; ?>" class="rounded-4 mb-3" style="width:150px;height:150px;object-fit:cover;">
                <h5 class="mb-1"><?php echo htmlspecialchars($info['nombre']); ?></h5>
                <p class="text-muted mb-2"><?php echo htmlspecialchars($info['especie'] ?: 'Perro'); ?> · <?php echo htmlspecialchars($info['raza'] ?: 'Raza no especificada'); ?></p>
                <?php echo badge_estado($info['estado'] ?? 'Activo'); ?>
                <hr>
                <p class="mb-1 small"><i class="bi bi-tag me-2"></i><strong><?php echo htmlspecialchars($info['codigo_tag']); ?></strong></p>
                <p class="mb-0 small text-muted">Asignado el <?php echo date('d/m/Y', strtotime($info['fecha_asignacion'])); ?></p>
                <a href="../perfil/perfil_perro.php?id=<?php echo $info['id_perro']; ?>" class="btn btn-pc btn-pc-verde w-100 mt-3"><i class="bi bi-eye me-1"></i>Ver perfil completo</a>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="pc-card h-100 animar">
                    <div class="pc-card-header"><i class="bi bi-heart-fill me-2"></i>Datos del animal</div>
                    <div class="pc-card-body small">
                        <p class="mb-2"><strong>Sexo:</strong> <?php echo htmlspecialchars($info['sexo'] ?: '—'); ?></p>
                        <p class="mb-2"><strong>Edad:</strong> <?php echo htmlspecialchars($info['edad'] ?: '—'); ?> años</p>
                        <p class="mb-2"><strong>Color:</strong> <?php echo htmlspecialchars($info['color'] ?: '—'); ?></p>
                        <p class="mb-2"><strong>Peso:</strong> <?php echo htmlspecialchars($info['peso'] ?: '—'); ?> kg</p>
                        <p class="mb-2"><strong>Tamaño:</strong> <?php echo htmlspecialchars($info['tamano'] ?: '—'); ?></p>
                        <p class="mb-2"><strong>Esterilizado:</strong> <?php echo $info['esterilizado'] ? 'Sí' : 'Pendiente'; ?></p>
                        <p class="mb-0"><strong>Colonia:</strong> <?php echo htmlspecialchars($info['colonia'] ?: '—'); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="pc-card h-100 animar">
                    <div class="pc-card-header"><i class="bi bi-person-fill me-2"></i>Dueño responsable</div>
                    <div class="pc-card-body small">
                        <p class="mb-2"><strong>Nombre:</strong> <?php echo htmlspecialchars($info['dueno_nombre']); ?></p>
                        <p class="mb-2"><strong>Teléfono:</strong> <?php echo htmlspecialchars($info['dueno_telefono']); ?></p>
                        <p class="mb-2"><strong>Correo:</strong> <?php echo htmlspecialchars($info['dueno_correo'] ?: '—'); ?></p>
                        <p class="mb-2"><strong>Dirección:</strong> <?php echo htmlspecialchars($info['dueno_direccion']); ?></p>
                        <p class="mb-0"><strong>Colonia:</strong> <?php echo htmlspecialchars($info['dueno_colonia'] ?: '—'); ?></p>
                        <a href="../perfil/perfil_dueno.php?id=<?php echo $info['id_dueno']; ?>" class="btn btn-pc btn-pc-outline btn-sm mt-2">Ver perfil del dueño</a>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="pc-card animar">
                    <div class="pc-card-header"><i class="bi bi-clock-history me-2"></i>Historial de lecturas de este Tag</div>
                    <div class="pc-card-body">
                        <?php
                        $lecturas = mysqli_query($conexion, "SELECT * FROM lecturas_rfid WHERE id_tag = {$info['id_tag']} ORDER BY id_lectura DESC LIMIT 10");
                        if (mysqli_num_rows($lecturas) === 0): ?>
                            <p class="text-muted mb-0">Esta es la primera vez que se consulta este Tag.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead><tr><th>Fecha y hora</th><th>Usuario</th><th>Ubicación</th></tr></thead>
                                <tbody>
                                <?php while ($l = mysqli_fetch_assoc($lecturas)): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y H:i', strtotime($l['fecha_hora'])); ?></td>
                                        <td><?php echo htmlspecialchars($l['usuario']); ?></td>
                                        <td><?php echo htmlspecialchars($l['ubicacion'] ?: '—'); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                                </tbody>
                            </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<?php include("../includes/footer.php"); ?>
