# PetChip 2.0 — Sistema de Control de Fauna Urbana (Ozumba)

Rediseño completo de interfaz + nuevas funcionalidades sobre el proyecto original
"PetChip", manteniendo la lógica y el nombre de las tablas/columnas ya existentes
(`perros`, `duenos`, `tags_rfid`, `usuarios`) para no romper tu base de datos.

## 🚀 Instalación

1. Copia la carpeta `petchip/` dentro de `C:\xampp\htdocs\`.
2. Inicia Apache y MySQL desde el panel de XAMPP.
3. Abre phpMyAdmin y ejecuta **uno** de estos scripts (están en `database/`):
   - **Instalación nueva** (no tienes datos aún): ejecuta `schema_completo.sql`.
   - **Ya tenías la base de datos `petchip` con información**: ejecuta
     `actualizar_bd.sql`. Si alguna línea `ALTER TABLE` marca error porque la
     columna ya existe, ignórala y continúa con la siguiente.
4. Verifica que exista al menos un usuario en la tabla `usuarios`. El script
   nuevo crea uno por defecto:
   - Usuario: `admin`
   - Contraseña: `admin123`
5. Entra a `http://localhost/petchip/`.

## 🗂️ Qué se agregó

- **Diseño**: Bootstrap 5 + Bootstrap Icons + fuentes Poppins/Inter, sidebar fijo,
  modo oscuro, tarjetas, animaciones, todo responsive (celular / tablet / PC).
- **Dashboard**: estadísticas en vivo, gráficas (Chart.js), actividad reciente,
  accesos rápidos.
- **Animales**: fotografía, especie, sexo, color, peso, tamaño, fecha de
  nacimiento, estado, colonia, observaciones, esterilización.
- **Perfil de animal**: línea de tiempo veterinaria, historial de vacunas, y
  código QR generado a partir de su Tag.
- **Dueños**: fotografía, correo, colonia, municipio, código postal, perfil con
  lista de mascotas.
- **RFID**: consulta mejorada que muestra todo el expediente y guarda un
  historial de lecturas con fecha, hora y usuario.
- **Campañas de esterilización**, **reportes de perdidos/encontrados**,
  **bitácora de acciones**, **buscador global** y **estadísticas** con varias
  gráficas (especie, esterilización, colonia, registros mensuales).
- **Reportes**: exportación a Excel (CSV) y vista imprimible para PDF (usa el
  diálogo de impresión del navegador → "Guardar como PDF").

## ✅ Parte 1 entregada — Seguridad y gestión de usuarios (esta actualización)

- **Contraseñas seguras**: `login.php` ahora usa `password_hash()` /
  `password_verify()`. Si tu base de datos aún tenía contraseñas en texto
  plano (como `admin123`), el sistema las detecta, valida el primer login
  normalmente y las re-encripta automáticamente en ese mismo momento — no
  necesitas migrar nada a mano.
- **Roles con permisos reales** (`includes/funciones.php` →
  `permisos_mapa()`, `tiene_permiso()`, `requerir_permiso()`): cada módulo
  del sistema ahora valida el rol antes de mostrar contenido. Si un usuario
  entra a una URL sin permiso, ve un mensaje de "Acceso denegado" y es
  redirigido al Dashboard.
- **Sidebar dinámico**: `includes/header.php` oculta automáticamente las
  secciones que el rol actual no puede usar (Bitácora y Gestión de usuarios
  solo para Administrador, Campañas/Extravío/Estadísticas/Reportes para
  Administrador y Veterinario, etc.). Los botones de editar/eliminar en las
  listas de animales, dueños y tags también se ocultan si el rol no tiene
  permiso.
- **Cuentas activas/inactivas**: si un Administrador desactiva a un usuario,
  su sesión se cierra automáticamente en la siguiente petición.
- **Módulo de gestión de usuarios** (`usuarios/`, solo Administrador):
  crear usuarios, editar nombre/rol, restablecer contraseña de cualquier
  cuenta, y activar/desactivar accesos.
- **Mi cuenta** (`mi_cuenta.php`, disponible para los 3 roles): cualquier
  usuario puede cambiar su propia contraseña desde el menú de su avatar.

## ✅ Parte 2 entregada — Dashboard y diseño (esta actualización)

- **Dashboard completo**: se agregaron los indicadores que faltaban del
  pedido original — **Campañas activas**, **Últimos animales registrados**
  (con foto, dueño y estado) y **Últimas lecturas RFID** (tag, animal,
  ubicación y usuario). El grid de tarjetas pasó a 4 columnas para acomodar
  el nuevo indicador sin saturar la vista.
- **Banner de bienvenida**: el saludo del Dashboard ahora tiene una tarjeta
  con gradiente oscuro (mismo estilo que el login) y muestra el rol del
  usuario en un badge.
- El sistema de diseño (Bootstrap 5 + Poppins/Inter + Bootstrap Icons,
  sidebar fijo, modo oscuro persistente, tarjetas con sombra suave, bordes
  redondeados y animaciones de entrada) ya existía en el proyecto base y se
  mantuvo sin cambios de fondo — solo se extendió para los nuevos bloques.

## ✅ Parte 3 entregada — Fichas de animal y dueño (esta actualización)

- La ficha del animal (`perfil/perfil_perro.php`) ya traía foto, estado,
  color, peso, tamaño, fecha de nacimiento, observaciones, código QR,
  historial de vacunas y línea de tiempo veterinaria — del proyecto base.
  Se reforzó para que **solo Administrador y Veterinario** puedan registrar
  vacunas o eventos veterinarios (antes cualquier rol con sesión podía
  hacerlo enviando el formulario directamente).
- La ficha del dueño (`perfil/perfil_dueno.php`) ya tenía foto, correo,
  dirección, municipio y lista de mascotas. Se agregó la **fecha de
  registro** y una sección de **Historial** con las acciones de la bitácora
  relacionadas a ese dueño (visible solo para Administrador).

## ⚠️ Notas honestas sobre alcance

- El lector RFID físico no está conectado (tal como en el proyecto original);
  el campo de captura en "Consulta RFID" está listo para recibir la lectura de
  un lector USB/Bluetooth que actúe como teclado.
- Las notificaciones de la campana siguen basadas en la bitácora general;
  las alertas específicas (vacunas próximas, campañas próximas, etc.) están
  pendientes para una siguiente entrega.

## ✅ Parte 4 entregada — Configuración, Campañas, Reportes PDF e identidad visual

- **Módulo de Configuración** (`configuracion/configuracion.php`, solo
  Administrador): información del sistema, versión, municipio, estado de la
  base de datos, cantidad de animales/dueños/usuarios, información del
  servidor (PHP y MySQL), datos de sesión, switch de modo oscuro y sección
  "Acerca del sistema" con el logotipo oficial. Se agregó al sidebar.
- **Campañas completo**: nueva tabla `campanas_atendidos` y la página
  `campanas/registrar_atendido.php` para registrar animales atendidos con un
  botón — el contador `realizadas` se incrementa automáticamente y la
  campaña se marca "Finalizada" sola si se llena el cupo. `lista_campanas.php`
  ahora muestra tarjetas resumen (activas, finalizadas, cupos disponibles,
  atendidos histórico) y una gráfica de cupo vs. atendidos por campaña.
- **Reportes en PDF generados en el servidor** con la librería Dompdf
  (ya no se depende de la impresión del navegador): ficha del animal,
  reporte general de animales, reporte de dueños, de campañas, de animales
  perdidos, de esterilizaciones, de vacunas y de Tags RFID. Todos con
  logotipo oficial, encabezado institucional, usuario y fecha de generación,
  tablas con formato profesional, pie de página y numeración automática.
  **Instalación requerida una sola vez:** ejecuta `composer require
  dompdf/dompdf` (o `composer install`, ya incluye `composer.json`) dentro
  de la carpeta raíz del proyecto. Si Dompdf no está instalado, el sistema
  muestra un aviso con instrucciones en vez de un error.
- **Identidad visual unificada**: se eliminó `logo_menu.png` (era un
  duplicado exacto de `logo.png`). Ahora solo existen dos archivos derivados
  del mismo logotipo oficial: `logo.png` (ícono, usado en el sidebar y el
  favicon) y `logo_completo.png` (logo con texto, usado en login, PDFs y
  "Acerca del sistema"). También se quitó un archivo `css` suelto en la raíz
  que era una copia antigua sin usar.

## ✅ Parte 5 entregada — Núcleo de identificación pública (QR) + Perdidos/Encontrados con mapa

- **Instalación requerida una sola vez:** ejecuta `database/actualizar_parte5.sql` en phpMyAdmin
  sobre tu base `petchip` (agrega columnas y tablas nuevas; si una línea marca error porque ya
  existe, ignórala y sigue).
- **Ficha pública sin login** (`publico/ficha.php?t=TOKEN`): cada animal tiene un token único.
  Solo muestra foto, nombre, especie, raza, edad, sexo, tamaño, color y estado — **nunca** el
  nombre, dirección, teléfono o correo del dueño. La información médica (vacunas, esterilización,
  observaciones) solo se muestra si el dueño/staff activa "Mostrar info médica" desde el perfil
  del animal.
- **Botón "Avisar al dueño"**: quien encuentra a la mascota llena un formulario (nombre y teléfono
  opcionales, lugar, comentarios) y, si su navegador lo permite, comparte su ubicación. Esto crea
  una notificación para el dueño y un registro en el historial de escaneos.
- **Código QR real**: en el perfil del animal (solo Administrador/Veterinario) ahora se genera un
  QR que apunta a la ficha pública, con botones para **descargar PNG**, **ver la ficha pública** y
  **regenerar el código** (invalida el anterior, útil si se pierde el control del QR impreso).
- **Historial de escaneos**: cada vez que se escanea el QR (público o desde el sistema) queda
  registrado con fecha, origen y, si se compartió, un mapa (Leaflet/OpenStreetMap) con los puntos
  donde fue visto el animal.
- **Centro de notificaciones**: el perfil de cada dueño (`perfil/perfil_dueno.php`) ahora tiene una
  sección de notificaciones con contador de no leídas.
  ⚠️ *Nota honesta:* el sistema todavía no tiene un login propio para el rol "Dueño" (solo
  Administrador/Veterinario/Operador inician sesión). Por ahora las notificaciones se consultan
  desde el perfil del dueño dentro del panel de staff; un portal de autoservicio para dueños sería
  la siguiente fase natural.
- **Perdidos y encontrados ampliado**: el formulario ahora incluye **recompensa** (opcional) y un
  mapa para marcar la última ubicación conocida. Se agregó `publico/perdidos.php`, una página
  **pública sin login** que lista todos los reportes activos con un mapa interactivo y filtro por
  tipo (Perdido/Encontrado). Enlazada desde el login y desde "Perdidos y encontrados" en el panel.
- **Nota sobre Dompdf**: el código de reportes en PDF ya estaba integrado desde la Parte 4, pero la
  librería (`vendor/`) sigue sin instalarse. Este entorno no tiene acceso a internet para correr
  Composer por ti — corre `composer require dompdf/dompdf` (o `composer install`) una sola vez
  dentro de la carpeta raíz del proyecto, en tu servidor.

## 🔑 Estructura de carpetas nueva

```
petchip/
├── assets/css/style.css      Sistema de diseño
├── assets/js/app.js          Sidebar, modo oscuro, validaciones, QR, mostrar/ocultar contraseña
├── includes/                 header.php, footer.php, funciones.php, header_dueno.php, footer_dueno.php
├── perfil/                   Perfil de animal y de dueño
├── veterinarios/              Catálogo de veterinarios (Administrador)
├── portal/                    Portal de autoservicio del dueño (login_dueno.php)
├── campanas/                  Campañas de esterilización + registro de atendidos
├── configuracion/              Panel de administración (Administrador)
├── extravio/                  Perdidos y encontrados
├── estadisticas/              Gráficas generales
├── bitacora/                  Historial de acciones
├── reportes/pdf/               Motor de reportes imprimibles (window.print, sin Dompdf)
├── uploads/perros, uploads/duenos    Fotografías subidas
└── database/schema_completo.sql, actualizar_bd.sql, actualizar_parte5.sql, actualizar_parte6.sql
```

## 🆕 Parte 6 (esta sesión)

- **Roles ampliados**: Administrador, Veterinario, Autoridad (antes "Operador") y Dueño.
  El rol "Dueño" es exclusivo del **portal de autoservicio** (login separado, nunca
  entra al panel de staff). Corre `database/actualizar_parte6.sql`.
- **Gestión completa de veterinarios** (`veterinarios/`): alta, edición, activar/desactivar,
  vínculo opcional a una cuenta de usuario. Las vacunas e historial médico ahora permiten
  elegir el veterinario del catálogo (o escribirlo libremente, como antes).
- **Portal de autoservicio del dueño** (`login_dueno.php`, `portal/`): el Administrador o
  Veterinario genera el acceso desde la ficha del dueño (botón "Crear acceso al portal").
  El dueño ve ahí sus mascotas, el QR (descargar/imprimir), notificaciones y el historial
  de ubicaciones — sin tocar el panel del municipio.
- **Panel de administración** (`configuracion/configuracion.php`): accesos rápidos a
  Usuarios, Veterinarios, Portal de dueños y Bitácora, más una tabla de roles y permisos.
- **PDF sin Dompdf**: `reportes/pdf/pdf_helper.php` ahora genera una vista HTML imprimible
  con estilos `@media print` y un botón "Imprimir / Guardar como PDF" que llama a
  `window.print()`. Ya no se requiere `composer install` (se eliminó `composer.json`).
- **Rediseño del login** (`login.php`, `login_dueno.php`): glassmorphism sobre el fondo de
  mascotas, animaciones suaves, mostrar/ocultar contraseña, mensajes de error elegantes y
  validaciones visuales — sin tocar la lógica de autenticación existente.

**Para poner todo al día:** ejecuta `database/actualizar_parte6.sql` en phpMyAdmin
(después de tener ya `schema_completo.sql`, `actualizar_bd.sql` y `actualizar_parte5.sql`
instalados). No borra ningún dato existente.
