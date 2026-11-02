# Configuración de Usuario

Documentación técnica del módulo de configuración personal: perfil, contraseña, apariencia, sesiones, historial de actividad, datos personales y comentarios. Volver al [índice de documentación](../README.md).

Las herramientas de servidor que aparecen en la misma pantalla de configuración (mantenimiento, respaldos, logs, caché y entorno) son exclusivas del administrador y se documentan en `admin/configuracion-sistema.md`.

---

## 1. Estado Actual

**Implementado y en funcionamiento** para los tres roles (estudiante, docente y administrador). El módulo se compone de dos pantallas:

| Pantalla | Ruta | Secciones |
|---|---|---|
| Centro de Configuración | `/settings` | Seguridad, Apariencia y Accesibilidad, Sesiones y Dispositivos, Historial de Actividad, Privacidad y Datos, Ayuda y Comentarios, y Configuración del Servidor (solo administrador) |
| Perfil | `/admin/profile` | Información General, Vista Previa del Perfil, Redes y Enlaces, Logros e Insignias, Sesiones y Actividad |

Funciones disponibles:

| Función | Descripción |
|---|---|
| Edición de perfil | Nombre, correo, teléfono, biografía y enlaces sociales. |
| Foto de perfil | Carga, recorte cuadrado con Cropper.js, reajuste sobre la imagen original y eliminación. |
| Cambio de contraseña | Con verificación de la contraseña actual e indicador visual de requisitos. |
| Apariencia | Tamaño de tipografía (14, 16 o 18 px) y modo de alto contraste, guardados en la cuenta. |
| Alternancia rápida de tema | Interruptor claro/oscuro del panel principal, que usa el mismo campo `high_contrast`. |
| Historial de actividad | Últimas 10 acciones del usuario registradas en `user_activity_logs`. |
| Descarga de datos personales | Exportación de los datos del perfil en un archivo JSON. |
| Comentarios | Envío de sugerencias al correo de soporte. |

Estado de las secciones con contenido informativo:

| Sección | Situación |
|---|---|
| Logros e Insignias (perfil) | Muestra valores estáticos de ejemplo (4 cursos completados, 7 insignias, 32 horas de estudio). No se alimenta de la base de datos. |
| Sesiones y Dispositivos | Lee la tabla `user_devices`. El código actual no inserta registros en esa tabla, por lo que la lista aparece vacía y se muestra el mensaje de sesión actual. |
| Centro de Ayuda, Condiciones de Uso y Política de Privacidad | Ventanas modales con contenido estático dentro de la vista. |

---

## 2. Propósito del Componente

Permitir que cada usuario administre su identidad visible, sus credenciales, sus preferencias de accesibilidad y sus datos personales sin intervención del administrador, y ofrecer un canal directo de comentarios hacia el equipo de soporte.

| Rol | Uso |
|---|---|
| **Estudiante** | Mantiene su perfil y foto, ajusta la legibilidad de la plataforma, cambia su contraseña y descarga sus datos. |
| **Docente** | Igual que el estudiante, con el teléfono como dato obligatorio. Sus redes y biografía alimentan su presentación como instructor. |
| **Administrador** | Igual que los demás roles, y además accede a la pestaña de configuración del servidor. |

Objetivos del componente:

- Accesibilidad: tipografía ajustable y alto contraste persistentes por usuario.
- Autonomía: cambios de perfil y contraseña sin soporte.
- Transparencia: historial de actividad propio y exportación de datos personales.

---

## 3. Estructura Interna

### 3.1 Controlador

`app/Http/Controllers/SettingsController.php`. Métodos de uso personal:

| Método | Función |
|---|---|
| `index` | Carga el Centro de Configuración con dispositivos, historial de actividad y, para el administrador, uso de almacenamiento y estado de mantenimiento. |
| `profileView` | Muestra la pantalla de perfil (`admin.profile`). |
| `updateProfile` | Valida y guarda perfil, enlaces y foto. |
| `updatePassword` | Cambia la contraseña. |
| `updateAppearance` | Guarda tamaño de tipografía y alto contraste. |
| `toggleTheme` | Alterna el modo claro/oscuro desde el panel (respuesta JSON). |
| `downloadPersonalData` | Entrega los datos del usuario en JSON. |
| `sendFeedback` | Envía los comentarios al correo de soporte. |
| `logActivity` (privado) | Inserta una fila en `user_activity_logs`. |

El mismo controlador contiene las acciones de servidor del administrador, protegidas con `authorizeAdmin()`.

### 3.2 Modelo y correo

| Elemento | Archivo | Función |
|---|---|---|
| `User` | `app/Models/User.php` | Campos asignables: `name`, `email`, `password`, `phone`, `avatar`, `bio`, `font_size`, `high_contrast`, `github_url`, `linkedin_url`, `portfolio_url`, entre otros. `original_avatar` se asigna directamente en el controlador. |
| `UserFeedbackMail` | `app/Mail/UserFeedbackMail.php` | Correo de comentarios con asunto "Nuevo Comentario / Sugerencia de Usuario - EduNivelam". |
| Plantilla de correo | `resources/views/emails/feedback.blade.php` | Muestra remitente, correo, rol y mensaje. |

### 3.3 Vistas

| Vista | Función |
|---|---|
| `resources/views/settings/index.blade.php` | Centro de Configuración con pestañas tipo *pill* y ventanas modales de ayuda, condiciones y privacidad. |
| `resources/views/admin/profile.blade.php` | Perfil: formulario general con Cropper, vista previa, redes, logros y sesiones. |
| `resources/views/layouts/app.blade.php` | Aplica tipografía y alto contraste del usuario al elemento `body` en cada página. |
| `resources/views/dashboard.blade.php` | Contiene los botones de modo claro y oscuro. |
| `resources/views/layouts/partials/sidebar.blade.php` | Enlaces a Configuración (`settings.index`) y a Perfil (`admin.profile`). |

### 3.4 Assets (Vite)

| Archivo | Función |
|---|---|
| `resources/css/settings/settings.css` | Estilos de las pantallas de configuración y perfil. |
| `resources/js/settings/password-validation.js` | Indicador visual de requisitos de contraseña en el formulario de cambio. |
| `resources/js/settings/appearance-settings.js` | Vista previa inmediata de tamaño de tipografía y alto contraste antes de guardar. |
| `resources/js/settings/server-actions.js` | Reabre la pestaña de servidor tras una acción del administrador. |
| `resources/js/profile-cropper.js` | Recorte de la foto de perfil con Cropper.js (CDN `cropperjs 1.5.13`). |
| `resources/js/dashboard.js` | Envía la alternancia de tema a `/settings/toggle-theme`. |

Los archivos `resources/js/settings/settings.js` y `resources/js/settings/profile-settings.js` están declarados en `vite.config.js`, pero ninguna vista los carga actualmente.

### 3.5 Almacenamiento de archivos

| Ruta (disco `public`) | Contenido |
|---|---|
| `storage/app/public/avatars/` | Fotos de perfil. Archivos `avatar_{uuid}.jpg` (recorte) y `original_{uuid}.{ext}` (carga original). |

El enlace público se obtiene mediante el enlace simbólico de `storage` (`php artisan storage:link`).

---

## 4. Flujo de Funcionamiento

### 4.1 Acceso a la configuración

1. El usuario autenticado abre Configuración desde el menú lateral (`GET /settings`) o Perfil (`GET /admin/profile`).
2. `SettingsController::index` consulta hasta 10 acciones recientes en `user_activity_logs`, los dispositivos del usuario en `user_devices` y, si es administrador, el uso de almacenamiento y el estado de mantenimiento.
3. La pestaña "Configuración del Servidor" solo se renderiza cuando el usuario es administrador.

### 4.2 Actualización de perfil

1. El usuario edita el formulario de Información General (`PUT /settings/profile`) o el de Redes y Enlaces; ambos usan la misma acción.
2. Validaciones:

| Campo | Regla |
|---|---|
| `name` | Opcional, 2 a 100 caracteres, único (ignora al propio usuario). |
| `email` | Opcional, correo válido, máximo 255, único (ignora al propio usuario). |
| `phone` | Obligatorio para docentes, opcional para el resto; máximo 20 caracteres, único. |
| `bio` | Opcional, máximo 500 caracteres. |
| `github_url`, `linkedin_url`, `portfolio_url` | Opcionales, URL válida, máximo 255 caracteres. |
| `avatar` | Opcional, imagen `jpeg`, `png`, `jpg` o `webp`, máximo 2 MB. |

3. Solo se actualizan los campos presentes en la solicitud. El correo se guarda en minúsculas y la biografía se limpia con `strip_tags`.
4. Se procesa la foto (ver 4.3), se guarda el usuario y se registra la acción "Actualización de perfil y avatar".
5. Se vuelve a la pantalla con el mensaje de éxito.

El cambio de correo se guarda directamente; no restablece `email_verified_at`.

### 4.3 Foto de perfil

1. En la pestaña Información General, el usuario selecciona una imagen (`png`, `jpg`, `jpeg` o `webp`).
2. `profile-cropper.js` la carga en Cropper.js con proporción 1:1. Al confirmar el recorte genera una imagen JPEG de 500 × 500 px con calidad 0,9, codificada en base64, y la guarda en el campo oculto `cropped_avatar`.
3. Al enviar el formulario, el controlador:
   - Decodifica la imagen y la guarda como `avatars/avatar_{uuid}.jpg`.
   - Elimina el recorte anterior si era distinto de la imagen original.
   - Si el usuario no tenía `original_avatar`, lo establece con la ruta del nuevo recorte.
4. El botón "Reajustar" reabre el recorte sobre la imagen guardada como original (`original_avatar`).
5. **Eliminación:** la casilla `remove_avatar` borra del disco el `avatar` y el `original_avatar` y deja ambos campos en nulo.

Observaciones técnicas:

- El selector de archivo del formulario no tiene atributo `name`, por lo que el navegador no envía el archivo; se envía únicamente la imagen recortada `cropped_avatar`. La rama del controlador para el campo `avatar` (que valida tipo y tamaño y guarda `original_{uuid}.{ext}`) se aplica a solicitudes que sí incluyan el archivo.
- El contenido de `cropped_avatar` se decodifica y se guarda sin una validación adicional de tipo en el servidor.

### 4.4 Cambio de contraseña

1. El usuario completa el formulario de la pestaña Seguridad (`PUT /settings/password`).
2. `password-validation.js` muestra en tiempo real el cumplimiento de: mínimo 8 caracteres, sin espacios, al menos un número, al menos un símbolo y que no contenga el nombre de usuario.
3. El servidor valida:

| Campo | Regla |
|---|---|
| `current_password` | Obligatoria y debe coincidir con la contraseña vigente (`current_password`). |
| `password` | Obligatoria, mínimo 8 caracteres, sin espacios en blanco, confirmada (`password_confirmation`) y que no contenga el nombre del usuario (sin distinguir mayúsculas). |

4. Se guarda con hash, se registra "Cambio de contraseña de acceso" y se muestra el mensaje de éxito. Las reglas de número y símbolo son ayuda visual del navegador; no se exigen en el servidor.

### 4.5 Apariencia y accesibilidad

1. En la pestaña Apariencia y Accesibilidad el usuario elige:

| Opción | Valores |
|---|---|
| Tamaño de tipografía | `14px` (Normal, predeterminado), `16px` (Grande), `18px` (Extra Grande / Alta Legibilidad) |
| Alto contraste | Activado o desactivado |

2. `appearance-settings.js` aplica la vista previa inmediatamente sobre el `body`.
3. Al guardar (`PUT /settings/appearance`) el servidor valida `font_size` (solo los tres valores permitidos) y `high_contrast` (booleano), actualiza `users.font_size` y `users.high_contrast` y registra la acción "Modificación de preferencias visuales y de accesibilidad".
4. En cada página, `layouts/app.blade.php` aplica `font-size` al `body` y la clase `bg-dark-mode text-light` cuando `high_contrast` es verdadero, o `bg-light text-dark` en caso contrario.

**Alternancia rápida de tema.** Los botones claro/oscuro del panel (`dashboard.blade.php`) envían `POST /settings/toggle-theme` con `high_contrast` en JSON mediante `dashboard.js` (con token CSRF). El controlador actualiza el mismo campo y responde `{"success": true, "high_contrast": ...}`; el navegador cambia las clases del `body` sin recargar.

### 4.6 Sesiones, dispositivos e historial

| Pestaña | Origen de los datos | Contenido |
|---|---|---|
| Sesiones y Dispositivos | Tabla `user_devices` filtrada por usuario, ordenada por `last_active_at` descendente | IP, navegador y última actividad. Si no hay registros, muestra un mensaje. |
| Historial de Actividad | Tabla `user_activity_logs` filtrada por usuario, últimas 10 filas | Fecha y hora, acción e IP. |
| Sesiones y Actividad (Perfil) | La vista de perfil solo recibe el usuario; lista `activeDevices` si existe y, si no, muestra "Sesión actual registrada en este navegador". | Resumen de sesión. |

Acciones del usuario que generan una fila en `user_activity_logs`:

| Acción registrada |
|---|
| Actualización de perfil y avatar |
| Cambio de contraseña de acceso |
| Modificación de preferencias visuales y de accesibilidad |
| Exportación y descarga de datos personales |
| Envío exitoso de comentarios al correo de soporte |

### 4.7 Descarga de datos personales

1. En la pestaña Privacidad y Datos, el usuario pulsa el botón de descarga (`GET /settings/download-data`).
2. El controlador arma un objeto con `perfil` (los atributos del modelo `User` que no están ocultos; `password` y `remember_token` se excluyen), `fecha_exportacion` y `plataforma`.
3. Registra "Exportación y descarga de datos personales" y entrega el archivo `mis-datos-edunivelam-{id}.json` en formato legible y con caracteres UTF-8.

### 4.8 Comentarios al equipo de soporte

1. En la pestaña Ayuda, Soporte y Comentarios, el usuario escribe su mensaje (`POST /settings/feedback`).
2. El servidor valida `feedback_message`: obligatorio y máximo 1000 caracteres.
3. Se envía `UserFeedbackMail` al correo de soporte configurado en el controlador, con nombre, correo y rol del remitente.
4. Si el envío es correcto, se registra la acción y se muestra el agradecimiento; si falla, se muestra un mensaje de error con el detalle de la excepción.

---

## 5. Rutas y Permisos

### 5.1 Rutas

Todas pertenecen al grupo `auth`: requieren sesión iniciada, sin importar el rol.

| Método | URI | Nombre | Controlador | Middleware |
|---|---|---|---|---|
| GET | `/settings` | `settings.index` | `SettingsController@index` | `auth` |
| PUT | `/settings/profile` | `settings.profile.update` | `SettingsController@updateProfile` | `auth` |
| PUT | `/settings/password` | `settings.password.update` | `SettingsController@updatePassword` | `auth` |
| PUT | `/settings/appearance` | `settings.appearance.update` | `SettingsController@updateAppearance` | `auth` |
| POST | `/settings/toggle-theme` | `settings.toggle-theme` | `SettingsController@toggleTheme` | `auth` |
| GET | `/settings/download-data` | `settings.data.download` | `SettingsController@downloadPersonalData` | `auth` |
| POST | `/settings/feedback` | `settings.feedback.send` | `SettingsController@sendFeedback` | `auth` |
| GET | `/admin/profile` | `admin.profile` | `SettingsController@profileView` | `auth` |
| PUT | `/admin/profile` | `admin.profile.update` | `SettingsController@updateProfile` | `auth` |

La ruta `/admin/profile` es la pantalla de perfil de todos los roles, a pesar de su prefijo `admin`.

### 5.2 Permisos

| Capacidad | Estudiante | Docente | Administrador |
|---|:---:|:---:|:---:|
| Editar su perfil, enlaces y foto | Sí | Sí | Sí |
| Teléfono obligatorio | No | Sí | No |
| Cambiar su contraseña | Sí | Sí | Sí |
| Ajustar apariencia y alto contraste | Sí | Sí | Sí |
| Consultar su historial de actividad | Sí | Sí | Sí |
| Descargar sus datos personales | Sí | Sí | Sí |
| Enviar comentarios a soporte | Sí | Sí | Sí |
| Pestaña "Configuración del Servidor" | No | No | Sí |

Reglas de acceso a los datos: todas las acciones operan sobre el usuario autenticado (`Auth::user()`); no reciben un identificador de usuario en la solicitud, por lo que cada persona solo puede consultar y modificar sus propios datos. Las únicas comprobaciones de unicidad que ignoran al propio usuario son las de `name`, `email` y `phone`.

---

## 6. Datos

### 6.1 Campos de `users` utilizados

| Campo | Tipo | Uso en este módulo |
|---|---|---|
| `name` | varchar(255) | Nombre visible (único en la validación de perfil). |
| `email` | varchar(255) | Correo de la cuenta (único). |
| `phone` | varchar(255), nulo | Teléfono (obligatorio para docentes). |
| `bio` | text, nulo | Biografía, máximo 500 caracteres. |
| `avatar` | varchar(255), nulo | Ruta de la foto de perfil mostrada. |
| `original_avatar` | varchar(255), nulo | Ruta de la imagen base para reajustar el recorte. |
| `github_url`, `linkedin_url`, `portfolio_url` | varchar(255), nulos | Enlaces sociales. |
| `font_size` | varchar(20), por defecto `14px` | Tamaño de tipografía (`14px`, `16px`, `18px`). |
| `high_contrast` | boolean, por defecto `false` | Alto contraste / modo oscuro. |
| `password` | varchar(255) | Hash de la contraseña. |

### 6.2 Tabla `user_activity_logs`

| Campo | Tipo | Descripción |
|---|---|---|
| `id` | bigint unsigned | Clave primaria. |
| `user_id` | bigint unsigned | Usuario (`ON DELETE CASCADE`). |
| `action` | varchar(255) | Descripción de la acción. |
| `ip_address` | varchar(45) | IP de origen. |
| `created_at`, `updated_at` | timestamp | Fecha del registro. |

### 6.3 Tabla `user_devices`

| Campo | Tipo | Descripción |
|---|---|---|
| `id` | bigint unsigned | Clave primaria. |
| `user_id` | bigint unsigned | Usuario (`ON DELETE CASCADE`). |
| `ip_address` | varchar(45) | IP del dispositivo. |
| `user_agent` | text | Navegador o cliente. |
| `last_active_at` | timestamp | Última actividad. |
| `created_at`, `updated_at` | timestamp | Fechas del registro. |

### 6.4 Archivos

| Ubicación | Contenido | Límite |
|---|---|---|
| `storage/app/public/avatars/` | Fotos de perfil y originales | Carga directa: 2 MB (`jpeg`, `png`, `jpg`, `webp`). El recorte se genera de 500 × 500 px en JPEG. |

### 6.5 Estructura del archivo de datos personales

Archivo `mis-datos-edunivelam-{id}.json`:

| Clave | Contenido |
|---|---|
| `perfil` | Atributos del usuario (sin `password` ni `remember_token`). |
| `fecha_exportacion` | Fecha y hora de la descarga. |
| `plataforma` | `EduNivelam` |

### 6.6 Almacenamiento en el navegador

| Elemento | Dónde | Uso |
|---|---|---|
| `sidebar_collapsed` | `localStorage` | Recuerda si el menú lateral está colapsado (`dashboard.js`). |
| Token CSRF | Etiqueta `meta csrf-token` | Autoriza la petición de alternancia de tema. |