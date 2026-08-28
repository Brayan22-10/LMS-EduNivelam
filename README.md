# Sistema CMS

Proyecto desarrollado como parte de las prácticas de desarrollo de software seguro con Laravel, aplicando buenas prácticas de ingeniería, control de versiones y protección desde las etapas tempranas del ciclo de vida (DevSecOps).

## Descripción del Problema
Este proyecto implementa una aplicación web funcional y defendible para la gestión de incidentes. A diferencia de un desarrollo tradicional centrado únicamente en que funcione, este sistema incorpora desde su inicio controles estrictos de seguridad por diseño: ocultación de secretos, validación de entradas de usuario, protección contra ataques CSRF, estructuración limpia de rutas mediante controladores y separación de capas.

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

| Método HTTP | URI | Nombre de Ruta | Controlador@Método | Tipo de Acceso |
| :--- | :--- | :--- | :--- | :--- |
| GET|HEAD | `/` | `home` | `PageController@home` | Público |
| GET|HEAD | `about` | `about` | `PageController@about` | Público |
| GET|HEAD | `contact` | `contact` | `ContactController@create` | Público |
| POST | `contact` | `contact.store` | `ContactController@store` | Público (Con CSRF) |
| GET|HEAD | `dashboard` | `dashboard` | `DashboardController@index` | Privado (Auth) |
| GET|HEAD | `directory` | `site.directory` | `SiteController@directory` | Público |
| GET|HEAD | `incidentes` | `incidentes.index` | `IncidentController@index` | Público |
| POST | `incidentes` | `incidentes.store` | `IncidentController@store` | Privado (Auth) |
| GET|HEAD | `incidentes/create` | `incidentes.create` | `IncidentController@create` | Privado (Auth) |
| GET|HEAD | `incidentes/{incidente}` | `incidentes.show` | `IncidentController@show` | Público |
| PUT|PATCH | `incidentes/{incidente}` | `incidentes.update` | `IncidentController@update` | Privado (Auth/Policy) |
| DELETE | `incidentes/{incidente}` | `incidentes.destroy` | `IncidentController@destroy` | Privado (Auth/Policy) |
| GET|HEAD | `incidentes/{incidente}/edit` | `incidentes.edit` | `IncidentController@edit` | Privado (Auth/Policy) |
| GET|HEAD | `login` | `login` | `AuthController@showLogin` | Público |
| POST | `login` | `login.store` | `AuthController@login` | Público |
| POST | `logout` | `logout` | `AuthController@logout` | Privado (Auth) |
| GET|HEAD | `posts` | `posts.index` | `PostController@index` | Público |
| POST | `posts` | `posts.store` | `PostController@store` | Privado (Auth) |
| GET|HEAD | `posts/create` | `posts.create` | `PostController@create` | Privado (Auth) |
| GET|HEAD | `posts/{post}` | `posts.show` | `PostController@show` | Público |
| PUT|PATCH | `posts/{post}` | `posts.update` | `PostController@update` | Privado (Auth/Policy) |
| DELETE | `posts/{post}` | `posts.destroy` | `PostController@destroy` | Privado (Auth/Policy) |
| GET|HEAD | `posts/{post}/edit` | `posts.edit` | `PostController@edit` | Privado (Auth/Policy) |
| GET|HEAD | `reports` | `site.reports` | `SiteController@reports` | Público |