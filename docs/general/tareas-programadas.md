# Tareas Programadas

Documentación técnica de los comandos Artisan de EduNivelam y de su programación automática (`mail:receive` y `news:publish-scheduled`). Volver al [índice de documentación](../README.md).

---

## 1. Estado Actual

**Implementados y registrados en el programador de tareas de Laravel.** Ambos comandos están definidos para ejecutarse **cada minuto**.

| Comando | Archivo | Frecuencia | Dónde se programa |
|---|---|---|---|
| `mail:receive` | `app/Console/Commands/ReceiveEmails.php` | Cada minuto | `routes/console.php` |
| `news:publish-scheduled` | `app/Console/Commands/PublishScheduledNews.php` | Cada minuto | `routes/web.php` |

| Característica | Valor |
|---|---|
| Programador | Laravel Task Scheduling (`Schedule::command(...)->everyMinute()`) |
| Zona horaria de la aplicación | `America/Bogota` (`config/app.php`) |
| Librería de correo entrante | `webklex/laravel-imap` `^6.2` |
| Opciones adicionales de programación | No se definen `withoutOverlapping`, `onOneServer` ni `runInBackground` |
| Ejecución del programador | No está incluida en los scripts del proyecto (ver sección 4.1) |

La publicación de noticias programadas se realiza además de forma complementaria al abrir la lista pública de noticias y el listado de noticias del panel de administración (ver 4.4).

---

## 2. Propósito del Componente

Automatizar dos tareas periódicas sin intervención del usuario:

| Comando | Qué resuelve | Rol beneficiado |
|---|---|---|
| `mail:receive` | Trae a la base de datos los correos nuevos del buzón de soporte (IMAP) y elimina los antiguos, para que el administrador los gestione desde el panel sin acceder al servidor de correo. | Administrador |
| `news:publish-scheduled` | Publica automáticamente las noticias programadas cuando llega su fecha y hora, sin que el administrador deba hacerlo manualmente. | Administrador y visitantes (reciben la noticia a tiempo) |

---

## 3. Estructura Interna

### 3.1 Comandos

| Elemento | Detalle |
|---|---|
| `ReceiveEmails` | Firma `mail:receive`. Descripción: "Consulta el buzón IMAP, procesa los mensajes nuevos, limpia los de más de una semana y los guarda en la base de datos". |
| `PublishScheduledNews` | Firma `news:publish-scheduled`. Descripción: "Publica automáticamente las noticias cuya fecha de programación ya se cumplió". |
| Argumentos y opciones | Ninguno de los dos comandos define argumentos ni opciones. |

### 3.2 Modelos y componentes relacionados

| Elemento | Archivo | Relación con los comandos |
|---|---|---|
| `ReceivedEmail` | `app/Models/ReceivedEmail.php` | Tabla destino de `mail:receive`. |
| `News` | `app/Models/News.php` | Tabla que actualiza `news:publish-scheduled`. |
| `ReceivedEmailController` | `app/Http/Controllers/ReceivedEmailController.php` | Consulta, lectura, respuesta y eliminación de los correos sincronizados. |
| `NewsController` | `app/Http/Controllers/NewsController.php` | Crea noticias programadas y repite la lógica de publicación en `index` y `publicIndex`. |

### 3.3 Configuración IMAP (`config/imap.php`)

El comando usa la cuenta `default`, configurada con variables de entorno:

| Variable | Valor por defecto | Descripción |
|---|---|---|
| `IMAP_DEFAULT_ACCOUNT` | `default` | Cuenta utilizada. |
| `IMAP_HOST` | `localhost` | Servidor IMAP. |
| `IMAP_PORT` | `993` | Puerto. |
| `IMAP_PROTOCOL` | `imap` | Protocolo. |
| `IMAP_ENCRYPTION` | `ssl` | Cifrado (`ssl`, `tls`, `starttls`, `notls` o `false`). |
| `IMAP_VALIDATE_CERT` | `true` | Validación del certificado del servidor. |
| `IMAP_USERNAME` | `root@example.com` | Usuario del buzón. |
| `IMAP_PASSWORD` | vacío | Contraseña del buzón. |
| `IMAP_AUTHENTICATION` | `null` | Método de autenticación (por ejemplo `oauth`). |

El archivo `.env.example` no incluye estas variables; deben agregarse en el `.env` del entorno donde se ejecute el comando.

---

## 4. Flujo de Funcionamiento

### 4.1 Cómo se ejecuta el programador

Laravel no ejecuta las tareas por sí solo: requiere que el servidor invoque el programador cada minuto.

| Entorno | Forma de ejecutarlo |
|---|---|
| Producción (Linux) | Una entrada de `cron` que ejecute `php artisan schedule:run` cada minuto: `* * * * * cd /ruta/al/proyecto && php artisan schedule:run >> /dev/null 2>&1` |
| Desarrollo | `php artisan schedule:work` en una terminal (ejecuta el programador de forma continua). |
| Contenedor Docker | Un proceso adicional o un `cron` dentro del contenedor, ya que el `Dockerfile` del proyecto no lo incluye. |

El script `composer dev` del proyecto levanta el servidor, el *listener* de colas, el visor de logs y Vite, pero no el programador.

Comandos útiles de verificación:

| Comando | Función |
|---|---|
| `php artisan schedule:list` | Lista las tareas registradas y su próxima ejecución. |
| `php artisan mail:receive` | Ejecuta la sincronización IMAP manualmente. |
| `php artisan news:publish-scheduled` | Ejecuta la publicación de noticias manualmente. |

### 4.2 Sincronización de correos (`mail:receive`)

1. El programador invoca el comando cada minuto.
2. **Limpieza:** elimina de `received_emails` los registros con `received_at` anterior a 7 días y, si eliminó alguno, informa la cantidad.
3. **Conexión:** abre la cuenta IMAP `default` y selecciona la carpeta `INBOX`.
4. **Lectura:** obtiene hasta **20 mensajes no leídos** (`unseen`) por ejecución.
5. Para cada mensaje:
   - Si su fecha supera los 7 días de antigüedad, lo marca como leído (`Seen`) en el servidor y continúa con el siguiente, sin guardarlo.
   - En caso contrario toma `Message-ID`, remitente (correo y nombre; si no hay remitente usa `desconocido@domain.com`), asunto y cuerpo (HTML, o texto plano si no hay HTML).
   - Guarda el correo con `firstOrCreate` usando `message_id` como clave, lo que evita duplicados; el registro nuevo queda con `is_read = false`.
   - Marca el mensaje como leído (`Seen`) en el servidor.
6. Al terminar, muestra el mensaje de éxito y devuelve el código `SUCCESS` (0).
7. Si ocurre una excepción (por ejemplo, fallo de conexión o credenciales), muestra "Error al conectar con IMAP" con el detalle y devuelve `FAILURE` (1). La limpieza del paso 2 ya se habrá ejecutado.

Los mensajes que quedan marcados como leídos en el buzón no vuelven a procesarse. Desde el panel, el administrador consulta, responde y elimina los correos (ver `admin/gestion-correos-imap.md`).

### 4.3 Publicación de noticias programadas (`news:publish-scheduled`)

1. El programador invoca el comando cada minuto.
2. Consulta las noticias con `status = programado`, `scheduled_at` no nulo y `scheduled_at` menor o igual a la fecha y hora actuales.
3. Por cada noticia encontrada, actualiza `status` a `publicado` y deja `scheduled_at` en nulo.
4. Informa en consola cada noticia publicada (ID y título) y el cierre de la revisión.

**Cómo llega una noticia al estado `programado`.** Al crear o editar una noticia en el panel, si el administrador indica una fecha de programación futura, el sistema guarda `status = programado` y `scheduled_at` con la fecha en formato `Y-m-d H:i:s`.

### 4.4 Puntos donde se publica una noticia vencida

La misma regla (pasar a `publicado` las noticias programadas cuya fecha ya venció) se aplica en tres lugares:

| Punto | Cuándo ocurre |
|---|---|
| Comando `news:publish-scheduled` | Cada minuto, si el programador está en ejecución. |
| `NewsController::index` (panel de administración) | Al abrir el listado de noticias. |
| `NewsController::publicIndex` (`/news`) | Al visitar la página pública de noticias. |

De esta forma, una noticia vencida aparece publicada en cuanto alguien consulta las noticias, aunque el programador no esté activo.

### 4.5 Comprobación de la conexión IMAP desde el panel

La ruta `GET /admin/check-imap` (`admin.check.imap`) está registrada para el administrador y apunta al método `ContactController::validateImapConnection`; ese método no está definido en el controlador actual.

---

## 5. Rutas y Permisos

### 5.1 Ejecución de los comandos

Los comandos no tienen rutas web: se ejecutan desde la línea de comandos o desde el programador, con los permisos del usuario del sistema operativo que invoca `php artisan` (cron o terminal).

| Comando | Quién lo ejecuta | Código de salida |
|---|---|---|
| `mail:receive` | Programador (cada minuto) o manualmente | 0 si finaliza correctamente; 1 si falla la conexión o el proceso IMAP |
| `news:publish-scheduled` | Programador (cada minuto) o manualmente | 0 |

### 5.2 Rutas relacionadas

| Método | URI | Nombre | Controlador | Middleware | Relación |
|---|---|---|---|---|---|
| GET | `/admin/correos` | `admin.emails.index` | `ReceivedEmailController@index` | `auth`, `verified` | Lista los correos sincronizados por `mail:receive`. |
| GET | `/admin/correos/{id}` | `admin.emails.show` | `ReceivedEmailController@show` | `auth`, `verified` | Lectura de un correo. |
| POST | `/admin/correos/{id}/reply` | `admin.emails.reply` | `ReceivedEmailController@reply` | `auth`, `verified` | Respuesta al remitente. |
| DELETE | `/admin/correos/{id}` | `admin.emails.destroy` | `ReceivedEmailController@destroy` | `auth`, `verified` | Eliminación manual. |
| GET | `/admin/check-imap` | `admin.check.imap` | `ContactController@validateImapConnection` | `auth`, `verified` | Comprobación de la conexión IMAP (ver 4.5). |
| GET | `/admin/news` | `admin.news.index` | `NewsController@index` | `can:manage-users` | Listado del panel; también publica noticias vencidas. |
| GET | `/news` | `news.index` | `NewsController@publicIndex` | Ninguno | Página pública; también publica noticias vencidas. |

### 5.3 Permisos

| Operación | Quién puede realizarla |
|---|---|
| Ejecutar los comandos | Quien tenga acceso a la terminal del servidor, o el programador del sistema. |
| Consultar, responder y eliminar correos sincronizados | Usuarios autenticados con correo verificado (ver [`seguridad.md`](./seguridad.md), sección 5.3). |
| Crear y programar noticias | Administrador (`can:manage-users`). |

---

## 6. Datos

### 6.1 Tabla `received_emails` (escrita por `mail:receive`)

| Campo | Tipo | Origen del dato |
|---|---|---|
| `id` | bigint unsigned | Autoincremental. |
| `message_id` | varchar(255), único | `Message-ID` del correo; clave para evitar duplicados. |
| `from_email` | varchar(255) | Correo del remitente (`desconocido@domain.com` si no hay remitente). |
| `from_name` | varchar(255), nulo | Nombre del remitente. |
| `subject` | varchar(255), nulo | Asunto. |
| `body` | longtext, nulo | Cuerpo HTML o, en su defecto, texto plano. |
| `received_at` | timestamp, nulo | Fecha del mensaje. |
| `is_read` | boolean, por defecto `false` | Siempre `false` al sincronizar; el administrador lo cambia al leerlo. |
| `created_at`, `updated_at` | timestamp | Fechas del registro. |

Reglas de retención: se conservan 7 días a partir de `received_at`; los más antiguos se eliminan en cada ejecución del comando.

### 6.2 Tabla `news` (actualizada por `news:publish-scheduled`)

| Campo | Cambio realizado por el comando |
|---|---|
| `status` | De `programado` a `publicado`. |
| `scheduled_at` | De la fecha programada a nulo. |

Valores válidos de `status`: `publicado`, `borrador`, `programado`.

### 6.3 Parámetros operativos

| Parámetro | Valor |
|---|---|
| Frecuencia de ambos comandos | Cada minuto |
| Mensajes IMAP procesados por ejecución | 20 (no leídos) |
| Carpeta IMAP | `INBOX` |
| Retención de correos | 7 días |
| Zona horaria de comparación de fechas | `America/Bogota` |
| Marca aplicada en el servidor IMAP | `Seen` |

### 6.4 Variables de entorno

| Grupo | Variables |
|---|---|
| IMAP | `IMAP_DEFAULT_ACCOUNT`, `IMAP_HOST`, `IMAP_PORT`, `IMAP_PROTOCOL`, `IMAP_ENCRYPTION`, `IMAP_VALIDATE_CERT`, `IMAP_USERNAME`, `IMAP_PASSWORD`, `IMAP_AUTHENTICATION` |