# Páginas Públicas y Vistas de Error

Documentación técnica del sitio público de EduNivelam (Home, About, News, Courses, Instructors y Contact), de su estructura de presentación y de las vistas de error. Volver al [índice de documentación](../README.md).

---

## 1. Estado Actual

**Implementado y en funcionamiento.** El sitio público está formado por seis páginas accesibles sin iniciar sesión, un diseño común (barra de navegación y pie de página) y siete vistas de error personalizadas.

| Página | Ruta | Origen del contenido |
|---|---|---|
| Home | `/` | Banner dinámico desde la base de datos (tabla `banners`, página `home`) y secciones estáticas. |
| About | `/about` | Banner dinámico desde la base de datos (tabla `banners`, página `about`) y secciones estáticas. |
| News | `/news` | Dinámico: noticias publicadas desde la tabla `news`, con paginación y Banner dinámico desde la base de datos (tabla `banners`, página `news`) y secciones estáticas. |
| Courses | `/courses` | Catálogo de 3 cursos definido en el controlador (arreglo estático) y Banner dinámico desde la base de datos (tabla `banners`, página `courses`). |
| Instructors | `/instructors` | Directorio de 3 instructores definido en el controlador (arreglo estático) y Banner dinámico desde la base de datos (tabla `banners`, página `instructors`). |
| Contact | `/contact` | Formulario de contacto con envío por correo y banco de 50 preguntas frecuentes en JavaScript y Banner dinámico desde la base de datos (tabla `banners`, página `contact`). |

| Vista de error | Código HTTP |
|---|---|
| `errors/401.blade.php` | 401 |
| `errors/403.blade.php` | 403 |
| `errors/404.blade.php` | 404 |
| `errors/419.blade.php` | 419 |
| `errors/429.blade.php` | 429 |
| `errors/maintenance.blade.php` | 503 |
| `errors/google-unregistered.blade.php` | 400, 403 o 404 (según la causa) |

Consideraciones de estado:

- El modelo `Banner` definen también `home`, `about`, `news`, `courses`, `instructors` y `contact`, pero esas vistas contienen su texto de forma fija.
- Courses e Instructors muestran datos de ejemplo definidos en `SiteController`; no existen tablas de cursos ni de instructores.
- Varios enlaces del pie de página (cursos destacados, planes y accesos, política de privacidad) apuntan a `#`.

---

## 2. Propósito del Componente

Presentar la plataforma a visitantes y usuarios, facilitar el registro y el contacto con soporte, y comunicar de forma clara y consistente los estados de error y mantenimiento.

| Rol | Qué obtiene |
|---|---|
| **Visitante** | Información institucional, catálogo de cursos, directorio de docentes, noticias y canal de contacto, con acceso directo a registro e inicio de sesión. |
| **Estudiante y docente** | La misma navegación pública, más el acceso a su campus ("Mi Campus") desde la barra superior. |
| **Administrador** | Controla desde el panel el banner del Home y las noticias que se muestran en el sitio; recibe los mensajes de contacto. |

---

## 3. Estructura Interna

### 3.1 Controladores

| Controlador | Método | Función |
|---|---|---|
| `PageController` | `home` | Renderiza `page.home`. |
| `PageController` | `about` | Renderiza `page.about`. |
| `PageController` | `contact` | Renderiza `page.contact` (la ruta `/contact` usa `ContactController::create`). |
| `SiteController` | `reports` | Define el catálogo de cursos y renderiza `page.courses`. |
| `SiteController` | `directory` | Define el directorio de instructores y renderiza `page.instructors`. |
| `NewsController` | `publicIndex` | Publica las noticias programadas vencidas, lista las publicadas (9 por página) y renderiza `news.index`. |
| `ContactController` | `create`, `store` | Muestra el formulario y envía el mensaje a soporte. |

### 3.2 Correo asociado

| Elemento | Archivo | Función |
|---|---|---|
| `ContactMessage` | `app/Mail/ContactMessage.php` | Asunto "Nuevo mensaje: {asunto}", `replyTo` con el correo del remitente. |
| Plantilla | `resources/views/emails/contact.blade.php` | Muestra nombre, correo, asunto y mensaje. |

### 3.3 Diseño común (`resources/views/layouts/`)

| Archivo | Función |
|---|---|
| `app.blade.php` | Estructura HTML base: metaetiquetas, CSS y JS globales, aplicación de tipografía y alto contraste del usuario (ver [`configuracion-usuario.md`](./configuracion-usuario.md)). Decide la variante de diseño según la ruta. |
| `navigation.blade.php` | Barra de navegación flotante con enlaces, acceso a campus, inicio de sesión, registro y cierre de sesión. |
| `footer.blade.php` | Pie de página con enlaces de exploración, acerca de, soporte y legal. |
| `partials/sidebar.blade.php` | Menú lateral de las áreas de administración (no pertenece al sitio público). |

Variantes de diseño definidas en `app.blade.php`:

| Variante | Condición | Efecto |
|---|---|---|
| Área de administración | Rutas `dashboard`, `admin.*` y `settings.*` | Barra superior dentro del contenedor del panel y sin pie de página. |
| Pantalla completa | Rutas `teacher.pending` y `teacher.status*` | Contenido sin contenedor, sin pie de página. |
| Pública estándar | Cualquier otra ruta | Contenedor centrado con pie de página. |
| Sin pie de página | Rutas `login*` y `register*` | Se omite el pie de página. |

### 3.4 Vistas de las páginas

| Vista | Secciones principales |
|---|---|
| `page/home.blade.php` | Hero con banner, tarjeta "Campus Interactivo", cifras (+500, 100 %, 3 áreas), pilares de formación (Matemáticas Básicas, Lectura Crítica, Habilidades Blandas), testimonios y llamada a la acción de registro. |
| `page/about.blade.php` | Encabezado, misión, visión, valores y llamada a la acción para contactar con la administración. |
| `news/index.blade.php` | Tarjetas de noticias, paginación y ventana modal de lectura. |
| `page/courses.blade.php` | Encabezado, tarjetas de cursos con botón "Inscribirme" y llamada a la acción de inicio de sesión. |
| `page/instructors.blade.php` | Encabezado, tarjetas de instructores con botón "Solicitar Asesoría" y llamada a la acción para docentes. |
| `page/contact.blade.php` | Preguntas frecuentes (acordeón), formulario de contacto y widget de Cloudflare Turnstile. |

### 3.5 Assets (Vite)

| Archivo | Uso |
|---|---|
| `resources/css/app.css`, `resources/css/dashboard.css` | Estilos globales (cargados por el diseño base). |
| `resources/css/navbar.css` | Estilos de la barra de navegación. |
| `resources/css/home.css`, `about.css`, `courses.css`, `instructors.css` | Estilos propios de cada página. |
| `resources/css/errors/error-401.css`, `error-403.css`, `error-404.css`, `error-419.css`, `error-429.css`, `maintenance.css`, `google-error.css` | Estilos de cada vista de error. |
| `resources/js/app.js` | Punto de entrada global: importa `bootstrap.js` e `inactivity.js`. |
| `resources/js/home.js` | Mensaje de bienvenida rotativo (cambia cada 4 segundos entre 4 frases). |
| `resources/js/about.js` | Animación de entrada escalonada de las tarjetas de valores. |
| `resources/js/courses.js`, `instructors.js` | Efecto de color de fondo al pasar el cursor sobre las tarjetas. |
| `resources/js/contact.js` | Banco de preguntas frecuentes y lógica del acordeón. |
| `resources/js/news-modal.js` | Rellena la ventana modal de una noticia. |
| `public/animations/` | Animaciones Lottie (`student-401.json`, `student-404.json`, `session-expired.json`, `too-many-requests.json`, `maintenance.json`, `google-error.json`) e imagen `error-403.svg`. |

Librerías externas cargadas desde CDN en el diseño base: Bootstrap 5.0.2 (CSS y JS), Bootstrap Icons 1.11.3, Popper 2.9.2 y Lottie Web 5.12.2. El formulario de contacto carga además Cloudflare Turnstile.

---

## 4. Flujo de Funcionamiento

### 4.1 Construcción de una página pública

1. La ruta invoca el controlador, que prepara los datos y devuelve la vista.
2. La vista extiende `layouts.app` y define las secciones `title`, `styles`, `content` y `scripts`.
3. El diseño base incluye la barra de navegación (`navigation.blade.php`) y, salvo en las variantes que lo omiten, el pie de página.
4. Se aplica la tipografía y el modo de contraste guardados por el usuario autenticado; para visitantes se usa 14 px y fondo claro.

**Barra de navegación.** Los enlaces fijos son Home, About, News, Courses, Instructors y Contact.

| Situación del usuario | Comportamiento |
|---|---|
| Visitante | Muestra los enlaces y los botones de inicio de sesión y registro. |
| Autenticado con correo verificado | Muestra los enlaces, el botón de campus y el cierre de sesión. |
| Autenticado sin verificar (fuera de un panel) | Oculta los enlaces del sitio. |

El botón de campus depende del rol: administrador, "Dashboard" (`dashboard`); docente, "Mi Campus" (`teacher.dashboard`); estudiante, "Mi Campus" (`student.dashboard`).

### 4.2 Home

1. `PageController::home` busca el banner con `page_identifier = home` y `is_active = true`.
2. Si existe, la vista usa su título, subtítulo e imagen. En el título, la palabra "universitaria" se resalta con color; el texto se escapa antes de aplicar el resaltado. La imagen se sirve desde `storage/{image_path}`.
3. Si no existe banner activo, la vista usa los textos predeterminados incluidos en ella.
4. `home.js` rota el mensaje de bienvenida cada 4 segundos con una transición de opacidad.
5. Los botones dirigen a registro (`register`) y al catálogo de cursos (`courses`).

### 4.3 About

Página estática con misión, visión, valores y llamada a la acción hacia el formulario de contacto. `about.js` anima la aparición de las tarjetas de valores.

### 4.4 News

1. `NewsController::publicIndex` cambia a `publicado` las noticias con `status = programado` cuya `scheduled_at` ya venció (esto ocurre también al visitar la página, además del comando `news:publish-scheduled`).
2. Obtiene las noticias con `status = publicado`, de la más reciente a la más antigua, 9 por página.
3. Cada tarjeta muestra imagen (si existe), categoría, fecha, título y extracto (o el contenido sin etiquetas si no hay extracto).
4. Al pulsar una tarjeta se abre una ventana modal. `news-modal.js` toma los datos de los atributos `data-*` de la tarjeta (título, categoría, fecha, autor, contenido e imagen) y los inserta con `textContent`, por lo que el contenido se muestra como texto plano. El autor es el usuario que creó la noticia o "Administrador" si no hay relación.
5. Si no hay noticias, se muestra un mensaje de lista vacía; la paginación se genera con `links()`.

### 4.5 Courses

1. `SiteController::reports` entrega 3 cursos con los campos `title`, `category`, `description`, `level`, `duration` y `badge_color`:

| Curso | Categoría | Nivel | Duración |
|---|---|---|---|
| Álgebra y Pensamiento Lógico | Matemáticas | Nivelatorio - Básico | 4 semanas |
| Comprensión y Análisis de Textos | Lectura Crítica | Nivelatorio - Intermedio | 3 semanas |
| Gestión del Tiempo y Liderazgo | Habilidades Blandas | Transición Universitaria | 2 semanas |

2. La vista genera una tarjeta por curso con el botón "Inscribirme", que lleva a la pantalla de registro.
3. Una sección final invita a los estudiantes registrados a iniciar sesión.

### 4.6 Instructors

1. `SiteController::directory` entrega 3 instructores con los campos `name`, `role`, `specialty`, `bio`, `badge` y `badge_color`:

| Instructor | Rol | Área |
|---|---|---|
| Dr. Alberto Martínez | Director del Área de Matemáticas | Matemáticas |
| Dra. Sofía Rincón | Coordinadora de Lectura Crítica | Lectura Crítica |
| Lic. Carlos Gómez | Especialista en Habilidades Blandas | Habilidades Blandas |

2. Cada tarjeta incluye el botón "Solicitar Asesoría", que lleva al formulario de contacto.
3. Una sección final invita a docentes a postularse; el botón también dirige a Contact.

### 4.7 Contact

**Preguntas frecuentes**

1. `contact.js` contiene un banco de 50 preguntas agrupadas en cuatro categorías: soporte (15), docentes (15), cuenta (10) y cursos (10).
2. Al cargar la página se muestran 10 preguntas principales en un acordeón.
3. Al abrir una pregunta, se reemplazan dos de otras categorías por dos preguntas relacionadas con la categoría seleccionada, y aparece el botón "Ver principales", que restaura la lista inicial.

**Envío del formulario** (`POST /contact`)

1. Campos: nombre, correo, motivo (Soporte Técnico, Inscripción a Cursos o Asesoría Instructores) y mensaje; el formulario incluye el widget de Turnstile.
2. `ContactController::store` aplica un límite de 3 envíos por cada 15 minutos por IP. El intento se cuenta antes de validar, de modo que las solicitudes con errores de validación también suman.
3. Validación: nombre hasta 100 caracteres, correo válido hasta 255, asunto hasta 120 y mensaje hasta 1000.
4. Se envía `ContactMessage` al correo de soporte configurado en el controlador.
5. Si el envío es correcto, se vuelve al formulario con el mensaje de éxito; si falla, se conservan los datos escritos y se muestra el error.
6. El mensaje llega a la bandeja de soporte; el administrador lo gestiona mediante la sincronización IMAP descrita en `admin/gestion-correos-imap.md`.

### 4.8 Manejo de errores

1. Un error de autorización, de ruta, de sesión, de límite o de mantenimiento genera la respuesta HTTP correspondiente.
2. Laravel busca `resources/views/errors/{código}.blade.php` y devuelve la vista personalizada. Las excepciones de autenticación y de método no permitido se procesan en `bootstrap/app.php` (ver [`seguridad.md`](./seguridad.md)).
3. Todas las vistas de error son documentos HTML independientes (no extienden `layouts.app`). Cada una carga su CSS con Vite, `app.js`, Lottie Web desde CDN y la animación correspondiente mediante un script en línea.
4. Cada vista ofrece un botón de salida adecuado al contexto.

| Código | Vista | Cuándo se muestra | Título | Botón | Animación | CSS |
|---|---|---|---|---|---|---|
| 401 | `errors.401` | Acceso sin sesión a una ruta protegida (solicitudes que no esperan JSON). | Acceso No Autorizado | Iniciar Sesión (`login`) | `student-401.json` | `error-401.css` |
| 403 | `errors.403` | Autorización denegada (`can:manage-users`, `authorizeAdmin`). | Acceso Restringido | Vuelve al panel según el rol: `dashboard`, `teacher.dashboard`, `student.dashboard` o `home` | `error-403.svg` (imagen estática) | `error-403.css` |
| 404 | `errors.404` | Ruta o recurso inexistente. | Página no encontrada | Regresar al Inicio (`home`) | `student-404.json` | `error-404.css` |
| 419 | `errors.419` | Token CSRF inválido, sesión caducada o cierre por inactividad (`/session-expired`). | La Sesión ha Caducado | Reintentar / Recargar (página anterior) | `session-expired.json` | `error-419.css` |
| 429 | `errors.429` | Límite de solicitudes superado (`throttle`). | Demasiadas Solicitudes | Regresar al Inicio (`home`) | `too-many-requests.json` | `error-429.css` |
| 503 | `errors.maintenance` | Modo mantenimiento activo para usuarios que no son administrador. | Sistema en Mantenimiento | Regresar al Inicio (`home`) | `maintenance.json` | `maintenance.css` |
| 400, 403, 404 | `errors.google-unregistered` | Fallo del acceso con Google (ver 4.9). | Cuenta no vinculada | Registrarse Ahora (`register`) y Volver al Login (`login`) | `google-error.json` | `google-error.css` |

No existe una vista personalizada para el error 500; ante una falla interna se utiliza la vista predeterminada de Laravel.

### 4.9 Error de acceso con Google

`GoogleAuthController` devuelve la vista `errors.google-unregistered` con una variable `message` y un código HTTP según la causa:

| Causa | Código | Mensaje |
|---|---|---|
| Fallo al completar el inicio de sesión con Google | 400 | "No pudimos completar el inicio de sesión con Google. Inténtalo de nuevo." |
| Cuenta de Google sin correo verificado | 400 | "Tu cuenta de Google no tiene un correo verificado." |
| Correo vinculado a otra cuenta de Google | 400 | "Este correo ya está vinculado a otra cuenta de Google." |
| Docente pendiente de aprobación | 403 | "Tu cuenta de docente se encuentra pendiente de aprobación por el administrador." |
| Cuenta desactivada | 403 | "Tu cuenta ha sido desactivada por un administrador." |
| Correo no registrado en EduNivelam | 404 | "El correo electrónico no está registrado en EduNivelam." |

Si la variable no se define, la vista muestra un mensaje predeterminado sobre correo no registrado.

### 4.10 Comportamiento transversal

- `app.js` importa `inactivity.js` en todas las páginas, incluidas las públicas y las vistas de error. Tras 5 minutos sin actividad, el navegador se redirige a `/session-expired`, una ruta que requiere sesión (ver [`autenticacion.md`](./autenticacion.md)).
- El modo mantenimiento se aplica a los paneles de estudiante y docente; las páginas públicas siguen disponibles.

---

## 5. Rutas y Permisos

### 5.1 Rutas públicas

| Método | URI | Nombre | Controlador | Middleware |
|---|---|---|---|---|
| GET | `/` | `home` | `PageController@home` | Ninguno |
| GET | `/about` | `about` | `PageController@about` | Ninguno |
| GET | `/news` | `news.index` | `NewsController@publicIndex` | Ninguno |
| GET | `/courses` | `courses` | `SiteController@reports` | Ninguno |
| GET | `/instructors` | `instructors` | `SiteController@directory` | Ninguno |
| GET | `/contact` | `contact` | `ContactController@create` | Ninguno |
| POST | `/contact` | `contact.store` | `ContactController@store` | Ninguno (límite interno de 3 envíos por 15 minutos por IP) |

### 5.2 Permisos

| Capacidad | Visitante | Estudiante | Docente | Administrador |
|---|:---:|:---:|:---:|:---:|
| Ver las páginas públicas | Sí | Sí | Sí | Sí |
| Enviar un mensaje de contacto | Sí | Sí | Sí | Sí |
| Ver noticias publicadas | Sí | Sí | Sí | Sí |
| Ver borradores y noticias programadas | No | No | No | Sí (en el panel de noticias) |
| Editar el banner del Home y las noticias | No | No | No | Sí |

Las páginas públicas no requieren autenticación ni verificación de correo, y no están afectadas por el modo mantenimiento.

---

## 6. Datos

### 6.1 Tabla `banners` (utilizada por Home)

| Campo | Uso en el Home |
|---|---|
| `page_identifier` | Valor `home` para localizar el banner. |
| `title` | Título principal del hero. |
| `subtitle` | Texto de apoyo del hero. |
| `image_path` | Ruta de la imagen de la tarjeta interactiva. |
| `is_active` | Solo se muestra si es verdadero. |

Los campos `badge_text` y `content_text` existen en la tabla y están disponibles para las demás páginas definidas en `Banner::PAGES`.

### 6.2 Tabla `news` (utilizada por News)

| Campo | Uso |
|---|---|
| `title`, `category`, `excerpt`, `content`, `image` | Contenido de la tarjeta y de la ventana modal. |
| `status` | Solo se listan las de estado `publicado`. |
| `scheduled_at` | Si el estado es `programado` y la fecha venció, la noticia pasa a `publicado`. |
| `user_id` | Autor mostrado en la ventana modal (relación con `users`). |
| `created_at` | Fecha mostrada y criterio de orden descendente. |

### 6.3 Datos estáticos en `SiteController`

| Conjunto | Campos |
|---|---|
| Cursos | `title`, `category`, `description`, `level`, `duration`, `badge_color` |
| Instructores | `name`, `role`, `specialty`, `bio`, `badge`, `badge_color` |

### 6.4 Banco de preguntas frecuentes (`contact.js`)

| Campo | Descripción |
|---|---|
| `id` | Identificador numérico de la pregunta (1 a 50). |
| `category` | `cuenta`, `soporte`, `docentes` o `cursos`. |
| `q` | Texto de la pregunta. |
| `a` | Texto de la respuesta. |

Preguntas mostradas inicialmente: 1, 11, 21, 36, 2, 14, 25, 37, 30 y 46.

### 6.5 Formulario de contacto

| Campo | Regla de validación |
|---|---|
| `name` | Obligatorio, texto, máximo 100 caracteres. |
| `email` | Obligatorio, correo válido, máximo 255. |
| `subject` | Obligatorio, texto, máximo 120. |
| `message` | Obligatorio, texto, máximo 1000. |

Control de abuso: clave de caché `public-contact-{IP}`, 3 intentos con vigencia de 15 minutos.

### 6.6 Recursos estáticos de error

| Recurso | Ubicación |
|---|---|
| Vistas | `resources/views/errors/` |
| Hojas de estilo | `resources/css/errors/` |
| Animaciones e imágenes | `public/animations/` |