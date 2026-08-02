<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
session_start();
include("../config/conexion.php");
include("../includes/funciones.php");
requerir_sesion("../");
requerir_permiso("ver_estadisticas", "../");

function contar2($conexion, $sql) { $r = mysqli_query($conexion, $sql); $f = mysqli_fetch_row($r); return (int) $f[0]; }

$perros_esp = contar2($conexion, "SELECT COUNT(*) FROM perros WHERE especie='Perro'");
$gatos_esp  = contar2($conexion, "SELECT COUNT(*) FROM perros WHERE especie='Gato'");
$otros_esp  = contar2($conexion, "SELECT COUNT(*) FROM perros WHERE especie NOT IN ('Perro','Gato')");

$ester_si = contar2($conexion, "SELECT COUNT(*) FROM perros WHERE esterilizado=1");
$ester_no = contar2($conexion, "SELECT COUNT(*) FROM perros WHERE esterilizado=0");

$colonias = [];
$res_col = mysqli_query($conexion, "SELECT colonia, COUNT(*) AS total FROM perros WHERE colonia IS NOT NULL AND colonia<>'' GROUP BY colonia ORDER BY total DESC LIMIT 8");
while ($f = mysqli_fetch_assoc($res_col)) $colonias[] = $f;

$meses_labels = []; $meses_datos = [];
for ($i = 11; $i >= 0; $i--) {
    $mes = date("Y-m", strtotime("-$i months"));
    $meses_labels[] = date("M Y", strtotime("-$i months"));
    $meses_datos[] = contar2($conexion, "SELECT COUNT(*) FROM perros WHERE DATE_FORMAT(fecha_registro,'%Y-%m')='$mes'");
}

$raiz = "../"; $pagina_activa = "estadisticas"; $titulo_pagina = "Estadísticas";
include("../includes/header.php");
?>
<div class="mb-4 animar">
    <h4 class="mb-1"><i class="bi bi-bar-chart-line-fill me-2 text-success"></i>Estadísticas de fauna urbana</h4>
    <p class="text-muted mb-0">Ozumba, Estado de México</p>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="pc-card animar"><div class="pc-card-header"><i class="bi bi-pie-chart-fill me-2"></i>Perros vs Gatos vs Otros</div>
        <div class="pc-card-body"><canvas id="grEspecie" height="200"></canvas></div></div>
    </div>
    <div class="col-lg-6">
        <div class="pc-card animar"><div class="pc-card-header"><i class="bi bi-clipboard2-pulse-fill me-2"></i>Esterilización</div>
        <div class="pc-card-body"><canvas id="grEster" height="200"></canvas></div></div>
    </div>
    <div class="col-lg-7">
        <div class="pc-card animar"><div class="pc-card-header"><i class="bi bi-geo-alt-fill me-2"></i>Mascotas por colonia</div>
        <div class="pc-card-body"><canvas id="grColonia" height="220"></canvas></div></div>
    </div>
    <div class="col-lg-5">
        <div class="pc-card animar"><div class="pc-card-header"><i class="bi bi-graph-up me-2"></i>Registros mensuales</div>
        <div class="pc-card-body"><canvas id="grMeses" height="220"></canvas></div></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('grEspecie'), { type:'doughnut',
    data:{ labels:['Perros','Gatos','Otros'], datasets:[{ data:[<?php echo $perros_esp;?>,<?php echo $gatos_esp;?>,<?php echo $otros_esp;?>], backgroundColor:['#1E5FD9','#1C9756','#FFB300'] }] },
    options:{ plugins:{ legend:{ position:'bottom' } } } });

new Chart(document.getElementById('grEster'), { type:'doughnut',
    data:{ labels:['Esterilizados','Pendientes'], datasets:[{ data:[<?php echo $ester_si;?>,<?php echo $ester_no;?>], backgroundColor:['#1E5FD9','#FFB300'] }] },
    options:{ plugins:{ legend:{ position:'bottom' } } } });

new Chart(document.getElementById('grColonia'), { type:'bar',
    data:{ labels: <?php echo json_encode(array_column($colonias,'colonia')); ?>,
           datasets:[{ label:'Mascotas', data: <?php echo json_encode(array_map('intval', array_column($colonias,'total'))); ?>, backgroundColor:'#1C9756', borderRadius:8 }] },
    options:{ indexAxis:'y', plugins:{ legend:{ display:false } } } });

new Chart(document.getElementById('grMeses'), { type:'line',
    data:{ labels: <?php echo json_encode($meses_labels); ?>,
           datasets:[{ label:'Registros', data: <?php echo json_encode($meses_datos); ?>, borderColor:'#1E5FD9', backgroundColor:'rgba(30,95,217,.15)', fill:true, tension:.35 }] },
    options:{ plugins:{ legend:{ display:false } }, scales:{ y:{ beginAtZero:true, ticks:{ precision:0 } } } } });
</script>
<?php include("../includes/footer.php"); ?>
