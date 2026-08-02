-- ============================================================
-- PETCHIP - Script de ACTUALIZACION (para BD ya existentes)
-- Ejecuta esto si tu base de datos "petchip" ya tenia las tablas
-- originales (usuarios, duenos, perros, tags_rfid) y solo quieres
-- agregar las columnas y tablas nuevas SIN perder tu informacion.
-- Si un ALTER falla porque la columna ya existe, ignoralo y sigue
-- con el siguiente bloque.
-- ============================================================

USE petchip;

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
