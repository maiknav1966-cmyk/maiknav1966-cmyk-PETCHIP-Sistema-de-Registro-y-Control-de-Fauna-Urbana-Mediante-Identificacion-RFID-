-- ============================================================
-- PETCHIP - Esquema completo de base de datos (version 2.0)
-- Municipio de Ozumba - Control de Fauna Urbana
-- Compatible con la instalacion original. Usa CREATE TABLE IF
-- NOT EXISTS para no romper una base de datos ya existente.
-- Para actualizar una BD que ya tenia las tablas originales,
-- usa el archivo actualizar_bd.sql (agrega columnas nuevas).
-- ============================================================

CREATE DATABASE IF NOT EXISTS petchip;
USE petchip;

-- Usuarios del sistema (login y roles)
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT NOT NULL AUTO_INCREMENT,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    nombre_completo VARCHAR(100) NULL,
    rol VARCHAR(20) NOT NULL DEFAULT 'operador', -- administrador | operador | veterinario
    activo TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dueños / propietarios responsables
CREATE TABLE IF NOT EXISTS duenos (
    id_dueno INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NULL,
    correo VARCHAR(100) NULL,
    direccion VARCHAR(150) NULL,
    colonia VARCHAR(100) NULL,
    municipio VARCHAR(100) NOT NULL DEFAULT 'Ozumba',
    codigo_postal VARCHAR(10) NULL,
    foto VARCHAR(255) NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_dueno)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Animales (se conserva el nombre original de tabla "perros" para
-- no romper el sistema; ahora tambien admite gatos mediante "especie")
CREATE TABLE IF NOT EXISTS perros (
    id_perro INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    especie VARCHAR(20) NOT NULL DEFAULT 'Perro', -- Perro | Gato | Otro
    raza VARCHAR(60) NULL,
    edad INT NULL,
    sexo VARCHAR(10) NULL, -- Macho | Hembra
    color VARCHAR(50) NULL,
    peso DECIMAL(5,2) NULL,
    tamano VARCHAR(20) NULL, -- Pequeño | Mediano | Grande
    fecha_nacimiento DATE NULL,
    esterilizado TINYINT(1) NOT NULL DEFAULT 0,
    estado VARCHAR(20) NOT NULL DEFAULT 'Activo', -- Activo | Perdido | Fallecido | Adoptado
    colonia VARCHAR(100) NULL,
    foto VARCHAR(255) NULL,
    observaciones TEXT NULL,
    id_dueno INT NOT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_perro),
    CONSTRAINT fk_perro_dueno FOREIGN KEY (id_dueno) REFERENCES duenos(id_dueno) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tags RFID
CREATE TABLE IF NOT EXISTS tags_rfid (
    id_tag INT NOT NULL AUTO_INCREMENT,
    codigo_tag VARCHAR(50) NOT NULL UNIQUE,
    fecha_asignacion DATE NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'Activo', -- Activo | Inactivo | Perdido
    id_perro INT NOT NULL,
    PRIMARY KEY (id_tag),
    CONSTRAINT fk_tag_perro FOREIGN KEY (id_perro) REFERENCES perros(id_perro) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Historial de lecturas del Tag (cada vez que se consulta un tag en campo)
CREATE TABLE IF NOT EXISTS lecturas_rfid (
    id_lectura INT NOT NULL AUTO_INCREMENT,
    id_tag INT NOT NULL,
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    ubicacion VARCHAR(150) NULL,
    usuario VARCHAR(50) NULL,
    PRIMARY KEY (id_lectura),
    CONSTRAINT fk_lectura_tag FOREIGN KEY (id_tag) REFERENCES tags_rfid(id_tag) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Vacunas aplicadas a cada animal
CREATE TABLE IF NOT EXISTS vacunas (
    id_vacuna INT NOT NULL AUTO_INCREMENT,
    id_perro INT NOT NULL,
    nombre_vacuna VARCHAR(100) NOT NULL,
    fecha_aplicacion DATE NOT NULL,
    proxima_fecha DATE NULL,
    veterinario VARCHAR(100) NULL,
    PRIMARY KEY (id_vacuna),
    CONSTRAINT fk_vacuna_perro FOREIGN KEY (id_perro) REFERENCES perros(id_perro) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Historial veterinario general (consultas, cirugias, tratamientos)
CREATE TABLE IF NOT EXISTS historial_veterinario (
    id_historial INT NOT NULL AUTO_INCREMENT,
    id_perro INT NOT NULL,
    fecha DATE NOT NULL,
    motivo VARCHAR(150) NOT NULL,
    diagnostico TEXT NULL,
    tratamiento TEXT NULL,
    veterinario VARCHAR(100) NULL,
    PRIMARY KEY (id_historial),
    CONSTRAINT fk_historial_perro FOREIGN KEY (id_perro) REFERENCES perros(id_perro) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Campañas municipales de esterilización
CREATE TABLE IF NOT EXISTS campanas_esterilizacion (
    id_campana INT NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(150) NOT NULL,
    fecha DATE NOT NULL,
    lugar VARCHAR(150) NOT NULL,
    cupo INT NOT NULL DEFAULT 0,
    realizadas INT NOT NULL DEFAULT 0,
    estado VARCHAR(20) NOT NULL DEFAULT 'Programada', -- Programada | En curso | Finalizada | Cancelada
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

-- Reportes de animales perdidos / encontrados
CREATE TABLE IF NOT EXISTS reportes_extravio (
    id_reporte INT NOT NULL AUTO_INCREMENT,
    tipo VARCHAR(15) NOT NULL, -- Perdido | Encontrado
    id_perro INT NULL,
    nombre_animal VARCHAR(100) NULL,
    especie VARCHAR(20) NULL,
    descripcion TEXT NULL,
    lugar VARCHAR(150) NULL,
    fecha DATE NOT NULL,
    contacto VARCHAR(100) NOT NULL,
    foto VARCHAR(255) NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'Activo', -- Activo | Resuelto
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_reporte),
    CONSTRAINT fk_reporte_perro FOREIGN KEY (id_perro) REFERENCES perros(id_perro) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bitácora de acciones del sistema
CREATE TABLE IF NOT EXISTS bitacora (
    id_bitacora INT NOT NULL AUTO_INCREMENT,
    usuario VARCHAR(50) NULL,
    accion VARCHAR(255) NOT NULL,
    modulo VARCHAR(50) NULL,
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_bitacora)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Datos de prueba minimos (usuario administrador por defecto)
INSERT IGNORE INTO usuarios (id_usuario, usuario, contrasena, nombre_completo, rol)
VALUES (1, 'admin', 'admin123', 'Administrador del Sistema', 'administrador');
