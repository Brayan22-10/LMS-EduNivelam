# Gestión de Usuarios

Documentación técnica del módulo de administración de usuarios: listado y estadísticas, cambio de roles, activación, desactivación y eliminación de cuentas, y aprobación o rechazo de solicitudes de docentes. Volver al [índice de documentación](../README.md).

---

## 1. Estado Actual

**Implementado y en funcionamiento.** El módulo se compone de dos pantallas del panel de administración y de una consulta pública para el docente solicitante.

| Pantalla | Ruta | Descripción |
|---|---|---|
| Gestión de Usuarios | `/admin/users` | Estadísticas, gráficos, listado paginado por rol, activar o desactivar cuentas, eliminación permanente y tablero para reasignar roles. |
| Solicitudes de Docentes | `/admin/teachers/pending` | Lista de docentes en espera, con acciones de aprobar o denegar. |
| Estado de la solicitud (pública) | `/teacher/status` | El docente consulta su estado y su posición en la cola con su correo y documento. |

| Característica | Valor |
|---|---|
| Controladores | `Admin/UserController`, `Admin/AdminTeacherController`, `Auth/TeacherStatusController` |
| Roles gestionados | `admin`, `teacher`, `student`, `user` |
| Estados de cuenta | Activo (`is_active = true`) e Inactivo (`is_active = false`) |
| Eliminación | Permanente (la tabla `users` no usa borrado lógico) |
| Registros de auditoría | Las acciones de este módulo no generan filas en `security_logs` ni en `user_activity_logs` |
| Librería de gráficos | Chart.js 4 (`chart.js ^4.6.1`) |

Consideraciones de estado:

- La acción de ruta `DELETE /admin/users/{id}` (`destroy`) **alterna el estado activo/inactivo** de la cuenta; no elimina el registro. La eliminación real se realiza con `forceDelete`.
- La desactivación se evalúa al iniciar sesión (formulario y Google). No existe un middleware que revise `is_active` en cada petición, por lo que una sesión ya iniciada no se cierra al desactivar la cuenta.
- La aprobación de un docente envía una contraseña temporal por correo en texto plano.

---

## 2. Propósito del Componente

Administrar el ciclo de vida de las cuentas de la plataforma: supervisar el crecimiento de usuarios, asignar o cambiar roles, habilitar o bloquear el acceso, eliminar cuentas y decidir qué docentes obtienen acceso.

| Necesidad | Cómo la resuelve |
|---|---|
| Conocer la composición de usuarios | Tarjetas de estadísticas y cuatro gráficos. |
| Encontrar usuarios por tipo | Pestañas por rol y listado paginado. |
| Controlar el acceso | Activar o desactivar cuentas. |
| Dar de baja una cuenta | Eliminación permanente con confirmación. |
| Reorganizar permisos | Cambio de rol individual y tablero de roles. |
| Validar a los docentes | Aprobación (con envío de credenciales) o rechazo (con eliminación del registro). |

Rol destinatario: **administrador**.

---

## 3. Estructura Interna

### 3.1 Controladores

| Controlador | Método | Función |
|---|---|---|
| `Admin/UserController` | `index` | Prepara listado, estadísticas, gráficos y tablero de roles. |
| | `updateRole` | Cambia el rol de un usuario. |
| | `destroy` | Activa o desactiva la cuenta. |
| | `forceDelete` | Elimina la cuenta de forma permanente. |
| | `buildStats` (privado) | Calcula total, nuevos del mes, crecimiento, activos e inactivos. |
| | `buildCharts` (privado) | Prepara los datos de los cuatro gráficos. |
| `Admin/AdminTeacherController` | `index` | Lista las solicitudes de docentes pendientes. |
| | `approve` | Aprueba la solicitud y envía las credenciales. |
| | `deny` | Rechaza la solicitud y elimina el registro. |
| `Auth/TeacherStatusController` | `show`, `check` | Formulario público y consulta del estado de la solicitud. |

### 3.2 Modelo

`app/Models/User.php`: constante `ROLES = ['admin', 'teacher', 'student', 'user']`. Campos relevantes: `role`, `is_active`, `is_approved`, `teaching_type`, `dni`, `phone`, `country_code`, `birthdate`, `email_verified_at` y `created_at`. La estructura completa está en [`base-datos.md`](./base-datos.md).

### 3.3 Vistas

| Vista | Función |
|---|---|
| `resources/views/admin/users/index.blade.php` | Pantalla de Gestión de Usuarios: tarjetas, gráficos, pestañas, tabla, paginación y modal "Tablero de Gestión de Roles de Usuarios". |
| `resources/views/admin/teachers-pending.blade.php` | Pantalla de solicitudes de docentes. |
| `resources/views/auth/teacher-status.blade.php` | Consulta pública del estado de la solicitud. |
| `resources/views/layouts/partials/sidebar.blade.php` | Opciones "Gestión de Usuarios" y "Aceptar Docentes" del menú lateral. |

### 3.4 Assets (Vite)

| Archivo | Función |
|---|---|
| `resources/css/dashboard.css` | Estilos generales del panel. |
| `resources/css/admin-users.css` | Estilos de tarjetas, gráficos y tabla de usuarios. |
| `resources/js/admin-users.js` | Construye los gráficos con Chart.js a partir de los datos de la vista y los adapta al modo claro u oscuro. |
| `resources/css/teacher-status.css`, `resources/js/teacher-status.js` | Estilos y comportamiento de la consulta pública. |

### 3.5 Correos

Los correos de aprobación y rechazo se envían con `Mail::raw` (texto plano), sin clase `Mailable` ni plantilla:

| Evento | Asunto |
|---|---|
| Aprobación | ¡Cuenta de Docente Aprobada - EduNivelam! |
| Rechazo | Solicitud de Docente Denegada - EduNivelam |

---

## 4. Flujo de Funcionamiento

### 4.1 Listado y estadísticas de usuarios

1. El administrador abre "Gestión de Usuarios" (`GET /admin/users`). La petición pasa por `auth`, `verified` y `can:manage-users`.
2. `UserController::index` lee el parámetro `tab` (`all`, `student`, `teacher` o `admin`); cualquier otro valor se trata como `all`.
3. Consulta los usuarios (de más reciente a más antiguo, **10 por página**, conservando el parámetro `tab` en los enlaces de paginación) y el conteo por rol.
4. Calcula las estadísticas y los datos de los gráficos, y entrega la vista.

**Tarjetas de estadísticas**

| Tarjeta | Cálculo |
|---|---|
| Total de usuarios | Cantidad de filas de `users`. |
| Nuevos este mes | Usuarios creados desde el inicio del mes actual. Incluye el porcentaje de crecimiento frente al mes anterior; si el mes anterior no tuvo registros, muestra "Sin datos del mes anterior". |
| Cuentas activas | Usuarios con `is_active` verdadero. |
| Cuentas inactivas | Total menos activas. |

**Gráficos** (el servidor entrega los datos en el atributo `data-charts` y `admin-users.js` los dibuja)

| Gráfico | Tipo | Contenido |
|---|---|---|
| Registros de cuentas | Línea | Registros de los últimos 30 días (diario) o de los últimos 12 meses (mensual), con botones para alternar. |
| Distribución por rol | Dona | Total de usuarios por rol: Administrador, Docente, Estudiante y Usuario. |
| Registros por rol (últimos 12 meses) | Barras | Registros mensuales de estudiantes, docentes y administradores. |
| Estado de cuentas | Dona | Cuentas activas frente a inactivas. |

**Pestañas y tabla**

| Pestaña | Filtro |
|---|---|
| Todos | Sin filtro |
| Estudiantes | `role = student` |
| Docentes | `role = teacher` |
| Administradores | `role = admin` |

Cada pestaña muestra su cantidad de usuarios. La tabla incluye ID, nombre, correo, rol actual, estado (Activo o Inactivo), fecha de registro y acciones.

### 4.2 Activar o desactivar una cuenta

1. En la fila del usuario, el administrador pulsa el botón de desactivar o de activar (con confirmación en el navegador para desactivar).
2. Se envía `DELETE /admin/users/{id}` (`admin.users.destroy`).
3. Si el usuario objetivo es el propio administrador, se rechaza con el mensaje "Por seguridad, no puedes modificar el estado de tu propia cuenta activa."
4. En caso contrario se invierte `is_active`, se guarda y se informa que la cuenta fue "activada" o "desactivada".
5. Efecto: un usuario inactivo no puede iniciar sesión (ver [`autenticacion.md`](./autenticacion.md)); el intento se registra como `inactive_account_access`.

### 4.3 Eliminar una cuenta de forma permanente

1. El administrador pulsa el botón de eliminar; el navegador solicita confirmación ("Esta acción eliminará permanentemente al usuario").
2. Se envía `DELETE /admin/users/permanent/{id}` (`admin.users.forceDelete`).
3. Si el objetivo es el propio administrador, se rechaza con el mensaje "Por seguridad, no puedes eliminar tu propia cuenta activa."
4. En caso contrario se elimina la fila. Por las reglas de integridad de la base de datos se eliminan en cascada sus `posts`, `news`, `user_devices` y `user_activity_logs`, y en `security_logs` el campo `user_id` queda en nulo (ver [`base-datos.md`](./base-datos.md), sección 4.4).

Para la propia cuenta, la tabla muestra la etiqueta "Tu cuenta" en lugar de los botones de acción.

### 4.4 Cambiar el rol de un usuario

1. El administrador abre el botón "Tablero de Gestión de Roles" (modal). El tablero muestra cuatro columnas: Administradores, Docentes, Estudiantes y Usuarios Generales, cada una con sus usuarios (nombre y correo).
2. En la tarjeta de un usuario elige un rol en el selector "Mover a..." y pulsa "Mover".
3. Se envía `PUT /admin/users/{id}/role` (`admin.users.updateRole`).
4. El servidor valida que `role` sea obligatorio y pertenezca a `User::ROLES`.
5. Si el administrador intenta asignarse a sí mismo un rol distinto de `admin`, se rechaza con el mensaje "Por seguridad, no puedes quitarte el rol de administrador a ti mismo."
6. En caso contrario se guarda el nuevo rol y se informa "El rol de {nombre} ha sido actualizado con éxito."

El cambio de rol modifica únicamente el campo `role`; no altera `is_approved` ni `is_active`.

### 4.5 Aprobación de docentes

**Origen de las solicitudes.** El docente se registra desde `/register/teacher`; la cuenta se crea con `role = teacher`, `is_approved = false` e `is_active = false` y no puede iniciar sesión (ver [`autenticacion.md`](./autenticacion.md), sección 4.4).

**Listado** (`GET /admin/teachers/pending`)

1. `AdminTeacherController::index` obtiene los usuarios con `is_approved = false` cuyo rol es `teacher` o que tienen `teaching_type` definido, de más reciente a más antiguo.
2. La vista muestra el total de pendientes y una tabla con nombre, correo, cédula (DNI), teléfono (código de país y número), fecha de nacimiento y tipo de postulación ("Creación de Cursos" o "Asesorías").
3. Si no hay solicitudes, muestra un mensaje de lista vacía.

**Aprobar** (`POST /admin/teachers/{id}/approve`)

1. Busca al usuario con `is_approved = false` y el ID indicado (si no existe, responde 404).
2. Genera una contraseña temporal de 10 caracteres y la guarda con hash.
3. Establece `role = teacher`, `is_approved = true`, `is_active = true` y `email_verified_at` con la fecha actual (el docente no necesita verificar su correo).
4. Envía por correo el mensaje de aprobación con el correo de acceso y la contraseña temporal, e indica que debe cambiarla desde su panel.
5. Vuelve a la pantalla con el mensaje de éxito.

**Denegar** (`DELETE /admin/teachers/{id}/deny`)

1. El navegador solicita confirmación ("denegar y eliminar por completo este registro").
2. Busca al usuario con `is_approved = false` y el ID indicado.
3. Envía el correo de rechazo.
4. Elimina el registro por completo y muestra el mensaje informativo correspondiente.

### 4.6 Consulta pública del estado de la solicitud

1. El docente abre `/teacher/status` e ingresa su correo y su número de documento.
2. `TeacherStatusController::check` normaliza el correo y valida: correo obligatorio y válido (máximo 255) y documento obligatorio, solo dígitos (máximo 30).
3. Aplica un límite de **5 consultas fallidas por 15 minutos** para cada combinación de correo e IP, además del límite de ruta `throttle:10,1`.
4. Busca un usuario con rol `teacher` que coincida con ambos datos. Si no existe (por ejemplo, porque la solicitud fue denegada y eliminada), responde que no se encontró la solicitud.
5. Si existe, limpia el contador de intentos y devuelve:

| Dato | Descripción |
|---|---|
| Nombre | Primer nombre del docente. |
| Estado | `approved` si `is_approved` es verdadero; `pending` en caso contrario. |
| Posición y total | Para las pendientes, el lugar en la cola de pendientes por orden de llegada y la cantidad total de pendientes. |
| Fecha de envío | `created_at` con formato `d/m/Y H:i`. |
| Tipo | "Creación de cursos" o "Asesorías" (o "Docencia" si no está definido). |

6. La vista muestra la cola o el mensaje de aprobación con enlace al inicio de sesión.

---

## 5. Rutas y Permisos

### 5.1 Rutas de administración

| Método | URI | Nombre | Controlador | Middleware |
|---|---|---|---|---|
| GET | `/admin/users` | `admin.users` | `UserController@index` | `auth`, `verified`, `can:manage-users` |
| PUT | `/admin/users/{id}/role` | `admin.users.updateRole` | `UserController@updateRole` | `auth`, `verified`, `can:manage-users` |
| DELETE | `/admin/users/{id}` | `admin.users.destroy` | `UserController@destroy` | `auth`, `verified`, `can:manage-users` |
| DELETE | `/admin/users/permanent/{id}` | `admin.users.forceDelete` | `UserController@forceDelete` | `auth`, `verified`, `can:manage-users` |
| GET | `/admin/teachers/pending` | `admin.teachers.pending` | `AdminTeacherController@index` | `auth`, `verified` |
| POST | `/admin/teachers/{id}/approve` | `admin.teachers.approve` | `AdminTeacherController@approve` | `auth`, `verified` |
| DELETE | `/admin/teachers/{id}/deny` | `admin.teachers.deny` | `AdminTeacherController@deny` | `auth`, `verified` |

### 5.2 Rutas públicas relacionadas

| Método | URI | Nombre | Controlador | Middleware |
|---|---|---|---|---|
| GET | `/teacher/status` | `teacher.status` | `TeacherStatusController@show` | `guest` |
| POST | `/teacher/status` | `teacher.status.check` | `TeacherStatusController@check` | `guest`, `throttle:10,1` |
| GET | `/register/teacher` | `register.teacher` | `AuthController@showTeacherRegister` | `guest` |
| GET | `/teacher/pending-approval` | `teacher.pending` | Closure | `guest` |

### 5.3 Permisos

| Capacidad | Estudiante | Docente | Administrador |
|---|:---:|:---:|:---:|
| Ver, filtrar y obtener estadísticas de usuarios | No (403) | No (403) | Sí |
| Cambiar roles | No (403) | No (403) | Sí |
| Activar, desactivar o eliminar cuentas | No (403) | No (403) | Sí |
| Ver y resolver solicitudes de docentes | Sesión verificada | Sesión verificada | Sí |
| Consultar el estado de su solicitud docente | Visitante | Visitante | No aplica |

Las rutas de aprobación y rechazo de docentes usan únicamente `auth` y `verified`, y `AdminTeacherController` no realiza una verificación de rol dentro de sus métodos (ver [`seguridad.md`](./seguridad.md), sección 5.3). El menú lateral muestra "Aceptar Docentes" solo al administrador.

### 5.4 Protecciones sobre la cuenta propia

| Acción | Restricción |
|---|---|
| Activar o desactivar | No se puede aplicar a la propia cuenta. |
| Eliminar | No se puede aplicar a la propia cuenta. |
| Cambiar el rol | No se puede asignar a la propia cuenta un rol distinto de `admin`. |
| Valores de rol | Solo los de `User::ROLES`. |

---

## 6. Datos

### 6.1 Campos de `users` utilizados

| Campo | Uso en el módulo |
|---|---|
| `id` | Identificador mostrado y usado en las rutas. |
| `name`, `email` | Datos mostrados en la tabla, el tablero y las solicitudes. |
| `role` | Filtro por pestaña, distribución, cambio de rol. |
| `is_active` | Estado Activo o Inactivo; se invierte con `destroy`. |
| `is_approved` | Define si la solicitud de docente está pendiente. |
| `teaching_type` | Tipo de postulación: `courses` o `advisories`. |
| `dni`, `country_code`, `phone`, `birthdate` | Datos de la solicitud de docente. |
| `password` | Se reemplaza con la contraseña temporal (hash) al aprobar. |
| `email_verified_at` | Se establece al aprobar al docente. |
| `created_at` | Orden del listado, estadísticas y gráficos de registros. |

### 6.2 Estructura de los datos de gráficos (`charts`)

| Clave | Contenido |
|---|---|
| `daily` | `labels` (30 fechas `d/m`) y `data` (registros por día). |
| `monthly` | `labels` (12 meses) y `data` (registros por mes). |
| `roleMonthly` | Una serie por rol (`student`, `teacher`, `admin`) con `role`, `label` y `data` mensual. |
| `roles` | Por cada rol (`admin`, `teacher`, `student`, `user`): `role`, `label`, `total`. |
| `status` | `active` e `inactive`. |

### 6.3 Etiquetas de rol

| Valor almacenado | Etiqueta mostrada |
|---|---|
| `admin` | Administrador |
| `teacher` | Docente |
| `student` | Estudiante |
| `user` | Usuario |

### 6.4 Parámetros operativos

| Parámetro | Valor |
|---|---|
| Registros por página | 10 |
| Contraseña temporal del docente | 10 caracteres generados aleatoriamente |
| Límite de consultas fallidas del estado de solicitud | 5 por 15 minutos (correo + IP) |
| Clave de caché del límite | Prefijo `teacher-status` seguido de correo e IP |
| Ventana de los gráficos | 30 días (diario) y 12 meses (mensual) |

### 6.5 Efectos de la eliminación de una cuenta

| Tabla relacionada | Resultado |
|---|---|
| `posts` | Se eliminan en cascada. |
| `news` | Se eliminan en cascada. |
| `user_devices` | Se eliminan en cascada. |
| `user_activity_logs` | Se eliminan en cascada. |
| `security_logs` | Se conservan con `user_id` en nulo. |