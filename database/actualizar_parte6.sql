-- ============================================================
-- PetChip - Parte 6: Roles ampliados (Autoridad y Dueño),
-- gestión de veterinarios y portal de autoservicio del dueño
-- ============================================================
-- Ejecuta este script UNA SOLA VEZ en phpMyAdmin (pestaña SQL)
-- sobre tu base de datos "petchip", DESPUÉS de tener instaladas
-- las partes anteriores (schema_completo.sql / actualizar_bd.sql /
-- actualizar_parte5.sql).
--
-- Si alguna línea marca error porque la columna o tabla ya existe,
-- ignórala y continúa con la siguiente.
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
