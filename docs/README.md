# Documentación Técnica — EduNivelam

Este archivo es el **índice central y la guía de navegación** de la carpeta `docs/`. La visión general del proyecto (instalación, stack tecnológico, arquitectura MVC, metodología Scrumban y estructura de carpetas) se encuentra en el [`README.md` principal](../README.md). Aquí solo se indica dónde está documentado cada componente; el detalle de cada uno se desarrolla dentro de su carpeta.

---

## 1. Guía de consulta

| Necesito consultar | Documento |
|---|---|
| Inicio de sesión, registro, verificación de correo y Google OAuth | [`general/autenticacion.md`](./general/autenticacion.md) |
| Tablas, migraciones, modelos y relaciones | [`general/base-datos.md`](./general/base-datos.md) |
| Cabeceras de seguridad, límites de intentos y registros de seguridad | [`general/seguridad.md`](./general/seguridad.md) |
| Perfil, contraseña, apariencia y datos personales del usuario | [`general/configuracion-usuario.md`](./general/configuracion-usuario.md) |
| Páginas públicas y vistas de error | [`general/paginas-publicas-y-errores.md`](./general/paginas-publicas-y-errores.md) |
| Comandos programados (`mail:receive`, `news:publish-scheduled`) | [`general/tareas-programadas.md`](./general/tareas-programadas.md) |
| Panel y métricas del administrador | [`admin/dashboard-admin.md`](./admin/dashboard-admin.md) |
| Usuarios, roles y aprobación de docentes | [`admin/gestion-usuarios.md`](./admin/gestion-usuarios.md) |
| Bandeja de correos IMAP | [`admin/gestion-correos-imap.md`](./admin/gestion-correos-imap.md) |
| Noticias y publicación programada | [`admin/noticias.md`](./admin/noticias.md) |
| Banners y textos editables del sitio | [`admin/banners-contenido.md`](./admin/banners-contenido.md) |
| Mantenimiento, respaldos, caché y variables de entorno | [`admin/configuracion-sistema.md`](./admin/configuracion-sistema.md) |
| Panel y perfil del docente | [`docente/panel-docente.md`](./docente/panel-docente.md) |
| Panel del estudiante | [`estudiante/panel-estudiante.md`](./estudiante/panel-estudiante.md) |

---

## 2. Estructura de la documentación

```text
docs/
├── README.md                              # Índice central y guía de navegación
├── general/                               # Módulos transversales
│   ├── autenticacion.md
│   ├── base-datos.md
│   ├── seguridad.md
│   ├── configuracion-usuario.md
│   ├── paginas-publicas-y-errores.md
│   └── tareas-programadas.md
├── admin/                                 # Panel de administración
│   ├── dashboard-admin.md
│   ├── gestion-usuarios.md
│   ├── gestion-correos-imap.md
│   ├── noticias.md
│   ├── banners-contenido.md
│   └── configuracion-sistema.md
├── docente/                               # Panel docente y creación de contenido
│   └── panel-docente.md
└── estudiante/                            # Panel estudiantil y exploración
    └── panel-estudiante.md
```

---

## 3. Índice por carpeta

### `general/` — Módulos transversales

Componentes compartidos por todos los roles de la plataforma.

| Documento | Contenido | Código de referencia |
|---|---|---|
| [`autenticacion.md`](./general/autenticacion.md) | Login con credenciales, registro de estudiante y docente, Google OAuth2, verificación de correo con sondeo de estado, recuperación de contraseña por código, bloqueo por intentos fallidos | `Auth/AuthController.php`, `Auth/GoogleAuthController.php`, `RegisterUserRequest.php`, vistas `auth/*` |
| [`base-datos.md`](./general/base-datos.md) | Esquema de tablas, migraciones, modelos Eloquent, relaciones y restricciones únicas (`email`, `dni`, `phone`, `google_id`) | `database/migrations/*`, `app/Models/*`, `config/database.php` |
| [`seguridad.md`](./general/seguridad.md) | Cabeceras HTTP y política CSP, *rate-limiting*, tabla `security_logs`, `Gate manage-users`, cierre de sesión por inactividad | `SecurityHeadersMiddleware.php`, `AppServiceProvider.php`, `resources/js/inactivity.js` |
| [`configuracion-usuario.md`](./general/configuracion-usuario.md) | Edición de perfil y avatar, cambio de contraseña, apariencia (tamaño de fuente, alto contraste, tema), descarga de datos personales, envío de comentarios | `SettingsController.php`, `resources/js/settings/*` |
| [`paginas-publicas-y-errores.md`](./general/paginas-publicas-y-errores.md) | Home, About, Contact, News, Courses e Instructors; vistas de error 401, 403, 404, 419, 429 y mantenimiento | `PageController.php`, `SiteController.php`, `ContactController.php`, `resources/views/errors/*` |
| [`tareas-programadas.md`](./general/tareas-programadas.md) | Comandos `mail:receive` y `news:publish-scheduled` y su frecuencia de ejecución | `routes/console.php`, `routes/web.php`, `app/Console/Commands/*` |

### `admin/` — Panel de administración

Herramientas disponibles para el rol administrador (protegidas con `can:manage-users`).

| Documento | Contenido | Código de referencia |
|---|---|---|
| [`dashboard-admin.md`](./admin/dashboard-admin.md) | Métricas generales, estado de seguridad de las últimas 24 horas y menú lateral | `DashboardController.php`, `dashboard.blade.php`, `layouts/partials/sidebar.blade.php` |
| [`gestion-usuarios.md`](./admin/gestion-usuarios.md) | Listado de usuarios, cambio de rol, desactivación y eliminación, aprobación o rechazo de docentes | `Admin/UserController.php`, `Admin/AdminTeacherController.php` |
| [`gestion-correos-imap.md`](./admin/gestion-correos-imap.md) | Sincronización IMAP, lectura, respuesta y eliminación de mensajes de contacto | `ReceivedEmailController.php`, `ContactController.php`, `ReceiveEmails.php`, `config/imap.php` |
| [`noticias.md`](./admin/noticias.md) | Creación, edición y eliminación de noticias, estados, publicación programada y recorte de imagen | `NewsController.php`, `Models/News.php`, `PublishScheduledNews.php` |
| [`banners-contenido.md`](./admin/banners-contenido.md) | Textos e imágenes editables de Home, About, News, Courses, Instructors y Contact | `Admin/BannerController.php`, `Models/Banner.php`, `config/banners.php` |
| [`configuracion-sistema.md`](./admin/configuracion-sistema.md) | Modo mantenimiento, respaldo de base de datos, pruebas SMTP e IMAP, limpieza de caché y logs, optimización de tablas, edición del `.env` | `SettingsController.php`, `CheckSystemMaintenance.php`, `admin/system-config.blade.php` |

### `docente/` — Panel docente

Espacio de trabajo del instructor.

| Documento | Contenido | Código de referencia |
|---|---|---|
| [`panel-docente.md`](./docente/panel-docente.md) | Registro de docentes con aprobación del administrador, pantalla de espera, panel principal y perfil académico | `AuthController::registerTeacher`, `teacher/dashboard.blade.php`, `auth/teacher-pending.blade.php` |

### `estudiante/` — Panel estudiantil y exploración

Experiencia del estudiante dentro de la plataforma.

| Documento | Contenido | Código de referencia |
|---|---|---|
| [`panel-estudiante.md`](./estudiante/panel-estudiante.md) | Acceso al panel tras la verificación del correo y seguimiento de la nivelación | `student/dashboard.blade.php` |

---

## 4. Estructura de cada documento

Todos los archivos de `docs/` siguen el mismo formato para facilitar su lectura:

1. **Estado Actual:** situación del componente.
2. **Propósito del Componente:** qué resuelve y para qué rol.
3. **Estructura Interna:** controladores, modelos, vistas, middleware, rutas y assets.
4. **Flujo de Funcionamiento:** recorrido paso a paso, de la petición a la respuesta.
5. **Rutas y Permisos:** método, URI, nombre de ruta y middleware.
6. **Datos:** tablas y campos involucrados.
