<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("gestionar_campanas", "../");

$id_campana = (int) ($_GET["id"] ?? $_POST["id_campana"] ?? 0);
$r = mysqli_query($conexion, "SELECT ce.*,
        (SELECT COUNT(*) FROM campanas_atendidos ca WHERE ca.id_campana = ce.id_campania) AS realizadas
    FROM campanas_esterilizacion ce WHERE ce.id_campania=$id_campana");
if (!$r || mysqli_num_rows($r) === 0) {
    header("Location: lista_campanas.php"); exit();
}
$campana = mysqli_fetch_assoc($r);

$mensaje = ""; $tipo_mensaje = "success";

if (isset($_POST["registrar"])) {
    $id_perro = (int) ($_POST["id_perro"] ?? 0);
    $nota = limpiar_dato($conexion, $_POST["nota"] ?? "");
    $usuario = limpiar_dato($conexion, $_SESSION["usuario"]);
    $id_perro_sql = $id_perro > 0 ? $id_perro : "NULL";

    mysqli_query($conexion, "INSERT INTO campanas_atendidos (id_campana, id_perro, nota, usuario)
                             VALUES ($id_campana, $id_perro_sql, '$nota', '$usuario')");

    // Si ya se cubrió el cupo, sugerimos pasar la campaña a "Finalizada" automaticamente solo si sigue "En curso"
    // (realizadas ahora se calcula al vuelo contando campanas_atendidos, ya no es una columna)
    $r_check = mysqli_query($conexion, "SELECT ce.meta_animales, ce.estado,
            (SELECT COUNT(*) FROM campanas_atendidos ca WHERE ca.id_campana = ce.id_campania) AS realizadas
        FROM campanas_esterilizacion ce WHERE ce.id_campania=$id_campana");
    $campana_actual = mysqli_fetch_assoc($r_check);
    if ($campana_actual["meta_animales"] > 0 && $campana_actual["realizadas"] >= $campana_actual["meta_animales"] && $campana_actual["estado"] === "En curso") {
        mysqli_query($conexion, "UPDATE campanas_esterilizacion SET estado='Finalizada' WHERE id_campania=$id_campana");
    }

    registrar_bitacora($conexion, "Registró un animal atendido en la campaña \"{$campana['nombre']}\"", "Campañas");
    header("Location: lista_campanas.php?atendido=1");
    exit();
}

// Animales disponibles para vincular (opcional)
$animales = mysqli_query($conexion, "SELECT id_perro, nombre, especie FROM perros ORDER BY nombre LIMIT 500");

// Historial de atendidos de esta campaña
$atendidos = mysqli_query($conexion, "SELECT ca.*, p.nombre AS animal
                                       FROM campanas_atendidos ca
                                       LEFT JOIN perros p ON ca.id_perro = p.id_perro
                                       WHERE ca.id_campana = $id_campana
                                       ORDER BY ca.id_atendido DESC");

$raiz = "../"; $pagina_activa = "campanas"; $titulo_pagina = "Registrar atendido";
include("../includes/header.php");
?>
<div class="mb-4 animar">
    <h4 class="mb-1"><i class="bi bi-clipboard2-check-fill me-2 text-success"></i>Registrar animal atendido</h4>
    <p class="text-muted mb-0"><?php echo htmlspecialchars($campana['nombre']); ?> · <?php echo $campana['realizadas']; ?> / <?php echo $campana['meta_animales']; ?> cupos usados</p>
</div>

<div class="row g-3">
    <div class="col-lg-5">
        <div class="pc-card animar">
            <div class="pc-card-body">
                <form method="POST" class="pc-form necesita-validacion" novalidate>
                    <input type="hidden" name="id_campana" value="<?php echo $id_campana; ?>">
                    <div class="mb-3">
                        <label class="form-label">Animal (opcional)</label>
                        <select name="id_perro" class="form-select">
                            <option value="0">— Sin vincular a un expediente —</option>
                            <?php while ($a = mysqli_fetch_assoc($animales)): ?>
                            <option value="<?php echo $a['id_perro']; ?>"><?php echo htmlspecialchars($a['nombre']); ?> (<?php echo htmlspecialchars($a['especie']); ?>)</option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nota</label>
                        <input type="text" name="nota" class="form-control" placeholder="Ej. Esterilización realizada sin complicaciones">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" name="registrar" class="btn btn-pc btn-pc-verde"><i class="bi bi-plus-circle me-1"></i>Registrar atendido</button>
                        <a href="lista_campanas.php" class="btn btn-pc btn-pc-outline">Volver</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="pc-card animar animar-1">
            <div class="pc-card-body">
                <h6 class="mb-3"><i class="bi bi-list-check me-2 text-success"></i>Animales atendidos en esta campaña</h6>
                <div class="pc-tabla-wrap">
                <table class="pc-tabla">
                    <thead><tr><th>Fecha</th><th>Animal</th><th>Nota</th><th>Usuario</th></tr></thead>
                    <tbody>
                    <?php if (mysqli_num_rows($atendidos) === 0): ?>
                        <tr><td colspan="4" class="text-center text-muted py-3">Aún no hay animales atendidos registrados.</td></tr>
                    <?php endif; ?>
                    <?php while ($f = mysqli_fetch_assoc($atendidos)): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($f['fecha_hora'])); ?></td>
                            <td><?php echo htmlspecialchars($f['animal'] ?: 'Sin vincular'); ?></td>
                            <td><?php echo htmlspecialchars($f['nota'] ?: '—'); ?></td>
                            <td><?php echo htmlspecialchars($f['usuario'] ?: '—'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include("../includes/footer.php"); ?>
