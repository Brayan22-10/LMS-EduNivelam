# EduNivelam - Plataforma Educativa y Marketplace de Cursos de Nivelación

Proyecto desarrollado en Laravel como parte de las prácticas de desarrollo de software seguro (DevSecOps), aplicando buenas prácticas de ingeniería, control de versiones y protección desde las etapas tempranas del ciclo de vida del software.

## Descripción del Problema y Tipo de Sistema
**EduNivelam** es una plataforma de marketplace educativo y LMS (Learning Management System) impulsado por la comunidad. Está diseñada específicamente para ofrecer cursos de nivelación a nivel estudiantil, enfocándose en áreas clave como matemáticas, lectura crítica y habilidades blandas. 

El sistema opera bajo un modelo colaborativo donde los usuarios pueden registrarse, crear y publicar sus propios cursos orientados a la certificación administrativa y al listado público. Arquitectónicamente integra un CMS robusto con controles estrictos de seguridad por diseño: ocultación de secretos, validación de entradas de usuario, protección contra ataques CSRF, limitación de tasa (*rate-limiting*), control de acceso basado en roles y políticas de autorización (`@can`), además de automatización de correos mediante protocolos SMTP e IMAP.

## Componentes y Módulos Desarrollados

1. **Navegación Pública y Autenticación (CMS Base):**
   - **Páginas Principales (`/`, `about`, `courses`, `instructors`, `contact`):** Vistas públicas para explorar la oferta académica, el directorio de instructores y secciones informativas.
   - **Registro y Autenticación (`/register`, `/login`):** Pasarela de acceso con validación estricta de datos, hashing seguro de contraseñas y control de intentos masivos (*rate limiting*).
   - **Barra de Navegación (`layouts.app`):** Menú global adaptativo para alternar fluidamente entre secciones públicas y privadas.

2. **Módulo de Gestión de Cursos y Contenidos (CRUD):**
   - Sistema dinámico integrado a la base de datos relacional para que los usuarios autenticados e instructores puedan crear, leer, actualizar y eliminar contenidos y cursos bajo esquemas de políticas de autorización.

3. **Módulo de Contacto y Envío de Correos (SMTP):**
   - **Formulario Público (`/contacto`):** Interfaz protegida con validaciones de servidor y protección contra ataques CSRF.
   - **Clase Mailable (`ContactMessage`):** Envío de correos corporativos y de soporte a través de un transporte SMTP (configurado con Mailtrap) utilizando plantillas en Blade.

4. **Recepción Automatizada y Protocolo IMAP:**
   - **Comando Artisan (`php artisan mail:receive`):** Tarea automatizada de servidor que se conecta mediante IMAP a la bandeja de entrada para consultar y extraer mensajes nuevos no leídos (`unseen`).

5. **Persistencia e Idempotencia:**
   - Almacenamiento estructurado en la tabla `received_emails` para registrar remitente, asunto, cuerpo, fecha y estado de lectura.
   - Uso de índices únicos (`message_id`) y métodos como `firstOrCreate` para garantizar la sincronización segura del buzón sin duplicar registros.

6. **Panel de Administración y Dashboard (UI / CSS):**
   - Interfaz privada protegida por directivas de autorización (`@can`) donde el administrador gestiona usuarios (`/admin/users`), revisa métricas y administra la Bandeja de Entrada IMAP integrada (`/admin/correos`).
   - Estilos optimizados (`dashboard.css`) para garantizar una separación limpia entre la barra de navegación, el layout principal y la barra lateral de administración.

## Requisitos del Entorno
Herramientas mínimas requeridas para levantar el proyecto localmente:
- PHP
- Composer
- Node.js y npm
- Git
- Base de datos local (MySQL)

## Pasos de Instalación y Configuración

1. **Clonar el repositorio:**
   Git Bash
   git clone <https://github.com/Brayan22-10/cms>
   cd cms

2. **Instalar dependencias de PHP**
   Git Bash: 
   composer install

3. **Instalar dependencias de Node.js:**
   Git Bash: 
   npm install
   npm run build

4. **Configurar el entorno:**
   Git Bash:
   cp .env.example .env
   php artisan key:generate

5. **Ejecutar migraciones:**
   Git Bash:
   php artisan migrate

6. **Iniciar el servidor local:**
   Git Bash:
   php artisan serve

# Sistema de Gestión 

## Estructura Detallada de Rutas

| Método HTTP | URI | Nombre de Ruta | Controlador@Método / Acción | Tipo de Acceso / Protección |
| :--- | :--- | :--- | :--- | :--- |
| `GET|HEAD` | `/` | `home` | `PageController@home` | Público |
| `GET|HEAD` | `about` | `about` | `PageController@about` | Público |
| `GET|HEAD` | `admin/check-imap` | `admin.check.imap` | `ContactController@validateImapConnection` | Privado / Admin |
| `GET|HEAD` | `admin/correos` | `admin.emails.index` | `ReceivedEmailController@index` | Privado / Admin |
| `GET|HEAD` | `admin/correos/{id}` | `admin.emails.show` | `ReceivedEmailController@show` | Privado / Admin |
| `GET|HEAD` | `admin/users` | `admin.users` | `Admin\UserController@index` | Privado / Admin |
| `POST` | `check-session-status` | — | `routes/web.php:40` | Autenticado |
| `GET|HEAD` | `contact` | `contact` | `ContactController@create` | Público |
| `POST` | `contact` | `contact.store` | `ContactController@store` | Público (Con CSRF / Throttle) |
| `GET|HEAD` | `courses` | `courses` | `SiteController@reports` | Público |
| `GET|HEAD` | `dashboard` | `dashboard` | `DashboardController@index` | Privado (Auth) |
| `GET|HEAD` | `incidentes` | `incidentes.index` | `IncidentController@index` | Público |
| `POST` | `incidentes` | `incidentes.store` | `IncidentController@store` | Privado (Auth) |
| `GET|HEAD` | `incidentes/create` | `incidentes.create` | `IncidentController@create` | Privado (Auth) |
| `GET|HEAD` | `incidentes/{incidente}` | `incidentes.show` | `IncidentController@show` | Público |
| `PUT|PATCH` | `incidentes/{incidente}` | `incidentes.update` | `IncidentController@update` | Privado (Auth/Policy) |
| `DELETE` | `incidentes/{incidente}` | `incidentes.destroy` | `IncidentController@destroy` | Privado (Auth/Policy) |
| `GET|HEAD` | `incidentes/{incidente}/edit` | `incidentes.edit` | `IncidentController@edit` | Privado (Auth/Policy) |
| `GET|HEAD` | `instructors` | `instructors` | `SiteController@directory` | Público |
| `GET|HEAD` | `login` | `login` | `Auth\AuthController@showLogin` | Público |
| `POST` | `login` | `login.store` | `Auth\AuthController@login` | Público (Rate Limited) |
| `POST` | `logout` | `logout` | `Auth\AuthController@logout` | Privado (Auth) |
| `GET|HEAD` | `posts` | `posts.index` | `PostController@index` | Público |
| `POST` | `posts` | `posts.store` | `PostController@store` | Privado (Auth) |
| `GET|HEAD` | `posts/create` | `posts.create` | `PostController@create` | Privado (Auth) |
| `GET|HEAD` | `posts/{post}` | `posts.show` | `PostController@show` | Público |
| `PUT|PATCH` | `posts/{post}` | `posts.update` | `PostController@update` | Privado (Auth/Policy) |
| `DELETE` | `posts/{post}` | `posts.destroy` | `PostController@destroy` | Privado (Auth/Policy) |
| `GET|HEAD` | `posts/{post}/edit` | `posts.edit` | `PostController@edit` | Privado (Auth/Policy) |
| `GET|HEAD` | `register` | `register` | `Auth\AuthController@showRegister` | Público |
| `POST` | `register` | `register.store` | `Auth\AuthController@register` | Público |
| `GET|HEAD` | `storage/{path}` | `storage.local` | Framework Filesystem Service | Público / Configurado |
| `PUT` | `storage/{path}` | `storage.local.upload` | Framework Filesystem Service | Privado |
| `GET|HEAD` | `up` | — | ApplicationBuilder Health Check | Público |