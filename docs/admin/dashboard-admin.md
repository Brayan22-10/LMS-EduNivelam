# Dashboard de Administración

Documentación técnica del panel principal del administrador: resumen general, métricas, menú lateral de navegación y monitoreo de seguridad y accesos. Volver al [índice de documentación](../README.md).

---

## 1. Estado Actual

**Implementado y en funcionamiento.** El módulo está formado por dos pantallas y un menú lateral compartido:

| Elemento | Ruta | Descripción |
|---|---|---|
| Resumen General | `/dashboard` | Bienvenida, métricas, interruptor de tema y accesos directos a las herramientas de gestión. |
| Seguridad y Accesos | `/admin/security` | Estado de seguridad de las últimas 24 horas y bitácora de eventos de acceso. |
| Menú lateral | Todas las pantallas del panel | Navegación por rol, colapsable, con acceso al perfil y cierre de sesión. |

| Característica | Valor |
|---|---|
| Protección de las rutas | `auth`, `verified` y `can:manage-users` (solo rol `admin`) |
| Controlador | `DashboardController` |
| Vistas | `dashboard.blade.php`, `admin/security.blade.php` |
| Estilos y scripts | `resources/css/dashboard.css`, `resources/js/dashboard.js` |
| Soporte de modo claro y oscuro | Sí (campo `high_contrast` del usuario) |
| Modo mantenimiento | No aplica a estas rutas: el administrador siempre puede acceder |

Métricas disponibles en el Resumen General:

| Métrica | Fuente | Detalle |
|---|---|---|
| Usuarios Registrados | `User::count()` | Total de filas de la tabla `users`, sin distinguir rol ni estado. |
| Publicaciones Activas | `Post::count()` | Total de filas de la tabla `posts`. |

Consideraciones de estado:

- El distintivo "Sistema Activo" es un elemento fijo de la vista; no refleja una comprobación en tiempo real.
- En el monitoreo de seguridad, la etiqueta "Cuentas activas en la plataforma" muestra el total de usuarios registrados (`User::count()`), no únicamente los de estado activo.
- El contador "Intentos de acceso erróneos detectados" incluye todos los eventos registrados en `security_logs` durante las últimas 24 horas (accesos exitosos y fallidos).
- El método `DashboardController::clearSystemCache` existe, pero no tiene ruta asociada; la limpieza de caché se atiende desde `SettingsController` (ver 4.7).

---

## 2. Propósito del Componente

Proveer al administrador una vista general del estado de la plataforma, indicadores clave y un punto único de acceso a las herramientas de gestión, además de supervisar la actividad de acceso para detectar comportamientos inusuales.

| Necesidad | Cómo la resuelve |
|---|---|
| Conocer la dimensión de la plataforma | Tarjetas con el total de usuarios y de publicaciones. |
| Acceder rápidamente a cada herramienta | Menú lateral y botones de acceso directo. |
| Supervisar la seguridad | Semáforo de estado y bitácora de las últimas 24 horas. |
| Adaptar la interfaz | Interruptor de modo claro y oscuro, y menú lateral colapsable. |

Rol destinatario: **administrador**. El menú lateral es compartido con los paneles de docente y estudiante, que muestran sus propias opciones.

---

## 3. Estructura Interna

### 3.1 Controlador

`app/Http/Controllers/DashboardController.php`

| Método | Función |
|---|---|
| `index` | Calcula las métricas (usuarios y publicaciones) y renderiza `dashboard`. |
| `security` | Consulta los eventos de `security_logs` de las últimas 24 horas, calcula el estado de seguridad y renderiza `admin.security`. |
| `clearSystemCache` | Ejecuta `optimize:clear` y devuelve un mensaje de éxito o error. No tiene ruta asociada actualmente. |

Ambas consultas verifican con `Schema::hasTable` que la tabla exista antes de consultarla; si no existe, la métrica vale 0 o la lista queda vacía.

### 3.2 Modelos y tablas

| Elemento | Uso |
|---|---|
| `User` | Conteo de usuarios y datos del administrador autenticado (nombre, `high_contrast`). |
| `Post` | Conteo de publicaciones. |
| Tabla `security_logs` | Origen de la bitácora (consulta con `DB::table`, sin modelo). |

### 3.3 Vistas y componentes

| Archivo | Función |
|---|---|
| `resources/views/dashboard.blade.php` | Resumen General: encabezado, interruptor de tema, tarjetas de métricas y accesos directos. |
| `resources/views/admin/security.blade.php` | Monitoreo de seguridad: estado, contadores y tabla de eventos. |
| `resources/views/layouts/partials/sidebar.blade.php` | Menú lateral dependiente del rol, con perfil y cierre de sesión. |
| `resources/views/layouts/partials/logout-animation.blade.php` | Animación al cerrar sesión (incluida desde el menú lateral). |
| `resources/views/layouts/app.blade.php` | Diseño base: en las rutas `dashboard`, `admin.*` y `settings.*` aplica la variante de área de administración (clase `admin-root`, barra superior dentro del panel y sin pie de página). |
| `resources/views/admin/system-config.blade.php` | Vista de limpieza de caché; actualmente no es alcanzada por ninguna ruta (ver 4.7). |

### 3.4 Assets (Vite)

| Archivo | Función |
|---|---|
| `resources/css/dashboard.css` | Estilos del panel: contenedor, menú lateral, barra superior, tarjetas de métricas (`stat-card`), colores adaptables al modo oscuro (`bg-card-adaptive`, `text-dark-adaptive`), interruptor de tema y reducción de animaciones (`prefers-reduced-motion`). |
| `resources/js/dashboard.js` | Colapso del menú lateral con persistencia y cambio de tema con sincronización en el servidor. |
| Bootstrap 5.0.2 y Bootstrap Icons 1.11.3 | Componentes e iconos (cargados por el diseño base). |

### 3.5 Menú lateral

Encabezado y opciones según el rol del usuario autenticado:

| Rol | Encabezado | Opciones |
|---|---|---|
| Administrador | Panel Admin, "Gestión General" | Resumen General, Gestión de Usuarios, Aceptar Docentes, Buzón IMAP, Gestión de Banners, Gestión de Noticias, Seguridad y Accesos, Configuración |
| Docente | Panel Docente, "Cuerpo Académico" | Inicio Docente, Mis Cursos Asignados y Subir Guías y Material (enlaces sin destino), Configuración |
| Estudiante | Panel Estudiante, "Nivelación Académica" | Inicio Estudiante, Configuración |

En todos los casos, el pie del menú incluye el botón "Mi Perfil" (`admin.profile`) y "Cerrar Sesión". Las opciones de gestión del administrador se muestran dentro de la directiva `@can('manage-users')`.

---

## 4. Flujo de Funcionamiento

### 4.1 Acceso al panel

1. El administrador inicia sesión (ver [`autenticacion.md`](./autenticacion.md)). En la barra de navegación aparece el botón "Dashboard".
2. Al solicitar `/dashboard` o `/admin/security`, la petición atraviesa los middleware de la ruta en este orden: `auth` (sesión iniciada), `verified` (correo verificado) y `can:manage-users` (rol `admin`).
3. Si falta la sesión se muestra la vista 401; si el correo no está verificado se redirige a la pantalla de verificación; si el usuario no es administrador se muestra la vista 403 (ver [`paginas-publicas-y-errores.md`](./paginas-publicas-y-errores.md)).
4. Si todo es correcto, `DashboardController` prepara los datos y devuelve la vista.

### 4.2 Resumen General

1. `DashboardController::index` obtiene el total de usuarios y de publicaciones.
2. La vista muestra:
   - **Encabezado:** "Bienvenido, {nombre}" y la descripción del panel.
   - **Interruptor de tema:** botones de modo claro y oscuro, con el estado actual tomado de `high_contrast`.
   - **Distintivo "Sistema Activo".**
   - **Tarjetas de métricas:** Usuarios Registrados y Publicaciones Activas.
   - **Panel de control general:** texto informativo y botones de acceso directo.

Botones de acceso directo (los primeros seis dentro de `@can('manage-users')`):

| Botón | Destino |
|---|---|
| Administrar Usuarios | `admin.users` |
| Aceptar Docentes | `admin.teachers.pending` |
| Ver Buzón IMAP | `admin.emails.index` |
| Gestión de Banners | `admin.banners.index` |
| Gestión de Noticias | `admin.news.index` |
| Auditoría de Seguridad | `admin.security` |
| Ajustes del Sistema | `settings.index` |

### 4.3 Cambio de modo claro y oscuro

1. El administrador pulsa el botón de sol o de luna.
2. `dashboard.js` envía `POST /settings/toggle-theme` con `high_contrast` en formato JSON y el token CSRF.
3. El servidor actualiza el campo `users.high_contrast` y responde `{"success": true, "high_contrast": ...}` (ver [`configuracion-usuario.md`](./configuracion-usuario.md)).
4. El navegador cambia las clases del `body` (`bg-dark-mode` o `bg-light`) y el botón activo sin recargar la página. La preferencia se conserva en la cuenta.

### 4.4 Navegación desde el menú lateral

| Opción | Ruta | Documentación |
|---|---|---|
| Resumen General | `/dashboard` | Este documento |
| Gestión de Usuarios | `/admin/users` | `admin/gestion-usuarios.md` |
| Aceptar Docentes | `/admin/teachers/pending` | `admin/gestion-usuarios.md` |
| Buzón IMAP | `/admin/correos` | `admin/gestion-correos-imap.md` |
| Gestión de Banners | `/admin/banners` | `admin/banners-contenido.md` |
| Gestión de Noticias | `/admin/news` | `admin/noticias.md` |
| Seguridad y Accesos | `/admin/security` | Este documento y [`seguridad.md`](./seguridad.md) |
| Configuración | `/settings` | [`configuracion-usuario.md`](./configuracion-usuario.md) y `admin/configuracion-sistema.md` |
| Mi Perfil | `/admin/profile` | [`configuracion-usuario.md`](./configuracion-usuario.md) |

La opción activa se resalta comparando el nombre de la ruta actual (`request()->routeIs(...)`).

### 4.5 Menú lateral colapsable

1. Los botones `#sidebarToggle` (dentro del menú) y `#sidebarToggleFloat` (botón flotante) alternan la clase `sidebar-collapsed` en el contenedor del panel y en el `body`.
2. El estado se guarda en `localStorage` bajo la clave `sidebar_collapsed`.
3. Al cargar cualquier pantalla del panel, `dashboard.js` lee esa clave y restaura el estado del menú.

### 4.6 Monitoreo de seguridad

1. El administrador abre "Seguridad y Accesos" (`GET /admin/security`).
2. `DashboardController::security` consulta en `security_logs` todos los eventos con `created_at` en las últimas 24 horas, de más reciente a más antiguo.
3. Cuenta los eventos y determina el estado:

| Eventos en las últimas 24 horas | Estado | Distintivo mostrado |
|---|---|---|
| 0 | `secure` | "Sistema Seguro (Operación Normal)" (verde) |
| De 1 a 4 | `warning` | "Precaución: Actividad Inusual" (amarillo) |
| 5 o más | `danger` | "Alerta Crítica de Seguridad" (rojo) |

4. La vista presenta:
   - **Contador de eventos:** en rojo si es mayor que cero, en verde si es cero.
   - **Total de usuarios registrados.**
   - **Bitácora de Eventos de Seguridad Recientes:** tabla con fecha y hora, correo involucrado (o `N/D`), dirección IP y detalle. El detalle se muestra en verde si el evento tiene `status = success` y en rojo en caso contrario. La tabla tiene desplazamiento vertical (altura máxima de 350 px).
   - Si no hay eventos, un mensaje indica que el sistema opera con normalidad.

Los eventos provienen de los inicios de sesión exitosos, fallidos y de cuentas desactivadas (ver [`autenticacion.md`](./autenticacion.md), sección 4.8).

### 4.7 Mantenimiento y limpieza de caché

Las herramientas de servidor no forman parte del Resumen General; se acceden desde Configuración (pestaña del servidor, solo administrador) y se documentan en `admin/configuracion-sistema.md`. Respecto a este módulo:

- La ruta `GET /admin/system/config` redirige a `settings.index`.
- La ruta `POST /admin/system/clear-cache` (`admin.system.clearcache`) se atiende en `SettingsController::clearSystemCache`.
- La vista `admin/system-config.blade.php` y el método `DashboardController::clearSystemCache` (que ejecuta `optimize:clear`) permanecen en el código sin una ruta que los invoque.

---

## 5. Rutas y Permisos

### 5.1 Rutas del módulo

| Método | URI | Nombre | Controlador | Middleware |
|---|---|---|---|---|
| GET | `/dashboard` | `dashboard` | `DashboardController@index` | `auth`, `verified`, `can:manage-users` |
| GET | `/admin/security` | `admin.security` | `DashboardController@security` | `auth`, `verified`, `can:manage-users` |
| GET | `/admin/system/config` | `admin.system.config` | Closure (redirige a `settings.index`) | `auth`, `verified`, `can:manage-users` |
| POST | `/settings/toggle-theme` | `settings.toggle-theme` | `SettingsController@toggleTheme` | `auth` |
| GET | `/admin/profile` | `admin.profile` | `SettingsController@profileView` | `auth` |

### 5.2 Destinos del menú lateral y su protección

| Destino | Ruta | Middleware de la ruta |
|---|---|---|
| Gestión de Usuarios | `/admin/users` | `auth`, `verified`, `can:manage-users` |
| Aceptar Docentes | `/admin/teachers/pending` | `auth`, `verified` |
| Buzón IMAP | `/admin/correos` | `auth`, `verified` |
| Gestión de Banners | `/admin/banners` | `auth`, `verified`, `can:manage-users` |
| Gestión de Noticias | `/admin/news` | `auth`, `verified`, `can:manage-users` |
| Seguridad y Accesos | `/admin/security` | `auth`, `verified`, `can:manage-users` |
| Configuración | `/settings` | `auth` |

El menú muestra estas opciones únicamente al administrador (directiva `@can('manage-users')`). Las rutas "Aceptar Docentes" y "Buzón IMAP" no incluyen `can:manage-users` en su definición (ver [`seguridad.md`](./seguridad.md), sección 5.3).

### 5.3 Permisos

| Capacidad | Estudiante | Docente | Administrador |
|---|:---:|:---:|:---:|
| Ver el Resumen General (`/dashboard`) | No (403) | No (403) | Sí |
| Ver el monitoreo de seguridad | No (403) | No (403) | Sí |
| Ver el menú lateral con sus propias opciones | Sí | Sí | Sí |
| Cambiar el modo claro u oscuro | Sí | Sí | Sí |
| Acceso durante el modo mantenimiento | No | No | Sí |

---

## 6. Datos

### 6.1 Origen de las métricas

| Dato mostrado | Consulta | Tabla |
|---|---|---|
| Usuarios Registrados | `User::count()` | `users` |
| Publicaciones Activas | `Post::count()` | `posts` |
| Cuentas activas en la plataforma (monitoreo) | `User::count()` | `users` |
| Eventos de las últimas 24 horas | `DB::table('security_logs')->where('created_at', '>=', ahora - 24 h)->orderBy('created_at', 'desc')` | `security_logs` |

### 6.2 Campos de `security_logs` usados en la bitácora

| Campo | Uso en la vista |
|---|---|
| `created_at` | Fecha y hora (formato `d/m/Y H:i:s`) y criterio de filtrado y orden. |
| `email` | Correo involucrado (`N/D` si es nulo). |
| `ip_address` | Dirección IP. |
| `status` | Define el color del detalle (`success` en verde; otro valor en rojo). |
| `description` | Detalle del evento. |

La estructura completa de la tabla se describe en [`base-datos.md`](./base-datos.md).

### 6.3 Umbrales del semáforo de seguridad

| Parámetro | Valor |
|---|---|
| Ventana de tiempo | 24 horas |
| Estado `secure` | 0 eventos |
| Estado `warning` | De 1 a 4 eventos |
| Estado `danger` | 5 o más eventos |

### 6.4 Datos del usuario y del navegador

| Elemento | Dónde | Uso |
|---|---|---|
| `users.name` | Base de datos | Saludo "Bienvenido, {nombre}". |
| `users.high_contrast` | Base de datos | Estado inicial del interruptor de tema y clases del `body`. |
| `sidebar_collapsed` | `localStorage` del navegador | Estado colapsado del menú lateral. |
| Token CSRF | Etiqueta `meta csrf-token` | Autoriza la petición de cambio de tema. |