-- ============================================================
-- PetChip - Migracion COMPLETA en un solo archivo
-- Combina actualizar_bd.sql + actualizar_parte5.sql + actualizar_parte6.sql
-- en el orden correcto. Ejecutar UNA SOLA VEZ en phpMyAdmin
-- (pestaña SQL) sobre tu base de datos "petchip".
--
-- Si una linea marca error porque la columna/tabla ya existe,
-- es normal: ignorala y sigue con la siguiente. phpMyAdmin
-- continua ejecutando el resto del script aunque una linea falle.
-- ============================================================

USE petchip;

-- ============================================================
-- PARTE 1 (antes: actualizar_bd.sql)
-- ============================================================


-- USUARIOS
ALTER TABLE usuarios ADD COLUMN nombre_completo VARCHAR(100) NULL;
ALTER TABLE usuarios ADD COLUMN activo TINYINT(1) NOT NULL DEFAULT 1;

-- DUENOS
ALTER TABLE duenos ADD COLUMN correo VARCHAR(100) NULL;
ALTER TABLE duenos ADD COLUMN colonia VARCHAR(100) NULL;
ALTER TABLE duenos ADD COLUMN municipio VARCHAR(100) NOT NULL DEFAULT 'Ozumba';
ALTER TABLE duenos ADD COLUMN codigo_postal VARCHAR(10) NULL;
ALTER TABLE duenos ADD COLUMN foto VARCHAR(255) NULL;
ALTER TABLE duenos ADD COLUMN fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP;

-- PERROS (animales)
ALTER TABLE perros ADD COLUMN especie VARCHAR(20) NOT NULL DEFAULT 'Perro';
ALTER TABLE perros ADD COLUMN sexo VARCHAR(10) NULL;
ALTER TABLE perros ADD COLUMN color VARCHAR(50) NULL;
ALTER TABLE perros ADD COLUMN peso DECIMAL(5,2) NULL;
ALTER TABLE perros ADD COLUMN tamano VARCHAR(20) NULL;
ALTER TABLE perros ADD COLUMN fecha_nacimiento DATE NULL;
ALTER TABLE perros ADD COLUMN esterilizado TINYINT(1) NOT NULL DEFAULT 0;
ALTER TABLE perros ADD COLUMN estado VARCHAR(20) NOT NULL DEFAULT 'Activo';
ALTER TABLE perros ADD COLUMN colonia VARCHAR(100) NULL;
ALTER TABLE perros ADD COLUMN foto VARCHAR(255) NULL;
ALTER TABLE perros ADD COLUMN observaciones TEXT NULL;
ALTER TABLE perros ADD COLUMN fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP;

-- TAGS
ALTER TABLE tags_rfid ADD COLUMN estado VARCHAR(20) NOT NULL DEFAULT 'Activo';

-- TABLAS NUEVAS
CREATE TABLE IF NOT EXISTS lecturas_rfid (
    id_lectura INT NOT NULL AUTO_INCREMENT,
    id_tag INT NOT NULL,
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    ubicacion VARCHAR(150) NULL,
    usuario VARCHAR(50) NULL,
    PRIMARY KEY (id_lectura),
    CONSTRAINT fk_lectura_tag2 FOREIGN KEY (id_tag) REFERENCES tags_rfid(id_tag) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS vacunas (
    id_vacuna INT NOT NULL AUTO_INCREMENT,
    id_perro INT NOT NULL,
    nombre_vacuna VARCHAR(100) NOT NULL,
    fecha_aplicacion DATE NOT NULL,
    proxima_fecha DATE NULL,
    veterinario VARCHAR(100) NULL,
    PRIMARY KEY (id_vacuna),
    CONSTRAINT fk_vacuna_perro2 FOREIGN KEY (id_perro) REFERENCES perros(id_perro) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS historial_veterinario (
    id_historial INT NOT NULL AUTO_INCREMENT,
    id_perro INT NOT NULL,
    fecha DATE NOT NULL,
    motivo VARCHAR(150) NOT NULL,
    diagnostico TEXT NULL,
    tratamiento TEXT NULL,
    veterinario VARCHAR(100) NULL,
    PRIMARY KEY (id_historial),
    CONSTRAINT fk_historial_perro2 FOREIGN KEY (id_perro) REFERENCES perros(id_perro) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS campanas_esterilizacion (
    id_campana INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(150) NOT NULL,
    fecha DATE NOT NULL,
    lugar VARCHAR(150) NOT NULL,
    cupo INT NOT NULL DEFAULT 0,
    realizadas INT NOT NULL DEFAULT 0,
    estado VARCHAR(20) NOT NULL DEFAULT 'Programada',
    PRIMARY KEY (id_campana)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Detalle de animales atendidos por campaña (alimenta el contador "realizadas")
CREATE TABLE IF NOT EXISTS campanas_atendidos (
    id_atendido INT NOT NULL AUTO_INCREMENT,
    id_campana INT NOT NULL,
    id_perro INT NULL,
    nota VARCHAR(255) NULL,
    usuario VARCHAR(50) NULL,
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_atendido),
    CONSTRAINT fk_atendido_campana FOREIGN KEY (id_campana) REFERENCES campanas_esterilizacion(id_campana) ON DELETE CASCADE,
    CONSTRAINT fk_atendido_perro FOREIGN KEY (id_perro) REFERENCES perros(id_perro) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS reportes_extravio (
    id_reporte INT NOT NULL AUTO_INCREMENT,
    tipo VARCHAR(15) NOT NULL,
    id_perro INT NULL,
    nombre_animal VARCHAR(100) NULL,
    especie VARCHAR(20) NULL,
    descripcion TEXT NULL,
    lugar VARCHAR(150) NULL,
    fecha DATE NOT NULL,
    contacto VARCHAR(100) NOT NULL,
    foto VARCHAR(255) NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'Activo',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_reporte),
    CONSTRAINT fk_reporte_perro2 FOREIGN KEY (id_perro) REFERENCES perros(id_perro) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS bitacora (
    id_bitacora INT NOT NULL AUTO_INCREMENT,
    usuario VARCHAR(50) NULL,
    accion VARCHAR(255) NOT NULL,
    modulo VARCHAR(50) NULL,
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_bitacora)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- PARTE 2 (antes: actualizar_parte5.sql)
-- ============================================================

-- ============================================================

-- Ficha pública del animal: token único para el QR y bandera de
-- privacidad para compartir (o no) información médica.
ALTER TABLE perros ADD COLUMN token_publico VARCHAR(40) NULL UNIQUE AFTER observaciones;
ALTER TABLE perros ADD COLUMN compartir_info_medica TINYINT(1) NOT NULL DEFAULT 0 AFTER token_publico;

-- Lecturas del QR/RFID: ahora también se registran escaneos
-- públicos (sin usuario logueado) y, si el navegador lo permite,
-- la ubicación donde se escaneó.
ALTER TABLE lecturas_rfid MODIFY usuario VARCHAR(100) NULL;
ALTER TABLE lecturas_rfid ADD COLUMN origen VARCHAR(20) NOT NULL DEFAULT 'sistema' AFTER usuario;
ALTER TABLE lecturas_rfid ADD COLUMN lat DECIMAL(10,7) NULL AFTER origen;
ALTER TABLE lecturas_rfid ADD COLUMN lng DECIMAL(10,7) NULL AFTER lat;

-- Reportes de perdidos/encontrados: recompensa y ubicación para el mapa.
ALTER TABLE reportes_extravio ADD COLUMN recompensa VARCHAR(100) NULL AFTER contacto;
ALTER TABLE reportes_extravio ADD COLUMN lat DECIMAL(10,7) NULL AFTER lugar;
ALTER TABLE reportes_extravio ADD COLUMN lng DECIMAL(10,7) NULL AFTER lat;

-- Centro de notificaciones del dueño.
CREATE TABLE IF NOT EXISTS notificaciones (
    id_notificacion INT NOT NULL AUTO_INCREMENT,
    id_dueno INT NOT NULL,
    id_perro INT NULL,
    tipo VARCHAR(30) NOT NULL DEFAULT 'general',
    mensaje VARCHAR(255) NOT NULL,
    leida TINYINT(1) NOT NULL DEFAULT 0,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_notificacion),
    CONSTRAINT fk_notif_dueno FOREIGN KEY (id_dueno) REFERENCES duenos(id_dueno) ON DELETE CASCADE,
    CONSTRAINT fk_notif_perro FOREIGN KEY (id_perro) REFERENCES perros(id_perro) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- "Avisar al dueño": lo que llena quien encuentra a la mascota
-- al escanear el QR público.
CREATE TABLE IF NOT EXISTS avisos_encontrado (
    id_aviso INT NOT NULL AUTO_INCREMENT,
    id_perro INT NOT NULL,
    nombre_reportante VARCHAR(100) NULL,
    telefono_reportante VARCHAR(20) NULL,
    comentarios TEXT NULL,
    lugar VARCHAR(150) NULL,
    lat DECIMAL(10,7) NULL,
    lng DECIMAL(10,7) NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_aviso),
    CONSTRAINT fk_aviso_perro FOREIGN KEY (id_perro) REFERENCES perros(id_perro) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- PARTE 3 (antes: actualizar_parte6.sql)
-- ============================================================

-- El rol "operador" pasa a llamarse "autoridad" (mismo nivel de
-- permisos, nombre más claro para el personal municipal en campo).
UPDATE usuarios SET rol = 'autoridad' WHERE rol = 'operador';

-- Acceso al portal de autoservicio para dueños (login propio,
-- separado del panel de staff). Queda desactivado hasta que un
-- Administrador genere el acceso desde la ficha del dueño.
ALTER TABLE duenos ADD COLUMN usuario_portal VARCHAR(60) NULL UNIQUE AFTER correo;
ALTER TABLE duenos ADD COLUMN contrasena_portal VARCHAR(255) NULL AFTER usuario_portal;
ALTER TABLE duenos ADD COLUMN portal_activo TINYINT(1) NOT NULL DEFAULT 1 AFTER contrasena_portal;

-- Catálogo de veterinarios (gestión completa: alta, edición,
-- baja lógica). Puede vincularse opcionalmente a una cuenta de
-- usuario del sistema (rol = veterinario) para que inicie sesión.
CREATE TABLE IF NOT EXISTS veterinarios (
    id_veterinario INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(120) NOT NULL,
    cedula_profesional VARCHAR(30) NULL,
    especialidad VARCHAR(100) NULL,
    telefono VARCHAR(20) NULL,
    correo VARCHAR(100) NULL,
    id_usuario INT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_veterinario),
    CONSTRAINT fk_veterinario_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Vincula (opcionalmente) las vacunas y el historial veterinario
-- con el catálogo de veterinarios. El campo de texto libre
-- "veterinario" se conserva para no perder los registros ya
-- capturados ni romper compatibilidad.
ALTER TABLE vacunas ADD COLUMN id_veterinario INT NULL AFTER veterinario;
ALTER TABLE vacunas ADD CONSTRAINT fk_vacuna_veterinario FOREIGN KEY (id_veterinario) REFERENCES veterinarios(id_veterinario) ON DELETE SET NULL;

ALTER TABLE historial_veterinario ADD COLUMN id_veterinario INT NULL AFTER veterinario;
ALTER TABLE historial_veterinario ADD CONSTRAINT fk_historial_veterinario FOREIGN KEY (id_veterinario) REFERENCES veterinarios(id_veterinario) ON DELETE SET NULL;
