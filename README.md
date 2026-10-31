# EduNivelam: Plataforma de fortalecimiento en habilidades blandas y orientación profesional mediante precarreras universitarias formativas para estudiantes en Ubaté

> **Integrantes:**  
> Brayan Sebastian Castillo López
> Johan Santiago Castillo Ruiz

> **Proyecto Académico Universidad de Cundinamarca**  
> EduNivelam es una plataforma LMS enfocado en transformar la orientación y el desarrollo personal de los estudiantes en Ubaté. Su objetivo principal es el fortalecimiento de habilidades blandas a través de una oferta diversificada de cursos formativos. Como aspecto innovador, la plataforma integra pruebas de orientación vocacional y cursos de precarreras universitarias, permitiendo que los estudiantes experimenten los contenidos reales de distintas áreas académicas antes de tomar una decisión definitiva sobre su futuro profesional.

---

A nivel de ingeniería, el proyecto está desarrollado bajo el modelo arquitectónico MVC (Modelo-Vista-Controlador) utilizando el framework Laravel, con esto garantizar una separación entre la lógica de negocio, las vistas y los controladores. Asimismo, su ciclo de vida de software se establece mediante la metodología agíl Scrumban, integrando la planificación por iteraciones con un flujo visual y continuo de tareas orientado a la calidad, la seguridad por diseño y la escalabilidad del sistema.

---

## 1. Patrón Arquitectónico: Modelo-Vista-Controlador (MVC)
El sistema está estructurado bajo el modelo arquitectónico **MVC (Modelo-Vista-Controlador)**.
* **Modelo (Model):** Representa la capa de datos y la lógica de negocio utilizando Eloquent ORM. Se encarga de la comunicación directa con la base de datos relacional, definiendo relaciones (Eloquent Relationships), restricciones de integridad, scopes y mutadores.
* **Vista (View):** Capa de presentación construida con plantillas Blade, complementada con componentes visuales de Bootstrap y recursos compilados mediante Vite. Separa las interfaces públicas del panel administrativo y de los roles específicos.
* **Controlador (Controller):** Capa intermediaria que gestiona las peticiones HTTP entrantes, procesa la lógica de control, interactúa con los modelos y retorna las respuestas o redirecciones correspondientes aplicando políticas de autorización (`Policies` / `@can`).

---

## 2. Metodología de Desarrollo: Scrumban
El ciclo de vida del desarrollo de software (SDLC) de **EduNivelam** se gestiona mediante la metodología agíl **Scrumban**, combinando la estructura de iteraciones y roles de **Scrum** con el flujo de trabajo continuo y visual de **Kanban**:
* **Gestión de Tareas y Seguimiento:** Uso activo de herramientas de gestión de proyectos (Jira) para estructurar el *Product Backlog* y el *Sprint Backlog*.
* **Priorización y Estimación:** Aplicación de criterios de priorización **MoSCoW** (Must have, Should have, Could have, Won't have) y matrices de impacto/esfuerzo.
* **Historias de Usuario y Calidad:** Redacción de requerimientos funcionales mediante historias de usuario detalladas, acompañadas de criterios de aceptación bajo la estructura *Given-When-Then*.
* **Control de Versiones y Ramas:** Flujo de trabajo organizado en Git mediante ramas estructuradas (`main`, `develop`, ramas de características `feature/...`, seguridad `security/...` y documentación `docs/...`).

---

## 3. Stack Tecnológico Detallado
* **Backend:** PHP / Laravel Framework.
* **Base de Datos:** MySQL (Relacional).
* **Frontend y UI:** Blade Templates, Bootstrap CSS/JS, Vite (Bundler de activos frontend) y estilos personalizados.
* **Seguridad y Control:** Hashing de contraseñas, protección contra ataques CSRF, control de intentos (*rate-limiting*) en pasarelas de autenticación y RBAC (Control de Acceso Basado en Roles).
* **Integraciones y Automatización:** 
  * Protocolo **SMTP** para el envío dinámico de correos corporativos y de contacto.
  * Protocolo **IMAP** mediante comandos personalizados de Artisan (`php artisan mail:receive`) para la lectura automatizada y sincronización de bandejas de entrada no leídas (`unseen`).
  * Autenticación y Gestión de Identidad con **Gmail**: Integración de flujos de autenticación basados en cuentas de Google (OAuth 2.0), permitiendo un acceso seguro y unificado para los usuarios dentro de la plataforma.

---

## 4. Requisitos del Entorno de Desarrollo
Para levantar y ejecutar el proyecto en un entorno local, se requiere el siguiente software instalado:
* **PHP** (versión compatible con Laravel)
* **Composer** (Gestor de dependencias de PHP)
* **Node.js y npm** (Gestor de paquetes de frontend)
* **Git** (Control de versiones)
* **Servidor de Base de Datos MySQL**

---

## 5. Guía de Instalación y Configuración Paso a Paso

Ejecuta los siguientes comandos en tu terminal (**Git Bash** o consola compatible):

1. **Clonar el repositorio:**
   ```bash
   git clone [https://github.com/Brayan22-10/cms](https://github.com/Brayan22-10/cms)
   cd cms

2. **Instalar dependencias de PHP:**
    ```bash
    composer install  

4. **Instalar y compilar dependencias de Frontend:**
    ```bash
      npm install
      npm run build 

6. **Configurar las variables de entorno:**
    ```bash
    cp .env.example .env
    php artisan key:generate

8. **Ejecutar migraciones de base de datos:**
    ```bash
    php artisan migrate

10. **Iniciar el servidor local de desarrollo:**
    ```bash
    php artisan serve

---

## 6. Estructura de Directorios del Proyecto
El proyecto mantiene una organización modular bajo el estándar del framework Laravel y la carpeta de documentación técnica:

```text
cms/
├── app/                  # Controladores, Modelos, Form Requests y Políticas de Autorización
├── bootstrap/            # Archivos de arranque del framework
├── config/               # Archivos de configuración general (base de datos, mail, servicios)
├── database/             # Migraciones, seeders y factorías para la base de datos relacional
├── public/               # Punto de entrada público y recursos compilados
├── resources/            # Vistas Blade, componentes UI y assets de frontend
├── routes/               # Definición de rutas web y endpoints protegidos por roles
├── storage/              # Archivos logs, caché y almacenamiento local
├── tests/                # Pruebas automatizadas del sistema
├── docs/                 # Documentación técnica detallada por roles y componentes
│   ├── general/          # Módulos transversales y base de datos
│   ├── admin/            # Panel de control y gestión del administrador
│   ├── docente/          # Creación de contenidos y habilidades blandas
│   └── estudiante/       # Vistas de aprendizaje y precarreras
└── ...
