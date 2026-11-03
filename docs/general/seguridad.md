# Seguridad

Documentación técnica de los mecanismos de seguridad de EduNivelam. Volver al [índice de documentación](../README.md).

El flujo de inicio de sesión, el bloqueo progresivo, la verificación de correo y la recuperación de contraseña se describen en [`autenticacion.md`](./autenticacion.md). Las tablas asociadas se describen en [`base-datos.md`](./base-datos.md). Este documento reúne el resto de controles transversales.

---

## 1. Estado Actual

**Implementado y en funcionamiento.** La seguridad se organiza en capas que operan en conjunto:

| Capa | Mecanismo | Ubicación |
|---|---|---|
| Transporte y navegador | Cabeceras HTTP de seguridad y Content Security Policy (CSP) | `SecurityHeadersMiddleware` |
| Transporte | HTTPS forzado en producción y confianza en proxies | `AppServiceProvider`, `bootstrap/app.php` |
| Peticiones | Protección CSRF en formularios (middleware web de Laravel) | Framework, directiva `@csrf` en las vistas |
| Abuso y fuerza bruta | Limitación de solicitudes (*rate limiting*) en login, recuperación, contacto y consulta de estado docente | `routes/web.php`, controladores, `AppServiceProvider` |
| Autenticación | Contraseñas con hash, regeneración de sesión, verificación de correo, cierre por inactividad | Ver [`autenticacion.md`](./autenticacion.md) |
| Autorización | `Gate manage-users`, middleware `can:`, `authorizeAdmin()` y `PostPolicy` | `AppServiceProvider`, `SettingsController`, `PostPolicy` |
| Disponibilidad | Modo mantenimiento con acceso exclusivo del administrador | `CheckSystemMaintenance` |
| Datos de entrada | Validación en controladores, `$fillable` en modelos, restricción de tipo y tamaño de imágenes | Controladores y modelos |
| Auditoría | Registro de eventos de acceso y de actividad del usuario, con panel de seguridad | `security_logs`, `user_activity_logs`, `DashboardController::security` |
| Errores | Vistas controladas para 401, 403, 404, 419, 429 y mantenimiento | `resources/views/errors/*`, `bootstrap/app.php` |
| Dependencias | Auditoría de paquetes con `composer audit` y `npm audit` | Carpeta `evidencias/` |

### 1.1 Evidencias de auditoría

La carpeta `evidencias/` del repositorio conserva los resultados de las auditorías realizadas antes y después de la migración a Laravel 13.

| Auditoría | Antes | Después |
|---|---|---|
| `composer audit` | 1 aviso de severidad media (`symfony/mime`) | Sin avisos de seguridad |
| `npm audit` | Avisos de severidad alta y crítica (`follow-redirects`, `shell-quote`) | 0 vulnerabilidades |
| Escaneo OWASP ZAP | 1 alto, 8 medios, 9 bajos, 9 informativos | 3 altos, 8 medios, 10 bajos, 9 informativos |

Alertas de riesgo alto del informe ZAP posterior: Inyección SQL (4 ocurrencias), PHP Version with Known Vulnerabilities (1) y Ruta Transversal (1). Los informes completos están en `evidencias/antes/Report-ZAP-Antes.md` y `evidencias/despues/Report-ZAP-Despues.md`.

Relación de las alertas de riesgo medio y bajo del informe posterior con los controles del código actual:

| Alerta reportada por ZAP | Control relacionado en el código |
|---|---|
| Falta de cabecera Anti-Clickjacking | `X-Frame-Options: SAMEORIGIN` en `SecurityHeadersMiddleware`. |
| Falta encabezado X-Content-Type-Options | `X-Content-Type-Options: nosniff`. |
| Strict-Transport-Security no establecido | `Strict-Transport-Security: max-age=31536000; includeSubDomains`. |
| Divulgación de `X-Powered-By` | El middleware elimina la cabecera `X-Powered-By`. |
| CSP: `unsafe-inline`, `unsafe-eval`, directiva wildcard | La CSP actual incluye `'unsafe-inline'` y `'unsafe-eval'` en `script-src`, `'unsafe-inline'` en `style-src` y `https:` en `img-src` (ver sección 3.2). |
| Cookie sin `HttpOnly` / `SameSite` | `config/session.php`: `http_only = true`, `same_site = lax` (valores por defecto). |
| Falta de atributo de integridad en recursos secundarios | Los recursos de CDN (`cdn.jsdelivr.net`, `cdnjs.cloudflare.com`) se cargan sin atributos SRI. |

---

## 2. Propósito del Componente

Proteger la plataforma, a sus usuarios y sus datos frente a accesos no autorizados, abuso de formularios, ataques web habituales y errores de configuración, y dejar trazabilidad de lo ocurrido.

| Rol | Qué obtiene del componente |
|---|---|
| **Estudiante** | Sesión protegida, cierre automático por inactividad, protección de sus datos personales y descarga de sus propios datos. |
| **Docente** | Acceso condicionado a la aprobación del administrador y a que la cuenta esté activa. |
| **Administrador** | Panel de seguridad con el estado de las últimas 24 horas, control exclusivo de herramientas sensibles (respaldos, mantenimiento, variables de entorno, logs) y bloqueo de acciones de autolesión (no puede quitarse el rol ni desactivar su propia cuenta). |
| **Visitante** | Formularios públicos protegidos contra envíos masivos. |

---

## 3. Estructura Interna

### 3.1 Middleware, políticas y proveedores

| Elemento | Archivo | Función |
|---|---|---|
| `SecurityHeadersMiddleware` | `app/Http/Middleware/SecurityHeadersMiddleware.php` | Agrega las cabeceras de seguridad y la CSP a todas las respuestas (registrado con `$middleware->append` en `bootstrap/app.php`). |
| `CheckSystemMaintenance` | `app/Http/Middleware/CheckSystemMaintenance.php` | Alias `maintenance`. Bloquea el acceso cuando el modo mantenimiento está activo. |
| `trustProxies(at: '*')` | `bootstrap/app.php` | Confía en las cabeceras de proxy inverso para detectar IP y esquema reales. |
| `Gate manage-users` | `app/Providers/AppServiceProvider.php` | Autoriza únicamente a usuarios con `role === 'admin'`. |
| `RateLimiter login` | `app/Providers/AppServiceProvider.php` | Limitador con nombre: 30 solicitudes por minuto por IP. |
| `URL::forceScheme('https')` | `app/Providers/AppServiceProvider.php` | Fuerza HTTPS cuando `APP_ENV=production`. |
| `PostPolicy` | `app/Policies/PostPolicy.php` | Solo el autor puede actualizar o eliminar una publicación. |
| Manejo de excepciones | `bootstrap/app.php` | `AuthenticationException` devuelve la vista 401; `MethodNotAllowedHttpException` redirige al inicio con un mensaje de error. Ambas se aplican a solicitudes que no esperan JSON. |
| `authorizeAdmin()` | `SettingsController` (método privado) | Verificación adicional de rol `admin`; devuelve HTTP 403 si no se cumple. |

### 3.2 Content Security Policy y cabeceras

Las cabeceras que establece `SecurityHeadersMiddleware`:

| Cabecera | Valor |
|---|---|
| `X-Powered-By` | Eliminada |
| `X-Frame-Options` | `SAMEORIGIN` |
| `X-Content-Type-Options` | `nosniff` |
| `X-XSS-Protection` | `1; mode=block` |
| `Strict-Transport-Security` | `max-age=31536000; includeSubDomains` |
| `Content-Security-Policy` | Ver tabla siguiente |

Directivas de la CSP:

| Directiva | Orígenes permitidos |
|---|---|
| `default-src` | `'self'` |
| `script-src` | `'self'`, `'unsafe-inline'`, `'unsafe-eval'`, `cdn.jsdelivr.net`, `cdnjs.cloudflare.com`, `challenges.cloudflare.com` |
| `style-src` | `'self'`, `'unsafe-inline'`, `cdn.jsdelivr.net`, `cdnjs.cloudflare.com`, `fonts.googleapis.com` |
| `img-src` | `'self'`, `data:`, `blob:`, `https:` |
| `font-src` | `'self'`, `data:`, `cdn.jsdelivr.net`, `cdnjs.cloudflare.com`, `fonts.gstatic.com` |
| `connect-src` | `'self'` |
| `frame-src` | `'self'`, `challenges.cloudflare.com` |
| `object-src` | `'none'` |
| `base-uri` | `'self'` |
| `form-action` | `'self'` |

Fuera de producción, la CSP añade los orígenes del servidor de desarrollo de Vite (`localhost:5173` y `[::1]:5173`, incluyendo WebSocket) a `script-src`, `style-src` y `connect-src`.

### 3.3 Controladores con funciones de seguridad

| Controlador | Función |
|---|---|
| `Auth/AuthController` | Login, bloqueo progresivo, registro, recuperación y registro de eventos en `security_logs`. |
| `Auth/GoogleAuthController` | Acceso con Google (OAuth 2.0) con validación de correo verificado y de estado de la cuenta. |
| `Auth/TeacherStatusController` | Consulta pública del estado de una solicitud docente, limitada por intentos. |
| `Admin/UserController` | Cambio de rol, activación y desactivación, eliminación, con protecciones sobre la cuenta propia. |
| `Admin/AdminTeacherController` | Aprobación y rechazo de docentes. |
| `DashboardController` | Panel de seguridad (`security`) y limpieza de caché del sistema. |
| `SettingsController` | Cambio de contraseña, avatar, respaldo, mantenimiento, logs, caché y variables de entorno. |
| `ContactController` | Formulario público de contacto con límite por IP. |

### 3.4 Vistas y assets

| Archivo | Función |
|---|---|
| `resources/views/errors/401.blade.php` | Acceso no autenticado. |
| `resources/views/errors/403.blade.php` | Acción no autorizada. |
| `resources/views/errors/404.blade.php` | Recurso no encontrado. |
| `resources/views/errors/419.blade.php` | Sesión o token CSRF expirado. |
| `resources/views/errors/429.blade.php` | Demasiadas solicitudes. |
| `resources/views/errors/maintenance.blade.php` | Sistema en mantenimiento (HTTP 503). |
| `resources/views/errors/google-unregistered.blade.php` | Error de acceso con Google. |
| `resources/views/admin/security.blade.php` | Panel de seguridad del administrador. |
| `resources/js/inactivity.js` | Cierre de sesión por inactividad (5 minutos). |

### 3.5 Configuración y variables de entorno

| Archivo | Valores relevantes |
|---|---|
| `config/session.php` | Driver `database`; `http_only` por defecto `true`; `same_site` por defecto `lax`; `secure` según `SESSION_SECURE_COOKIE`; `encrypt` según `SESSION_ENCRYPT` (por defecto `false`); duración según `SESSION_LIFETIME`. |
| `config/services.php` | Bloque `turnstile` con `site_key` y `secret_key`; bloque `google`. |
| `.env.example` | `BCRYPT_ROUNDS=12`, `SESSION_LIFETIME=120`, `SESSION_ENCRYPT=false`. |

| Variable | Uso en seguridad |
|---|---|
| `APP_ENV` | Con valor `production` fuerza HTTPS y retira los orígenes de desarrollo de la CSP. |
| `APP_DEBUG` | Debe estar en `false` en producción. El administrador puede cambiarla desde el panel de sistema. |
| `APP_KEY` | Clave de cifrado de cookies y sesiones firmadas. |
| `BCRYPT_ROUNDS` | Costo del hash de contraseñas (12). |
| `SESSION_SECURE_COOKIE`, `SESSION_SAME_SITE`, `SESSION_HTTP_ONLY` | Atributos de la cookie de sesión. |
| `TURNSTILE_SITE_KEY`, `TURNSTILE_SECRET_KEY` | Claves de Cloudflare Turnstile. |
| `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI` | Credenciales OAuth. |

---

## 4. Flujo de Funcionamiento

### 4.1 Recorrido de una petición

1. La petición llega al servidor; `trustProxies` interpreta la IP y el esquema reales.
2. Se ejecuta el middleware del grupo web de Laravel: cookies, sesión y verificación del token CSRF en métodos `POST`, `PUT`, `PATCH` y `DELETE`.
3. Se evalúa el middleware de la ruta, según corresponda: `guest`, `auth`, `verified`, `signed`, `throttle`, `can:manage-users` y `maintenance`.
4. El controlador valida los datos de entrada y aplica las reglas de negocio y de autorización.
5. `SecurityHeadersMiddleware` agrega a la respuesta las cabeceras de seguridad y la CSP.
6. Si ocurre una excepción de autenticación o de método no permitido, `bootstrap/app.php` entrega la vista o redirección controlada.

### 4.2 Protección CSRF

Las vistas incluyen el token con `@csrf` y el middleware web de Laravel rechaza las solicitudes sin token válido con HTTP 419, que se presenta en la vista `errors.419`. No existen rutas excluidas de la verificación en el proyecto.

### 4.3 Limitación de solicitudes

| Punto | Mecanismo | Límite |
|---|---|---|
| `POST /login` | Limitador con nombre `login` | 30 solicitudes por minuto por IP |
| Intentos fallidos de login | `RateLimiter` en `AuthController` (clave correo + IP) | 5 intentos y bloqueo progresivo de 5 a 60 minutos |
| `GET /auth/google` y callback | `throttle:10,1` | 10 por minuto |
| `POST /forgot-password` | `throttle:3,1` | 3 por minuto |
| `POST /verify-code` | `throttle:5,1` | 5 por minuto |
| `POST /reset-password` | `throttle:5,1` | 5 por minuto |
| `POST /email/verification-notification` | `throttle:6,1` | 6 por minuto |
| `POST /teacher/status` | `throttle:10,1` y `RateLimiter` interno (clave correo + IP) | 10 por minuto por ruta y 5 consultas fallidas en 15 minutos por combinación correo + IP |
| `POST /contact` | `RateLimiter` en `ContactController` (clave por IP) | 3 envíos por cada 15 minutos |

Cuando se supera un límite de `throttle`, Laravel responde con HTTP 429, que se muestra con la vista `errors.429`.

### 4.4 Autorización

La autorización se aplica en tres niveles:

1. **Ruta:** `can:manage-users` restringe a administradores todas las rutas de noticias, banners, usuarios, sistema, panel y seguridad.
2. **Controlador:** `SettingsController::authorizeAdmin()` repite la verificación de rol `admin` en acciones sensibles (respaldo, optimización, logs, caché granular y variables de entorno). Responde con HTTP 403.
3. **Recurso:** `PostPolicy` limita la edición y eliminación de publicaciones a su autor mediante `Gate::authorize`. El controlador de publicaciones exige sesión (`auth`) en todas las acciones excepto `index` y `show`.

Protecciones sobre la cuenta propia en `Admin/UserController`:

| Acción | Restricción |
|---|---|
| Cambiar el rol | Un administrador no puede quitarse a sí mismo el rol `admin`. |
| Activar o desactivar | No se puede modificar el estado de la propia cuenta. |
| Rol asignado | Debe pertenecer a `User::ROLES` (`admin`, `teacher`, `student`, `user`). |

### 4.5 Modo mantenimiento

1. El administrador activa o desactiva el modo desde el panel de sistema (`POST /admin/system/maintenance`).
2. El estado se guarda en caché bajo la clave `system_maintenance`.
3. El middleware `maintenance` se aplica a los paneles de estudiante y docente. Si el modo está activo:
   - Un usuario autenticado con rol `admin` continúa normalmente.
   - Una solicitud que espera JSON recibe `{"error": "..."}` con HTTP 503.
   - Cualquier otra recibe la vista `errors.maintenance` con HTTP 503.

### 4.6 Validación de entrada y archivos

| Ámbito | Controles |
|---|---|
| Formularios | Validación con reglas `required`, `string`, `max`, `email`, `regex`, `unique`, `in`, `digits` y `confirmed`, según el formulario. |
| Contraseñas | Mínimo 8 caracteres y sin espacios en blanco. Al cambiarla desde Configuración se exige la contraseña actual y que la nueva no contenga el nombre del usuario. |
| Asignación masiva | Los modelos definen `$fillable`; los campos sensibles como `google_id`, `reset_code` o `email_verified_at` se asignan de forma explícita en el código. |
| Avatar de perfil | Imagen `jpeg`, `png`, `jpg` o `webp`, máximo 2 MB; el nombre del archivo se genera con UUID. |
| Imagen de banner | Imagen `jpeg`, `png`, `jpg` o `webp`, máximo 2 MB; solo se acepta en las páginas cuyo campo `image_path` está permitido. |
| Imagen de noticia | Se recibe recortada como cadena base64 y se guarda con nombre generado (`news_{uuid}.jpg`) en el disco público. |
| Campos de perfil | `bio` máximo 500 caracteres; `github_url`, `linkedin_url` y `portfolio_url` deben ser URL válidas. |
| Formulario de contacto | Nombre hasta 100, correo hasta 255, asunto hasta 120 y mensaje hasta 1000 caracteres. |
| Salida | Las vistas Blade escapan el contenido con `{{ }}`. |

### 4.7 Cloudflare Turnstile

El formulario público de contacto (`page/contact.blade.php`) incluye el widget de Cloudflare Turnstile, y la CSP permite el origen `challenges.cloudflare.com` para scripts y marcos. Las claves se leen desde `config/services.php`. En `ContactController::store`, el control efectivo de envío es el límite de 3 mensajes por 15 minutos por IP; este controlador no consulta el servicio de verificación del token.

### 4.8 Registro de eventos y panel de seguridad

1. Cada acceso relevante se inserta en `security_logs` (ver tipos de evento en [`autenticacion.md`](./autenticacion.md)).
2. Las acciones sensibles realizadas desde Configuración (cambio de contraseña, respaldo, optimización, variables de entorno, entre otras) se registran en `user_activity_logs` mediante `SettingsController::logActivity`, con usuario, acción e IP.
3. El administrador consulta `GET /admin/security`. El controlador lee todos los registros de `security_logs` de las últimas 24 horas y calcula el estado:

| Eventos en las últimas 24 horas | Estado mostrado |
|---|---|
| 0 | `secure` |
| De 1 a 4 | `warning` |
| 5 o más | `danger` |

El conteo incluye todos los eventos registrados en ese período (accesos exitosos, fallidos y de cuentas inactivas).

### 4.9 Herramientas sensibles del administrador

| Herramienta | Ruta | Protección y comportamiento |
|---|---|---|
| Variables de entorno | `POST /admin/system/env/update` | Solo `APP_NAME`, `APP_DEBUG` (`true` o `false`) y `MAIL_MAILER` (`smtp`, `log`, `mailgun`, `ses`). Ejecuta `config:clear` y registra la acción. |
| Respaldo SQL | `POST /admin/system/backup` | Exporta los datos de todas las tablas, incluidos los hashes de contraseña, por lo que el archivo debe tratarse como información sensible. |
| Logs del sistema | `POST /admin/system/logs/view` y `logs/clear` | Lee o vacía `storage/logs/laravel.log`. |
| Caché | `POST /admin/system/cache/inspect`, `cache/granular` y `clear-cache` | Inspección y limpieza por tipo. |
| Optimización de la base de datos | `POST /admin/system/db/optimize` | Ver [`base-datos.md`](./base-datos.md). |
| Descarga de datos personales | `GET /settings/download-data` | Disponible para el usuario autenticado, solo sobre sus propios datos. |

### 4.10 Manejo de errores

| Código | Vista | Cuándo se muestra |
|---|---|---|
| 401 | `errors.401` | Acceso a una ruta protegida sin sesión. |
| 403 | `errors.403` | Acción no autorizada (por ejemplo, `can:manage-users` o `authorizeAdmin`). |
| 404 | `errors.404` | Recurso inexistente. |
| 419 | `errors.419` | Token CSRF inválido o sesión expirada. |
| 429 | `errors.429` | Límite de solicitudes superado. |
| 503 | `errors.maintenance` | Modo mantenimiento activo. |

Además, un método HTTP no permitido en una ruta redirige al inicio con el mensaje "El método de acceso no es válido para esta acción".

---

## 5. Rutas y Permisos

### 5.1 Niveles de protección de las rutas

| Nivel | Middleware | Rutas representativas |
|---|---|---|
| Público | Ninguno | `/`, `/about`, `/news`, `/courses`, `/instructors`, `/contact` (el envío tiene límite interno) |
| Solo invitados | `guest` (más `throttle` donde aplica) | `/login`, `/register`, `/register/teacher`, `/auth/google`, `/forgot-password`, `/verify-code`, `/reset-password`, `/teacher/status` |
| Autenticado | `auth` | `/logout`, `/session-expired`, `/email/verify*`, `/check-session-status`, `/settings/*` |
| Autenticado con mantenimiento | `auth`, `maintenance` | `/student/dashboard`, `/teacher/dashboard` |
| Autenticado y verificado | `auth`, `verified` | `/admin/correos/*`, `/admin/check-imap`, `/admin/teachers/*` |
| Administrador | `auth`, `verified`, `can:manage-users` | `/dashboard`, `/admin/security`, `/admin/users*`, `/admin/news*`, `/admin/banners*`, `/admin/system/*` |
| Recurso con política | Controlador (`auth` excepto `index` y `show`) y `PostPolicy` | `/posts*` |

### 5.2 Rutas de seguridad y administración

| Método | URI | Nombre | Controlador | Middleware |
|---|---|---|---|---|
| GET | `/admin/security` | `admin.security` | `DashboardController@security` | `auth`, `verified`, `can:manage-users` |
| PUT | `/admin/users/{id}/role` | `admin.users.updateRole` | `Admin\UserController@updateRole` | `can:manage-users` |
| DELETE | `/admin/users/{id}` | `admin.users.destroy` | `Admin\UserController@destroy` | `can:manage-users` |
| DELETE | `/admin/users/permanent/{id}` | `admin.users.forceDelete` | `Admin\UserController@forceDelete` | `can:manage-users` |
| POST | `/admin/system/maintenance` | `admin.system.maintenance` | `SettingsController@toggleMaintenance` | `can:manage-users` |
| POST | `/admin/system/backup` | `admin.system.backup` | `SettingsController@generateBackup` | `can:manage-users`, `authorizeAdmin()` |
| POST | `/admin/system/db/optimize` | `admin.system.db.optimize` | `SettingsController@optimizeDatabase` | `can:manage-users`, `authorizeAdmin()` |
| POST | `/admin/system/env/update` | `admin.system.env.update` | `SettingsController@updateEnv` | `can:manage-users`, `authorizeAdmin()` |
| POST | `/admin/system/logs/view` | `admin.system.logs.view` | `SettingsController@viewLogs` | `can:manage-users` |
| POST | `/admin/system/logs/clear` | `admin.system.logs.clear` | `SettingsController@clearLogs` | `can:manage-users`, `authorizeAdmin()` |
| POST | `/admin/system/cache/inspect` | `admin.system.cache.inspect` | `SettingsController@inspectCache` | `can:manage-users` |
| POST | `/admin/system/cache/granular` | `admin.system.cache.granular` | `SettingsController@clearGranularCache` | `can:manage-users` |
| POST | `/admin/system/clear-cache` | `admin.system.clearcache` | `SettingsController@clearSystemCache` | `can:manage-users` |
| POST | `/admin/system/test-email` y `/admin/system/smtp/test` | `admin.system.testemail`, `admin.system.smtp.test` | `SettingsController` | `can:manage-users` |

### 5.3 Matriz de permisos por rol

| Capacidad | Visitante | Estudiante | Docente | Administrador |
|---|:---:|:---:|:---:|:---:|
| Consultar páginas públicas y enviar contacto | Sí | Sí | Sí | Sí |
| Iniciar sesión | Sí | Sí | Solo si está aprobado y activo | Sí |
| Consultar el estado de su solicitud docente (ruta solo para invitados) | Sí | No aplica | No aplica | No aplica |
| Configuración de perfil, contraseña y apariencia | No | Sí | Sí | Sí |
| Descargar sus datos personales | No | Sí | Sí | Sí |
| Panel de estudiante | No | Sí | Sí (sin validación de rol en la ruta) | Sí (sin validación de rol en la ruta) |
| Panel de docente | No | Sí (sin validación de rol en la ruta) | Sí | Sí (sin validación de rol en la ruta) |
| Aprobar o rechazar docentes, bandeja de correos | No | Sesión verificada | Sesión verificada | Sí |
| Gestionar usuarios, noticias y banners | No | No | No | Sí |
| Panel de seguridad, respaldos, mantenimiento, logs y entorno | No | No | No | Sí |
| Acceso durante el modo mantenimiento | No | No | No | Sí |

En las filas marcadas "Sesión verificada" y "sin validación de rol en la ruta", el control efectivo de la ruta es `auth` y `verified` (o `auth` y `maintenance`), sin `can:manage-users`. `Admin/AdminTeacherController` y `ReceivedEmailController` no realizan una verificación adicional de rol dentro del controlador.

---

## 6. Datos

### 6.1 Tabla `security_logs`

| Campo | Tipo | Descripción |
|---|---|---|
| `id` | bigint unsigned | Clave primaria. |
| `user_id` | bigint unsigned, nulo | Usuario asociado; pasa a nulo si el usuario se elimina (`ON DELETE SET NULL`). |
| `email` | varchar(255), nulo | Correo utilizado en el intento. |
| `ip_address` | varchar(255), nulo | IP de origen. |
| `status` | varchar(255) | `success` o `failed`. |
| `event_type` | varchar(255) | `successful_login`, `failed_login`, `inactive_account_access`. |
| `description` | text | Descripción del evento. |
| `user_agent` | text, nulo | Navegador o cliente. |
| `created_at`, `updated_at` | timestamp | Índice compuesto (`created_at`, `ip_address`) para consultas del panel. |

### 6.2 Tabla `user_activity_logs`

| Campo | Tipo | Descripción |
|---|---|---|
| `id` | bigint unsigned | Clave primaria. |
| `user_id` | bigint unsigned | Usuario que realizó la acción (`ON DELETE CASCADE`). |
| `action` | varchar(255) | Descripción de la acción (por ejemplo, "Cambio de contraseña de acceso"). |
| `ip_address` | varchar(45) | IP de origen. |
| `created_at`, `updated_at` | timestamp | Fecha del registro. |

### 6.3 Campos de `users` con función de seguridad

| Campo | Función |
|---|---|
| `password` | Hash de la contraseña (cast `hashed`, `BCRYPT_ROUNDS=12`). |
| `role` | Rol para la autorización (`admin`, `teacher`, `student`, `user`). |
| `is_active` | Cuenta habilitada; las desactivadas no pueden iniciar sesión. |
| `is_approved` | Aprobación del docente. |
| `email_verified_at` | Verificación del correo electrónico. |
| `reset_code`, `reset_code_expires_at` | Código de recuperación y su vencimiento (15 minutos). |
| `remember_token` | Token de "recordarme". |
| `google_id` | Vinculación con la cuenta de Google. |

### 6.4 Datos transitorios de seguridad

| Elemento | Almacenamiento | Contenido y vigencia |
|---|---|---|
| Contadores de intentos de login | Caché (`cache`) | Intentos por correo e IP; se limpian al iniciar sesión correctamente. |
| `lockout_count_` (más clave de control) | Caché | Número de bloqueos consecutivos; 24 horas. |
| Intentos de consulta de estado docente | Caché | Clave `teacher-status` con correo e IP; 15 minutos. |
| Intentos de contacto | Caché | Clave `public-contact-` con IP; 15 minutos. |
| `system_maintenance` | Caché | Indicador del modo mantenimiento. |
| Sesiones | Tabla `sessions` | Una fila por sesión activa. |
| Cookies `saved_email`, `remember_checked` | Navegador | 30 días; la cookie `saved_password` se elimina siempre. |

### 6.5 Archivos de registro

| Archivo | Contenido |
|---|---|
| `storage/logs/laravel.log` | Registro de la aplicación; el administrador puede consultarlo y vaciarlo desde el panel de sistema. |
| `.env` | Configuración sensible; solo `APP_NAME`, `APP_DEBUG` y `MAIL_MAILER` son editables desde la interfaz. |