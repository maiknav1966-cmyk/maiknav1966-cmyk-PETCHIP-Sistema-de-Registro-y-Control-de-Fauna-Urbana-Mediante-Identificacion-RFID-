<?php
// ============================================================
// Funciones auxiliares comunes - PetChip
// Incluir despues de session_start() y de conexion.php
// ============================================================

function requerir_sesion($raiz = "") {
    if (!isset($_SESSION["usuario"])) {
        header("Location: {$raiz}login.php");
        exit();
    }
    // Si la cuenta fue desactivada despues de iniciar sesion, se cierra la sesion.
    global $conexion;
    if (isset($conexion) && isset($_SESSION["id_usuario"])) {
        $id = (int) $_SESSION["id_usuario"];
        $r = mysqli_query($conexion, "SELECT activo FROM usuarios WHERE id_usuario=$id");
        if ($r && ($f = mysqli_fetch_assoc($r)) && (int) $f["activo"] === 0) {
            session_destroy();
            header("Location: {$raiz}login.php?bloqueado=1");
            exit();
        }
    }
}

// ============================================================
// Sistema de permisos por rol - PetChip
// Roles válidos: administrador | veterinario | autoridad | dueno
// ("operador" se mantiene como alias heredado de "autoridad" para
// no romper cuentas antiguas que no hayan corrido la migración de
// la Parte 6). El rol "dueno" es exclusivo del portal de
// autoservicio y nunca entra al panel de staff.
// ============================================================
function permisos_mapa() {
    return [
        // Animales (mascotas)
        "ver_animales"      => ["administrador", "veterinario", "autoridad", "operador"],
        "crear_animales"    => ["administrador", "veterinario", "autoridad", "operador"],
        "editar_animales"   => ["administrador", "veterinario", "autoridad", "operador"],
        "eliminar_animales" => ["administrador"],
        // Dueños (el veterinario solo consulta, no crea ni edita dueños)
        "ver_duenos"        => ["administrador", "veterinario", "autoridad", "operador"],
        "crear_duenos"      => ["administrador", "autoridad", "operador"],
        "editar_duenos"     => ["administrador", "autoridad", "operador"],
        "eliminar_duenos"   => ["administrador"],
        // RFID / Chips de identificación
        "consultar_rfid"    => ["administrador", "veterinario", "autoridad", "operador"],
        "gestionar_tags"    => ["administrador", "autoridad", "operador"],
        "eliminar_tags"     => ["administrador"],
        // Campañas
        "gestionar_campanas"=> ["administrador", "veterinario"],
        // Extravío (perdidos y encontrados, gestión de staff)
        "gestionar_extravio"=> ["administrador", "veterinario", "autoridad", "operador"],
        // Estadísticas y reportes: exclusivos del Administrador
        "ver_estadisticas"  => ["administrador"],
        "ver_reportes"      => ["administrador"],
        // Veterinarios (catálogo): Administrador y Encargado pueden registrar/gestionar
        "ver_veterinarios"      => ["administrador", "veterinario", "autoridad", "operador"],
        "gestionar_veterinarios"=> ["administrador", "autoridad", "operador"],
        // Administración: exclusivos del Administrador
        "ver_bitacora"      => ["administrador"],
        "gestionar_usuarios"=> ["administrador"],
        "ver_configuracion" => ["administrador"],
    ];
}

function tiene_permiso($permiso) {
    $rol = $_SESSION["rol"] ?? "";
    $mapa = permisos_mapa();
    if (!isset($mapa[$permiso])) return true; // permiso no controlado = acceso libre a usuarios con sesion
    return in_array($rol, $mapa[$permiso], true);
}

// ============================================================
// Catálogo de especialidades veterinarias (para menús desplegables)
// ============================================================
function especialidades_veterinarias() {
    return [
        "Medicina general",
        "Cirugía",
        "Medicina interna",
        "Dermatología",
        "Cardiología",
        "Odontología veterinaria",
        "Oftalmología",
        "Ortopedia y traumatología",
        "Oncología",
        "Neurología",
        "Medicina de fauna silvestre",
        "Nutrición y dietética",
        "Anestesiología",
        "Reproducción y obstetricia",
        "Etología (conducta animal)",
    ];
}

// ============================================================
// Catálogos de raza y color (para menús desplegables de mascotas)
// ============================================================
function razas_por_especie() {
    return [
        "Perro" => [
            "Mestizo / Criollo", "Labrador Retriever", "Pastor Alemán", "Chihuahua",
            "Poodle (Caniche)", "Bulldog Francés", "Bulldog Inglés", "Schnauzer",
            "Pug", "Husky Siberiano", "Golden Retriever", "Xoloitzcuintle",
            "Rottweiler", "Boxer", "Dálmata", "Yorkshire Terrier",
            "Salchicha (Dachshund)", "Beagle", "Pitbull",
        ],
        "Gato" => [
            "Mestizo / Criollo", "Siamés", "Persa", "Angora",
            "Bengalí", "Maine Coon", "Esfinge (Sphynx)", "Ragdoll",
            "Británico de pelo corto",
        ],
        "Otro" => [
            "Sin raza definida",
        ],
    ];
}

function colores_comunes() {
    return [
        "Negro", "Blanco", "Café", "Dorado / Beige", "Gris",
        "Atigrado", "Manchado (blanco y negro)", "Manchado (blanco y café)",
        "Tricolor", "Naranja / Canela",
    ];
}

function nombre_rol_legible($rol) {
    $nombres = [
        "administrador" => "Administrador",
        "veterinario"   => "Veterinario",
        "autoridad"     => "Encargado",
        "operador"      => "Encargado",
        "dueno"         => "Dueño",
    ];
    return $nombres[$rol] ?? ucfirst($rol);
}

// ============================================================
// Portal de autoservicio del dueño - sesión independiente del
// panel de staff (usa $_SESSION["dueno_id"], nunca "usuario"/"rol")
// ============================================================
function requerir_sesion_dueno($raiz = "") {
    if (!isset($_SESSION["dueno_id"])) {
        header("Location: {$raiz}login_dueno.php");
        exit();
    }
    global $conexion;
    if (isset($conexion)) {
        $id = (int) $_SESSION["dueno_id"];
        $r = mysqli_query($conexion, "SELECT portal_activo FROM duenos WHERE id_dueno=$id");
        if ($r && ($f = mysqli_fetch_assoc($r)) && (int) $f["portal_activo"] === 0) {
            session_unset();
            session_destroy();
            header("Location: {$raiz}login_dueno.php?bloqueado=1");
            exit();
        }
    }
}

function requerir_permiso($permiso, $raiz = "") {
    if (!tiene_permiso($permiso)) {
        $_SESSION["pc_acceso_denegado"] = "Acceso denegado: tu rol (" . ($_SESSION["rol"] ?? "sin rol") . ") no tiene permiso para esa sección.";
        header("Location: {$raiz}menu.php");
        exit();
    }
}

function es_administrador() {
    return ($_SESSION["rol"] ?? "") === "administrador";
}

function limpiar_dato($conexion, $dato) {
    return mysqli_real_escape_string($conexion, trim($dato ?? ""));
}

function registrar_bitacora($conexion, $accion, $modulo = "") {
    $usuario = isset($_SESSION["usuario"]) ? limpiar_dato($conexion, $_SESSION["usuario"]) : "sistema";
    $accion = limpiar_dato($conexion, $accion);
    $modulo = limpiar_dato($conexion, $modulo);
    mysqli_query($conexion, "INSERT INTO bitacora (usuario, accion, modulo) VALUES ('$usuario', '$accion', '$modulo')");
}

function subir_foto($campo, $carpeta_destino) {
    // Devuelve el nombre de archivo guardado, o null si no se subio nada.
    if (!isset($_FILES[$campo]) || $_FILES[$campo]["error"] !== UPLOAD_ERR_OK) {
        return null;
    }
    $permitidas = ["jpg", "jpeg", "png", "webp"];
    $extension = strtolower(pathinfo($_FILES[$campo]["name"], PATHINFO_EXTENSION));
    if (!in_array($extension, $permitidas)) {
        return null;
    }
    if (!is_dir($carpeta_destino)) {
        mkdir($carpeta_destino, 0755, true);
    }
    $nombre_final = uniqid("img_") . "." . $extension;
    if (move_uploaded_file($_FILES[$campo]["tmp_name"], $carpeta_destino . "/" . $nombre_final)) {
        return $nombre_final;
    }
    return null;
}

function badge_estado($estado) {
    $estado = $estado ?: "Activo";
    $clases = [
        "Activo"    => "bg-success-subtle text-success",
        "Perdido"   => "bg-danger-subtle text-danger",
        "Fallecido" => "bg-secondary-subtle text-secondary",
        "Adoptado"  => "bg-info-subtle text-info",
        "Inactivo"  => "bg-secondary-subtle text-secondary",
        "Resuelto"  => "bg-success-subtle text-success",
        "Programada"=> "bg-warning-subtle text-warning",
        "En curso"  => "bg-primary-subtle text-primary",
        "Finalizada"=> "bg-success-subtle text-success",
        "Cancelada" => "bg-danger-subtle text-danger",
    ];
    $clase = $clases[$estado] ?? "bg-light text-dark";
    return "<span class=\"badge rounded-pill {$clase} px-3 py-2\">{$estado}</span>";
}

// ============================================================
// Parte 5 - QR público, notificaciones y avisos
// ============================================================
function obtener_token_publico($conexion, $id_perro) {
    $id_perro = (int) $id_perro;
    $r = mysqli_fetch_assoc(mysqli_query($conexion, "SELECT token_publico FROM perros WHERE id_perro=$id_perro"));
    if ($r && !empty($r['token_publico'])) return $r['token_publico'];
    return regenerar_token_publico($conexion, $id_perro);
}

function regenerar_token_publico($conexion, $id_perro) {
    $id_perro = (int) $id_perro;
    $token = bin2hex(random_bytes(16));
    mysqli_query($conexion, "UPDATE perros SET token_publico='$token' WHERE id_perro=$id_perro");
    return $token;
}

function url_publica_animal($token) {
    $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    $carpeta = rtrim(str_replace("\\", "/", dirname(dirname($_SERVER['SCRIPT_NAME']))), "/");
    return $protocolo . "://" . $_SERVER['HTTP_HOST'] . $carpeta . "/publico/ficha.php?t=" . $token;
}

function crear_notificacion($conexion, $id_dueno, $tipo, $mensaje, $id_perro = null) {
    $id_dueno = (int) $id_dueno;
    $mensaje = limpiar_dato($conexion, $mensaje);
    $tipo = limpiar_dato($conexion, $tipo);
    $id_perro_sql = $id_perro ? (int) $id_perro : "NULL";
    mysqli_query($conexion, "INSERT INTO notificaciones(id_dueno, id_perro, tipo, mensaje) VALUES($id_dueno, $id_perro_sql, '$tipo', '$mensaje')");
}

function tiempo_relativo($fecha_hora) {
    if (!$fecha_hora) return "";
    $diferencia = time() - strtotime($fecha_hora);
    if ($diferencia < 60) return "hace un momento";
    if ($diferencia < 3600) return "hace " . floor($diferencia / 60) . " min";
    if ($diferencia < 86400) return "hace " . floor($diferencia / 3600) . " h";
    return "hace " . floor($diferencia / 86400) . " días";
}
?>
