# Autenticación

Documentación técnica del módulo de autenticación de EduNivelam. Volver al [índice de documentación](../README.md).

---

## 1. Estado Actual

**Implementado y en funcionamiento.** El módulo cubre:

- Inicio de sesión con correo y contraseña, con opción "recordarme".
- Bloqueo progresivo por intentos fallidos de acceso.
- Registro de estudiantes con verificación obligatoria de correo electrónico.
- Registro de docentes con aprobación manual del administrador.
- Inicio de sesión automático mediante Google (OAuth 2.0).
- Recuperación de contraseña mediante código numérico de 6 dígitos enviado por correo.
- Cierre de sesión manual y cierre automático por inactividad.
- Registro de eventos de acceso en la tabla `security_logs`.

| Característica | Valor |
|---|---|
| Guard de autenticación | `web` (sesión) |
| Proveedor de usuarios | Eloquent, modelo `App\Models\User` |
| Driver de sesión | `database` (tabla `sessions`) |
| Verificación de correo | Contrato `MustVerifyEmail` en el modelo `User` |
| Paquete de OAuth | `laravel/socialite` |
| Roles definidos | `admin`, `teacher`, `student`, `user` |
| Rol asignado por defecto | `student` |

---

## 2. Propósito del Componente

Gestionar el acceso seguro de los tres tipos de usuario de la plataforma:

| Rol | Qué resuelve el módulo |
|---|---|
| **Estudiante** | Autoregistro, verificación de correo, acceso con credenciales o con Google y recuperación de contraseña. |
| **Docente** | Solicitud de registro que debe ser aprobada por un administrador; recibe sus credenciales por correo una vez aprobado. |
| **Administrador** | Recibe las solicitudes de docentes (ver [`admin/gestion-usuarios.md`](../admin/gestion-usuarios.md)) y consulta los registros de acceso. |

Objetivos de seguridad del módulo:

- Garantizar que los correos registrados sean válidos mediante verificación.
- Evitar la acumulación de cuentas no verificadas.
- Limitar los ataques de fuerza bruta mediante bloqueo progresivo.
- Impedir el acceso de cuentas desactivadas o de docentes no aprobados.
- Mantener trazabilidad de los accesos exitosos y fallidos.

---

## 3. Estructura Interna

### 3.1 Controladores

| Archivo | Responsabilidad |
|---|---|
| `app/Http/Controllers/Auth/AuthController.php` | Login, registro de estudiante, registro de docente, logout y recuperación de contraseña. |
| `app/Http/Controllers/Auth/GoogleAuthController.php` | Redirección a Google y procesamiento del callback OAuth. |
| `app/Http/Controllers/Admin/AdminTeacherController.php` | Aprobación o rechazo de solicitudes de docentes. |

Métodos de `AuthController`:

| Método | Función |
|---|---|
| `showLogin` / `login` | Muestra el formulario y procesa el inicio de sesión. |
| `showRegister` / `register` | Muestra el formulario y registra estudiantes. |
| `showTeacherRegister` / `registerTeacher` | Muestra el formulario y registra solicitudes de docentes. |
| `logout` | Cierra la sesión. |
| `showLinkRequestForm` / `sendResetCode` | Solicita y envía el código de recuperación. |
| `showVerifyForm` / `verifyCode` | Valida el código recibido. |
| `showResetForm` / `updatePassword` | Establece la nueva contraseña. |
| `securityLog` (privado) | Inserta eventos en `security_logs`. |

Constantes de bloqueo definidas en `AuthController`:

| Constante | Valor | Significado |
|---|---|---|
| `MAX_ATTEMPTS` | 5 | Intentos fallidos permitidos antes del bloqueo. |
| `BASE_LOCKOUT` | 300 s | Duración base del bloqueo (5 minutos). |
| `MAX_LOCKOUT` | 3600 s | Duración máxima del bloqueo (1 hora). |

### 3.2 Modelo

`app/Models/User.php`

- Extiende `Authenticatable` e implementa `MustVerifyEmail`.
- Constante `ROLES = ['admin', 'teacher', 'student', 'user']`.
- Cast `password => hashed`, `is_active => boolean`, `is_approved => boolean`, `email_verified_at => datetime`.
- Atributos ocultos en serialización: `password`, `remember_token`.

### 3.3 Form Request

`app/Http/Requests/RegisterUserRequest.php` define reglas de registro: `name` único (2 a 100 caracteres), `email` único, y `password` confirmada, sin espacios, con mínimo 8 caracteres, letras, mayúsculas y minúsculas, y números. Actualmente el método `AuthController::register` aplica su propia validación en línea (ver sección 4.2).

### 3.4 Middleware y proveedores

| Elemento | Ubicación | Función |
|---|---|---|
| `guest` | Laravel | Restringe login, registro y recuperación a usuarios no autenticados. |
| `auth` | Laravel | Protege logout, verificación de correo y rutas internas. |
| `signed` | Laravel | Valida la firma del enlace de verificación de correo. |
| `throttle:login` | `AppServiceProvider` | Limitador con nombre: 30 solicitudes por minuto por IP. |
| `throttle:N,M` | `routes/web.php` | Límites por ruta (ver sección 5). |
| `maintenance` | `CheckSystemMaintenance` | Aplicado a los paneles de estudiante y docente. |
| `SecurityHeadersMiddleware` | `bootstrap/app.php` | Cabeceras de seguridad en todas las respuestas. |
| Manejo de excepciones | `bootstrap/app.php` | `AuthenticationException` muestra la vista `errors.401`; `MethodNotAllowedHttpException` redirige al inicio con mensaje de error. |

### 3.5 Vistas

| Vista | Uso |
|---|---|
| `auth/login.blade.php` | Formulario de acceso, modal de bloqueo y botón de Google. |
| `auth/register.blade.php` | Registro de estudiante. |
| `auth/register-teacher.blade.php` | Solicitud de registro de docente. |
| `auth/teacher-pending.blade.php` | Confirmación de solicitud docente en revisión. |
| `auth/verify-email.blade.php` | Pantalla de espera de verificación de correo. |
| `auth/forgot-password.blade.php` | Solicitud del código de recuperación. |
| `auth/verify-code.blade.php` | Ingreso del código de 6 dígitos. |
| `auth/reset-password.blade.php` | Definición de la nueva contraseña. |
| `errors/401.blade.php` | Acceso no autenticado. |
| `errors/419.blade.php` | Sesión expirada. |

### 3.6 Assets (Vite)

| Archivo | Función |
|---|---|
| `resources/css/login.css`, `resources/js/login.js` | Estilos del login, contador regresivo y modal de bloqueo. |
| `resources/css/register.css`, `resources/js/register.js` | Estilos de registro, indicador de fortaleza de contraseña y selector de código de país (`intl-tel-input`, cargado desde CDN). |
| `resources/css/auth-pages.css`, `resources/js/auth-pages.js` | Pantallas de verificación y recuperación: mostrar u ocultar contraseña, entrada solo numérica del código, reglas visuales de contraseña, bloqueo de doble envío (`form.js-once`) y sondeo de verificación. |
| `resources/js/inactivity.js` | Cierre de sesión por inactividad (importado desde `resources/js/app.js`). |
| `resources/js/logout-animation.js`, `resources/css/logout-animation.css` | Animación al cerrar sesión (incluida desde el sidebar). |
| `public/animations/session-expired.json` | Animación de la vista 419. |

### 3.7 Configuración y variables de entorno

| Archivo | Contenido relevante |
|---|---|
| `config/auth.php` | Guard `web` con driver `session`; proveedor `eloquent` sobre `User`. |
| `config/session.php` | Driver `database`; duración definida por `SESSION_LIFETIME`. |
| `config/services.php` | Bloque `google` con `client_id`, `client_secret` y `redirect`. |
| `config/mail.php` | Mailer para códigos de recuperación y credenciales de docentes. |

Variables de entorno utilizadas por el módulo:

| Variable | Uso |
|---|---|
| `GOOGLE_CLIENT_ID` | Identificador del cliente OAuth de Google. |
| `GOOGLE_CLIENT_SECRET` | Secreto del cliente OAuth de Google. |
| `GOOGLE_REDIRECT_URI` | URL de retorno (`/auth/google/callback`). |
| `SESSION_DRIVER`, `SESSION_LIFETIME` | Almacenamiento y duración de la sesión. |
| `MAIL_*` | Configuración de envío de correo (SMTP). |

---

## 4. Flujo de Funcionamiento

### 4.1 Inicio de sesión

1. El usuario accede a `GET /login` (solo invitados). La vista calcula con `RateLimiter` si la combinación correo + IP está bloqueada y, de ser así, muestra el modal con cuenta regresiva.
2. Envía el formulario a `POST /login`, protegido por `throttle:login`.
3. El controlador normaliza el correo y construye la clave de control formada por correo e IP (`correo|IP`).
4. Si la clave ya superó los 5 intentos, devuelve el error de bloqueo sin procesar las credenciales.
5. Valida el formato: correo obligatorio y válido; contraseña obligatoria y sin espacios en blanco.
6. Busca al usuario. Si no existe, ejecuta un hash ficticio para igualar el tiempo de respuesta y evitar revelar si el correo está registrado.
7. Si la contraseña es correcta, aplica las verificaciones de estado:
   - Docente no aprobado: rechaza el acceso con mensaje de aprobación pendiente.
   - Cuenta desactivada (`is_active = false`): rechaza el acceso y registra el evento `inactive_account_access`.
8. Si todo es válido, ejecuta `Auth::attempt`, limpia los contadores de intentos, regenera la sesión y registra el evento `successful_login`.
9. Gestiona las cookies de "recordarme": si se marcó, crea `saved_email` y `remember_checked` con vigencia de 30 días (43 200 minutos); si no, las elimina. La cookie `saved_password` se elimina siempre.
10. Redirige a la URL previamente solicitada o, por defecto, a `home`.

**Bloqueo progresivo.** Cuando las credenciales fallan:

1. Se suma un intento y se registra el evento `failed_login`.
2. Al llegar a 5 intentos se incrementa el contador de bloqueos de esa clave (almacenado en caché 24 horas bajo la clave `lockout_count_` seguida de la clave de control).
3. La duración del bloqueo se calcula como `300 s × 2^(n-1)`, con un máximo de 3600 s:

| Bloqueo consecutivo | Duración |
|---|---|
| 1.º | 5 minutos |
| 2.º | 10 minutos |
| 3.º | 20 minutos |
| 4.º | 40 minutos |
| 5.º en adelante | 60 minutos |

4. El mensaje mostrado ante credenciales inválidas es genérico (`Las credenciales no son válidas.`).

### 4.2 Registro de estudiante

1. El usuario accede a `GET /register` y envía `POST /register`.
2. El controlador normaliza el correo y valida los campos:

| Campo | Regla |
|---|---|
| `name` | Obligatorio, texto de 2 a 100 caracteres. |
| `dni` | Obligatorio, solo dígitos, máximo 30. |
| `phone` | Opcional, solo dígitos, máximo 20. |
| `email` | Obligatorio, formato válido, máximo 255. |
| `password` | Obligatoria, mínimo 8 caracteres, sin espacios en blanco. |

3. Si existe una cuenta **verificada** con el mismo correo o documento, rechaza el registro.
4. Si existen cuentas **no verificadas** con el mismo correo o documento, las elimina para permitir un nuevo registro limpio.
5. Crea el usuario con `role = student`, `is_approved = true`, `is_active = true`, `font_size = 14px` y `locale = es`. El teléfono se guarda como `null` si no se proporciona.
6. Dispara el evento `Registered`, que envía el correo de verificación.
7. Inicia sesión automáticamente, regenera la sesión y redirige a `verification.notice`.

En el formulario, `register.js` muestra un indicador de fortaleza con cinco criterios (longitud, mayúscula, símbolo, número y que no contenga el nombre). Es una ayuda visual del lado del cliente.

### 4.3 Verificación de correo electrónico

1. Tras el registro, el usuario ve `auth/verify-email` (`GET /email/verify`).
2. El elemento con `data-verify-email` activa en `auth-pages.js` un sondeo cada **3 segundos** a `GET /check-session-status`.
3. La ruta devuelve `{"status": "verified"}` si `email_verified_at` tiene valor, o `{"status": "pending"}` en caso contrario.
4. El usuario abre el enlace firmado recibido por correo: `GET /email/verify/{id}/{hash}` (middleware `signed`). El controlador ejecuta `fulfill()` y redirige a `home` con mensaje de éxito.
5. En la pestaña de espera, el sondeo detecta el estado `verified` y redirige automáticamente a `home`.
6. Si no llegó el correo, el botón de reenvío usa `POST /email/verification-notification` (máximo 6 solicitudes por minuto).

**Limpieza de cuentas no verificadas.** Si un estudiante sin verificar cierra sesión, su cuenta se elimina (ver 4.7). Además, un nuevo registro con el mismo correo o documento elimina los intentos previos sin verificar (ver 4.2).

### 4.4 Registro y aprobación de docentes

**Solicitud**

1. El aspirante accede a `GET /register/teacher` y envía `POST /register/teacher`.
2. El formulario captura el teléfono con selector de país (`intl-tel-input`, país inicial Colombia); el código se guarda en el campo oculto `country_code`.
3. Validaciones:

| Campo | Regla |
|---|---|
| `name` | Obligatorio, 2 a 100 caracteres, único en `users`. |
| `email` | Obligatorio, válido, único. |
| `dni` | Obligatorio, solo dígitos, máximo 30, único. |
| `country_code` | Obligatorio. |
| `phone` | Obligatorio, solo dígitos, máximo 20, único. |
| `birthdate` | Obligatoria, fecha con al menos 18 años cumplidos. |
| `teaching_type` | Obligatorio, valores `courses` o `advisories`. |

4. Se crea el usuario con `role = teacher`, `is_approved = 0`, `is_active = 0` y una contraseña aleatoria no comunicada. No se inicia sesión.
5. Se redirige a `teacher.pending`, que informa que la solicitud está en revisión.

**Resolución por el administrador** (`AdminTeacherController`)

- **Aprobar** (`POST /admin/teachers/{id}/approve`): genera una contraseña temporal de 10 caracteres, la guarda con hash, marca `is_approved = true`, `is_active = true` y `email_verified_at = now()`, y envía por correo las credenciales de acceso. Se indica al docente que cambie la contraseña desde su panel.
- **Denegar** (`DELETE /admin/teachers/{id}/deny`): envía un correo de rechazo y elimina el registro por completo.

### 4.5 Acceso con Google (OAuth 2.0)

1. El usuario pulsa el botón de Google en el login: `GET /auth/google` redirige al proveedor mediante Socialite.
2. Google devuelve al usuario a `GET /auth/google/callback`.
3. El controlador valida el resultado:
   - Si Socialite falla, registra una advertencia en el log y redirige al login con mensaje de error.
   - Si el correo está vacío o no está verificado por Google, rechaza el acceso.
4. Busca el usuario por `google_id` o, en su defecto, por correo.

**Usuario existente**

| Condición | Resultado |
|---|---|
| Ya tiene un `google_id` distinto al recibido | Rechaza: el correo está vinculado a otra cuenta de Google. |
| Docente no aprobado | Rechaza con mensaje de aprobación pendiente. |
| Cuenta desactivada | Rechaza y registra `inactive_account_access`. |
| Caso válido | Vincula el `google_id` si no lo tenía y marca el correo como verificado si no lo estaba. |

**Usuario nuevo**

Se crea automáticamente con `role = student`, `is_approved = true`, `is_active = true`, contraseña aleatoria de 40 caracteres, `email_verified_at = now()`, `font_size = 14px` y `locale = es`.

5. Se inicia sesión, se regenera la sesión y se registra el evento `successful_login` ("con Google").
6. Redirige a la URL solicitada o a `home`. Cualquier error redirige a `login` con un mensaje en sesión.

### 4.6 Recuperación de contraseña

1. **Solicitud** (`GET /forgot-password`, `POST /forgot-password`, máximo 3 por minuto): el usuario ingresa su correo. Si existe, se genera un código aleatorio de 6 dígitos, se guarda en `users.reset_code` junto con `reset_code_expires_at` (15 minutos) y se envía por correo. Se guarda el correo en sesión (`reset_email`). La respuesta es siempre la misma ("Si el correo está registrado, recibirás un código"), exista o no la cuenta.
2. **Verificación** (`GET /verify-code`, `POST /verify-code`, máximo 5 por minuto): requiere `reset_email` en sesión. Valida que el código tenga 6 dígitos, coincida con el del usuario y no haya expirado. Si es correcto, se marca `code_verified` en sesión.
3. **Nueva contraseña** (`GET /reset-password`, `POST /reset-password`, máximo 5 por minuto): requiere `reset_email` y `code_verified` en sesión. Valida la contraseña (mínimo 8, sin espacios, con confirmación), comprueba nuevamente que el código no haya expirado, guarda la nueva contraseña con hash, borra `reset_code` y `reset_code_expires_at`, limpia la sesión y redirige al login con mensaje de éxito.
4. Si el código expiró en cualquier punto, se limpia la sesión y se pide solicitar uno nuevo.

### 4.7 Cierre de sesión e inactividad

**Cierre manual** (`POST /logout`)

1. Si el usuario es estudiante y no ha verificado su correo, su cuenta se elimina.
2. Se cierra la sesión, se invalida y se regenera el token CSRF.
3. Redirige a `home` con el mensaje "Has salido del sistema".

**Cierre por inactividad**

1. `inactivity.js` reinicia un temporizador de **5 minutos** (300 000 ms) con cada movimiento del ratón, tecla, clic, desplazamiento o toque.
2. Al agotarse, redirige a `GET /session-expired`.
3. Esa ruta cierra la sesión, la invalida, regenera el token y devuelve la vista `errors.419` con código HTTP 419.

El valor `SESSION_LIFETIME` define además la vigencia de la sesión en el servidor; el valor efectivo depende de la variable de entorno configurada.

### 4.8 Registro de eventos de seguridad

Cada acceso relevante inserta un registro en `security_logs` mediante el método `securityLog` (presente en `AuthController` y `GoogleAuthController`):

| `event_type` | `status` | Se genera cuando |
|---|---|---|
| `successful_login` | `success` | Inicio de sesión exitoso (credenciales o Google). |
| `failed_login` | `failed` | Credenciales inválidas. |
| `inactive_account_access` | `failed` | Intento de acceso con cuenta desactivada. |

Los registros se consultan desde el panel de seguridad del administrador (`GET /admin/security`).

---

## 5. Rutas y Permisos

### 5.1 Rutas para invitados (middleware `guest`)

| Método | URI | Nombre | Controlador | Middleware adicional |
|---|---|---|---|---|
| GET | `/login` | `login` | `AuthController@showLogin` | `guest` |
| POST | `/login` | `login.store` | `AuthController@login` | `guest`, `throttle:login` |
| GET | `/auth/google` | `auth.google.redirect` | `GoogleAuthController@redirect` | `guest`, `throttle:10,1` |
| GET | `/auth/google/callback` | `auth.google.callback` | `GoogleAuthController@callback` | `guest`, `throttle:10,1` |
| GET | `/register` | `register` | `AuthController@showRegister` | `guest` |
| POST | `/register` | `register.store` | `AuthController@register` | `guest` |
| GET | `/register/teacher` | `register.teacher` | `AuthController@showTeacherRegister` | `guest` |
| POST | `/register/teacher` | `register.teacher.store` | `AuthController@registerTeacher` | `guest` |
| GET | `/teacher/pending-approval` | `teacher.pending` | Closure (vista `auth.teacher-pending`) | `guest` |
| GET | `/forgot-password` | `password.request` | `AuthController@showLinkRequestForm` | `guest` |
| POST | `/forgot-password` | `password.email` | `AuthController@sendResetCode` | `guest`, `throttle:3,1` |
| GET | `/verify-code` | `password.verify.form` | `AuthController@showVerifyForm` | `guest` |
| POST | `/verify-code` | `password.verify.submit` | `AuthController@verifyCode` | `guest`, `throttle:5,1` |
| GET | `/reset-password` | `password.reset.form` | `AuthController@showResetForm` | `guest` |
| POST | `/reset-password` | `password.update` | `AuthController@updatePassword` | `guest`, `throttle:5,1` |

### 5.2 Rutas para usuarios autenticados (middleware `auth`)

| Método | URI | Nombre | Controlador | Middleware adicional |
|---|---|---|---|---|
| POST | `/logout` | `logout` | `AuthController@logout` | `auth` |
| GET | `/session-expired` | `session.expired` | Closure (cierra sesión y muestra 419) | `auth` |
| GET | `/email/verify` | `verification.notice` | Closure (vista `auth.verify-email`) | `auth` |
| GET | `/check-session-status` | `verification.status-check` | Closure (JSON con estado de verificación) | `auth` |
| GET | `/email/verify/{id}/{hash}` | `verification.verify` | Closure (`EmailVerificationRequest`) | `auth`, `signed` |
| POST | `/email/verification-notification` | `verification.send` | Closure (reenvío de verificación) | `auth`, `throttle:6,1` |

### 5.3 Rutas de administración relacionadas

| Método | URI | Nombre | Controlador | Middleware |
|---|---|---|---|---|
| GET | `/admin/teachers/pending` | `admin.teachers.pending` | `AdminTeacherController@index` | `auth`, `verified` |
| POST | `/admin/teachers/{id}/approve` | `admin.teachers.approve` | `AdminTeacherController@approve` | `auth`, `verified` |
| DELETE | `/admin/teachers/{id}/deny` | `admin.teachers.deny` | `AdminTeacherController@deny` | `auth`, `verified` |
| GET | `/admin/security` | `admin.security` | `DashboardController@security` | `auth`, `verified`, `can:manage-users` |

### 5.4 Límites de solicitudes

| Acción | Límite | Alcance |
|---|---|---|
| Envío de login | 30 por minuto | Por IP (limitador `login`) |
| Intentos fallidos de login | 5 antes del bloqueo progresivo | Por combinación correo + IP |
| Flujo de Google OAuth | 10 por minuto | Grupo de rutas de Google |
| Solicitud de código de recuperación | 3 por minuto | Ruta |
| Verificación del código y cambio de contraseña | 5 por minuto | Cada ruta |
| Reenvío del correo de verificación | 6 por minuto | Ruta |

### 5.5 Permisos y condiciones de acceso por rol

| Rol | Condiciones para iniciar sesión | Observaciones |
|---|---|---|
| `student` | Cuenta activa (`is_active = true`). | Rol asignado por defecto en registro y en acceso con Google. Debe verificar su correo; si cierra sesión sin verificar, la cuenta se elimina. |
| `teacher` | `is_approved = true` y `is_active = true`. | Se crea inactivo y no aprobado; el administrador lo habilita y le envía una contraseña temporal. |
| `admin` | Cuenta activa. | Autorización posterior mediante `Gate manage-users` (`role === 'admin'`), definido en `AppServiceProvider`. |
| `user` | Cuenta activa. | Rol genérico definido en `User::ROLES`. |

---

## 6. Datos

### 6.1 Tabla `users` (campos usados por el módulo)

| Campo | Tipo | Detalle |
|---|---|---|
| `id` | bigint | Clave primaria. |
| `name` | string | Nombre del usuario. |
| `email` | string | Único. Se almacena en minúsculas. |
| `email_verified_at` | timestamp, nulo | Fecha de verificación del correo. |
| `password` | string | Hash (cast `hashed`). |
| `remember_token` | string, nulo | Token de "recordarme". |
| `reset_code` | string, nulo | Código de recuperación de 6 dígitos. |
| `reset_code_expires_at` | timestamp, nulo | Vencimiento del código (15 minutos). |
| `role` | string | Valor por defecto `student`. |
| `is_approved` | boolean | Valor por defecto `true`; los docentes se crean en `false`. |
| `is_active` | boolean | Valor por defecto `true`; los docentes se crean en `false`. |
| `dni` | string, nulo | Único. Solo dígitos. |
| `country_code` | string, nulo | Prefijo telefónico (docentes). |
| `phone` | string, nulo | Único. Solo dígitos. |
| `birthdate` | date, nulo | Fecha de nacimiento (docentes). |
| `teaching_type` | string, nulo | `courses` o `advisories` (docentes). |
| `google_id` | string, nulo | Único. Identificador de la cuenta de Google vinculada. |
| `locale` | string | Idioma; se asigna `es` al registrar. |
| `font_size` | string | Preferencia de apariencia; se asigna `14px` al registrar. |

Migraciones asociadas: `0001_01_01_000000_create_users_table`, `2026_09_26_231024_add_is_active_to_users_table`, `2026_09_27_222411_add_role_and_approval_to_users_table`, `2026_09_27_223810_add_teacher_details_to_users_table`, `2026_09_30_163716_add_locale_to_users_table`, `2026_10_28_154626_add_google_id_to_users_table`.

### 6.2 Tabla `security_logs`

| Campo | Tipo | Detalle |
|---|---|---|
| `id` | bigint | Clave primaria. |
| `user_id` | foreign key, nulo | Referencia a `users`; se pone en nulo si el usuario se elimina. |
| `email` | string, nulo | Correo involucrado en el evento. |
| `ip_address` | string, nulo | IP de origen. |
| `status` | string | `success` o `failed`. |
| `event_type` | string | `successful_login`, `failed_login`, `inactive_account_access`. |
| `description` | text | Descripción legible del evento. |
| `user_agent` | text, nulo | Navegador o cliente. |
| `created_at`, `updated_at` | timestamps | Fechas del registro. Índice compuesto en (`created_at`, `ip_address`). |

### 6.3 Tabla `sessions`

Almacena las sesiones activas (driver `database`): `id`, `user_id`, `ip_address`, `user_agent`, `payload` y `last_activity`.

### 6.4 Caché

El control de intentos y bloqueos se guarda en el almacén de caché configurado (`CACHE_STORE`, por defecto `database`, tablas `cache` y `cache_locks`):

| Clave | Contenido | Vigencia |
|---|---|---|
| Clave de `RateLimiter` (correo + IP) | Contador de intentos fallidos. | Según el bloqueo vigente. |
| `lockout_count_` seguido de correo + IP | Número de bloqueos consecutivos. | 24 horas. |

### 6.5 Cookies

| Cookie | Contenido | Vigencia |
|---|---|---|
| `saved_email` | Correo recordado en el formulario de login. | 30 días |
| `remember_checked` | Estado de la casilla "recordarme". | 30 días |

### 6.6 Sesión (flujo de recuperación)

| Clave de sesión | Contenido |
|---|---|
| `reset_email` | Correo para el que se solicitó el código. |
| `code_verified` | Indica que el código fue validado correctamente. |