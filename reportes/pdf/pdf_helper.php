<?php
// ============================================================
// Generador central de reportes imprimibles - PetChip
// Ya NO depende de Dompdf. Genera una pagina HTML con el
// encabezado institucional, logotipo y pie de pagina, lista para
// usarse con la impresion del navegador (Ctrl+P -> "Guardar como
// PDF"). El boton "Imprimir / Guardar como PDF" dispara
// window.print() automaticamente si se desea.
// ============================================================

/**
 * Genera y muestra en el navegador un reporte imprimible con el
 * encabezado institucional, logotipo oficial y pie de pagina de
 * PetChip.
 *
 * @param string $titulo_reporte  Nombre del reporte (se muestra en el encabezado)
 * @param string $contenido_html  HTML del cuerpo del reporte (tablas, texto, etc.)
 * @param string $nombre_archivo  Nombre sugerido del archivo, sin extension
 * @param string $orientacion     "portrait" o "landscape"
 */
function generar_pdf_reporte($titulo_reporte, $contenido_html, $nombre_archivo, $orientacion = "portrait") {
    $usuario = $_SESSION["usuario"] ?? "Sistema";
    $rol = function_exists("nombre_rol_legible") ? nombre_rol_legible($_SESSION["rol"] ?? "") : ucfirst($_SESSION["rol"] ?? "");
    $fecha_generacion = date("d/m/Y H:i");

    // Las paginas que arman $contenido_html construyen las rutas de imagen
    // como "file://" + ruta absoluta (heredado de cuando se usaba Dompdf).
    // Aqui se convierten a rutas relativas para que el navegador las cargue.
    $raiz_proyecto = dirname(__DIR__, 2);
    $contenido_html = str_replace('file://' . $raiz_proyecto, '..', $contenido_html);
    $contenido_html = str_replace('file://' . str_replace('\\', '/', $raiz_proyecto), '..', $contenido_html);

    $orientacion_css = $orientacion === "landscape" ? "landscape" : "portrait";
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo_reporte); ?> · PetChip</title>
    <link rel="icon" href="../img/logo.png">
    <style>
        @page { size: <?php echo $orientacion_css; ?>; margin: 16mm 14mm; }
        * { box-sizing: border-box; }
        body { font-family: "Segoe UI", Arial, sans-serif; color: #1f2937; font-size: 13px; margin: 0; padding: 0; background: #eef1f6; }
        .hoja { max-width: 940px; margin: 24px auto; background: #fff; padding: 34px 40px 26px; box-shadow: 0 10px 30px rgba(20,25,45,.12); border-radius: 10px; }
        .encabezado { display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #1C9756; padding-bottom: 12px; margin-bottom: 18px; }
        .encabezado .marca { display: flex; align-items: center; gap: 12px; }
        .encabezado img { height: 48px; }
        .encabezado .institucion { font-size: 15px; font-weight: bold; color: #14743F; }
        .encabezado .municipio { font-size: 11px; color: #6b7280; }
        .encabezado .meta { text-align: right; font-size: 11px; color: #6b7280; }
        h1.titulo-reporte { font-size: 19px; color: #123E99; margin: 6px 0 16px 0; }
        table.datos { width: 100%; border-collapse: collapse; margin-top: 6px; margin-bottom: 16px; }
        table.datos th { background: #14743F; color: #fff; text-align: left; padding: 8px 10px; font-size: 11.5px; }
        table.datos td { padding: 7px 10px; border-bottom: 1px solid #e5e7eb; font-size: 11.5px; }
        table.datos tr:nth-child(even) td { background: #f8faf9; }
        .badge-pdf { padding: 2px 10px; border-radius: 10px; font-size: 10.5px; display: inline-block; }
        .bg-verde { background:#d3f2df; color:#14743F; }
        .bg-ambar { background:#ffecd1; color:#a15c00; }
        .bg-rojo  { background:#ffdcdc; color:#b3261e; }
        .bg-gris  { background:#e5e7eb; color:#4b5563; }
        .pie-pagina { margin-top: 26px; padding-top: 10px; border-top: 1px solid #d1d5db; font-size: 10.5px; color: #6b7280; display: flex; justify-content: space-between; }
        .barra-imprimir { max-width: 940px; margin: 0 auto 0; padding: 12px 4px; display: flex; justify-content: flex-end; gap: 10px; }
        .btn-imprimir {
            background: #1E5FD9; color: #fff; border: none; border-radius: 10px; padding: 10px 20px;
            font-weight: 600; font-size: 14px; cursor: pointer; box-shadow: 0 6px 16px rgba(30,95,217,.3);
        }
        .btn-imprimir:hover { background: #123E99; }
        .btn-volver {
            background: #fff; color: #1E5FD9; border: 1px solid #1E5FD9; border-radius: 10px; padding: 10px 20px;
            font-weight: 600; font-size: 14px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center;
        }
        @media print {
            body { background: #fff; }
            .barra-imprimir { display: none !important; }
            .hoja { box-shadow: none; margin: 0; border-radius: 0; max-width: 100%; }
        }
    </style>
    </head>
    <body>

    <div class="barra-imprimir">
        <a href="reporte.php" class="btn-volver">&larr; Volver a reportes</a>
        <button type="button" class="btn-imprimir" onclick="window.print()">Imprimir / Guardar como PDF</button>
    </div>

    <div class="hoja">
        <div class="encabezado">
            <div class="marca">
                <img src="../img/logo_completo.png" alt="PetChip">
                <div>
                    <div class="institucion">PetChip · Sistema de Control de Fauna Urbana</div>
                    <div class="municipio">H. Ayuntamiento de Ozumba, Estado de México</div>
                </div>
            </div>
            <div class="meta">
                Generado: <?php echo htmlspecialchars($fecha_generacion); ?><br>
                Por: <?php echo htmlspecialchars($usuario); ?> (<?php echo htmlspecialchars($rol); ?>)
            </div>
        </div>

        <h1 class="titulo-reporte"><?php echo htmlspecialchars($titulo_reporte); ?></h1>

        <?php echo $contenido_html; ?>

        <div class="pie-pagina">
            <span>© <?php echo date("Y"); ?> PetChip · Reporte generado automáticamente · Uso oficial municipal</span>
            <span>Documento generado desde el sistema</span>
        </div>
    </div>

    </body>
    </html>
    <?php
    exit();
}
