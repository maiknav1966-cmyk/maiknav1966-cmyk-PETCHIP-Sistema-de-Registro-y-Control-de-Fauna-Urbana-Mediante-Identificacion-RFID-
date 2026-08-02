<?php
// Header para páginas PÚBLICAS (sin sesión, sin sidebar).
// Variables esperadas: $titulo_pagina
$titulo_pagina = $titulo_pagina ?? "PetChip";
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($titulo_pagina); ?> · PetChip</title>
<link rel="icon" href="../img/logo.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<link href="../assets/css/style.css" rel="stylesheet">
<style>
    body { background: linear-gradient(160deg, var(--pc-navy), var(--pc-primario-oscuro) 130%); min-height: 100vh; }
    .pc-publico-wrap { max-width: 760px; margin: 0 auto; padding: 32px 16px 60px; }
    .pc-publico-marca { display: flex; align-items: center; gap: 10px; justify-content: center; margin-bottom: 22px; }
    .pc-publico-marca img { width: 38px; }
    .pc-publico-marca span { color: #fff; font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1.3rem; }
</style>
</head>
<body>
<div class="pc-publico-wrap">
    <div class="pc-publico-marca animar">
        <img src="../img/logo.png" alt="PetChip">
        <span>PetChip</span>
    </div>
    <div class="text-center mb-3 animar">
        <a href="perdidos.php" class="btn btn-pc btn-pc-outline btn-sm text-white border-white me-2"><i class="bi bi-house-heart me-1"></i>Inicio</a>
        <a href="buscar_chip.php" class="btn btn-pc btn-pc-outline btn-sm text-white border-white"><i class="bi bi-broadcast me-1"></i>Buscar por chip</a>
    </div>
