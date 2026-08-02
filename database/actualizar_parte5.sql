-- ============================================================
-- PetChip - Parte 5: Núcleo de identificación pública (QR) +
-- Mascotas perdidas/encontradas con mapa
-- ============================================================
-- Ejecuta este script UNA SOLA VEZ en phpMyAdmin (pestaña SQL)
-- sobre tu base de datos "petchip", DESPUÉS de tener ya instalado
-- el esquema base (schema_completo.sql / actualizar_bd.sql).
--
-- Si alguna línea marca error porque la columna o tabla ya existe,
-- ignórala y continúa con la siguiente.
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
