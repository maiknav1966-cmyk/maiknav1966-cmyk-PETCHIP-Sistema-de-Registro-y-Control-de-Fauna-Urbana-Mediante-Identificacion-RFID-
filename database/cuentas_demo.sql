-- ============================================================
-- PetChip - Cuentas de prueba para los 5 accesos del portal
-- ============================================================
-- Ejecuta esto UNA SOLA VEZ en phpMyAdmin (pestaña SQL), sobre tu
-- base "petchip", DESPUÉS de tener instalado el resto de la BD
-- (schema_completo.sql + actualizar_parte5.sql + actualizar_parte6.sql,
-- o actualizar_todo.sql).
--
-- Crea una cuenta de prueba para cada tarjeta del portal:
--   Administrador  -> admin           / admin123        (ya viene de fábrica)
--   Encargado      -> encargado       / encargado123
--   Veterinario    -> veterinario     / veterinario123
--   Dueño (portal) -> duenoprueba     / dueno123
-- La persona cualquiera (público) no necesita cuenta.
--
-- Cambia estas contraseñas antes de usar el sistema en producción,
-- desde "Mi cuenta" (staff) o desde el perfil del dueño (portal).
-- ============================================================

-- Encargado
INSERT IGNORE INTO usuarios (usuario, contrasena, nombre_completo, rol, activo)
VALUES ('encargado', 'encargado123', 'Encargado de Campo (demo)', 'autoridad', 1);

-- Veterinario (cuenta de acceso al sistema)
INSERT IGNORE INTO usuarios (usuario, contrasena, nombre_completo, rol, activo)
VALUES ('veterinario', 'veterinario123', 'Dr. Veterinario Demo', 'veterinario', 1);

-- Ficha del veterinario en el catálogo, vinculada a la cuenta anterior
INSERT INTO veterinarios (nombre, cedula_profesional, especialidad, telefono, correo, id_usuario, activo)
SELECT 'Dr. Veterinario Demo', 'DEMO-0001', 'Medicina general', '5555555555',
       'veterinario@petchip.demo', u.id_usuario, 1
FROM usuarios u
WHERE u.usuario = 'veterinario'
  AND NOT EXISTS (SELECT 1 FROM veterinarios v WHERE v.id_usuario = u.id_usuario);

-- Dueño de prueba con acceso al portal ya activado
-- (usuario: duenoprueba / contraseña: dueno123, ya cifrada con bcrypt)
INSERT IGNORE INTO duenos
    (nombre, telefono, correo, direccion, colonia, municipio, usuario_portal, contrasena_portal, portal_activo)
VALUES
    ('Dueño de Prueba', '5555555555', 'dueno@petchip.demo', 'Calle Demo 123', 'Centro', 'Ozumba',
     'duenoprueba', '$2b$10$r.1jnxP36Riq6LFxMAm.n.jG9Xgx4uxsi/3mNFH2W.px91Qw5xbCK', 1);

-- Una mascota de ejemplo para que el portal del dueño no se vea vacío
INSERT INTO perros (nombre, especie, raza, edad, sexo, color, peso, tamano, esterilizado, estado, colonia, id_dueno)
SELECT 'Firulais', 'Perro', 'Mestizo / Criollo', 3, 'Macho', 'Café', 12.5, 'Mediano', 1, 'Activo', 'Centro', d.id_dueno
FROM duenos d
WHERE d.usuario_portal = 'duenoprueba'
  AND NOT EXISTS (SELECT 1 FROM perros p WHERE p.id_dueno = d.id_dueno);
