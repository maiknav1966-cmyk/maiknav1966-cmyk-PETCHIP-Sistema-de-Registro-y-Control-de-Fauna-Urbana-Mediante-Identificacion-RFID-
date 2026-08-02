-- ============================================================
-- Corrección: el código PHP del módulo de Campañas usa los
-- nombres id_campania, fecha_inicio, ubicacion, meta_animales,
-- pero la tabla se creó con id_campana, fecha, lugar, cupo.
-- Este script renombra las columnas para que coincidan.
-- Ejecutar UNA sola vez en phpMyAdmin (pestaña SQL) sobre tu
-- base de datos de PetChip.
-- ============================================================

-- 1) Quitar temporalmente la llave foránea que apunta a id_campana
ALTER TABLE campanas_atendidos DROP FOREIGN KEY fk_atendido_campana;

-- 2) Renombrar columnas en campanas_esterilizacion
ALTER TABLE campanas_esterilizacion
    CHANGE COLUMN id_campana id_campania INT NOT NULL AUTO_INCREMENT,
    CHANGE COLUMN fecha fecha_inicio DATE NOT NULL,
    CHANGE COLUMN lugar ubicacion VARCHAR(150) NOT NULL,
    CHANGE COLUMN cupo meta_animales INT NOT NULL DEFAULT 0;

-- 3) Volver a crear la llave foránea apuntando al nuevo nombre
ALTER TABLE campanas_atendidos
    ADD CONSTRAINT fk_atendido_campana FOREIGN KEY (id_campana)
    REFERENCES campanas_esterilizacion(id_campania) ON DELETE CASCADE;
