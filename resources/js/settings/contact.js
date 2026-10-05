document.addEventListener('DOMContentLoaded', function() {
    const faqBank = [
        { id: 1, category: 'cuenta', q: '¿Cómo recupero mi contraseña olvidada?', a: 'Haz clic en "¿Olvidaste tu contraseña?" en la pantalla de login e ingresa tu correo institucional para recibir el enlace de restauración.' },
        { id: 2, category: 'cuenta', q: '¿Cómo actualizo mi nombre o foto de perfil?', a: 'Dirígete a tu Panel de Usuario, entra en la sección "Perfil y Cuenta" y podrás modificar tus datos personales y fotografía.' },
        { id: 3, category: 'cuenta', q: '¿Por qué mi cuenta aparece bloqueada o suspendida?', a: 'Las cuentas pueden bloquearse por múltiples intentos fallidos de acceso. Contacta a soporte técnico para desbloquearla de inmediato.' },
        { id: 4, category: 'cuenta', q: '¿Cómo cambio mi correo electrónico registrado?', a: 'Por seguridad institucional, el cambio de correo principal debe solicitarse directamente enviando un ticket al administrador.' },
        { id: 5, category: 'cuenta', q: '¿Puedo iniciar sesión desde varios dispositivos a la vez?', a: 'Sí, EduNivelam permite sesiones activas simultáneas, las cuales puedes supervisar y cerrar desde tu historial de sesiones.' },
        { id: 6, category: 'cuenta', q: '¿Cómo cierro sesión de forma segura en equipos compartidos?', a: 'Haz clic en el botón rojo "Salir" en la esquina superior derecha o inferior del menú lateral para destruir tu sesión actual.' },
        { id: 7, category: 'cuenta', q: '¿Qué hago si no me llega el correo de activación de cuenta?', a: 'Revisa tu bandeja de correo no deseado (Spam). Si no aparece tras 10 minutos, solicita un nuevo envío desde la plataforma.' },
        { id: 8, category: 'cuenta', q: '¿Es obligatorio usar mi nombre real en el perfil?', a: 'Sí, al ser una plataforma académica oficial, se requiere el nombre legal registrado en la institución.' },
        { id: 9, category: 'cuenta', q: '¿Cómo activo la autenticación o seguridad adicional?', a: 'Puedes revisar el estado de tus accesos y dispositivos seguros desde la sección de Configuración > Seguridad.' },
        { id: 10, category: 'cuenta', q: '¿Quién puede ver mi información personal?', a: 'Solo los administradores del sistema y tus profesores asignados tienen acceso a tus datos de contacto académicos.' },
        { id: 11, category: 'cursos', q: '¿Cómo me inscribo a los cursos de nivelación?', a: 'Inicia sesión, ve al apartado de cursos disponibles y selecciona la materia en la que requieres nivelación para formalizar tu inscripción.' },
        { id: 12, category: 'cursos', q: '¿Cuál es la duración promedio de un curso de nivelación?', a: 'Los módulos están diseñados para completarse entre 4 y 8 semanas dependiendo de la intensidad horaria y la materia.' },
        { id: 13, category: 'cursos', q: '¿Puedo cursar dos materias de nivelación al mismo tiempo?', a: 'Sí, siempre y cuando los horarios de tutoría y evaluación no se crucen en tu carga académica.' },
        { id: 14, category: 'cursos', q: '¿Dónde encuentro el material de estudio y guías descargables?', a: 'Cada curso cuenta con un repositorio interno de archivos PDF y guías metodológicas accesibles desde su panel principal.' },
        { id: 15, category: 'cursos', q: '¿Qué pasa si repruebo una evaluación de nivelación?', a: 'El sistema habilitará una oportunidad de recuperación o tutoría de refuerzo según las políticas de tu docente a cargo.' },
        { id: 16, category: 'cursos', q: '¿Cómo sé cuáles son los prerrequisitos de una materia?', a: 'Al hacer clic sobre cualquier curso en la oferta académica, se detallarán las materias previas aprobadas requeridas.' },
        { id: 17, category: 'cursos', q: '¿Los cursos otorgan certificado de aprobación al finalizar?', a: 'Sí, al completar todas las actividades y superar el puntaje mínimo, la plataforma genera un certificado digital.' },
        { id: 18, category: 'cursos', q: '¿Cómo descargo mis notas y calificaciones parciales?', a: 'En tu resumen general de estudiante puedes visualizar el récord de notas y exportar tu historial académico.' },
        { id: 19, category: 'cursos', q: '¿Qué hago si un video o recurso multimedia no carga?', a: 'Verifica tu conexión a internet o limpia el caché del navegador. Si persiste, repórtalo en este formulario.' },
        { id: 20, category: 'cursos', q: '¿Puedo cancelar un curso de nivelación después de inscrito?', a: 'Sí, dentro de los primeros 5 días hábiles puedes solicitar la baja del curso con el coordinador académico.' },
        { id: 21, category: 'soporte', q: '¿Cuál es el horario de atención de soporte técnico?', a: 'Nuestro equipo atiende de Lunes a Viernes de 8:00 a.m. a 6:00 p.m. de forma continua.' },
        { id: 22, category: 'soporte', q: '¿Qué navegadores son compatibles con EduNivelam?', a: 'La plataforma está optimizada para Google Chrome, Mozilla Firefox, Microsoft Edge y Safari en sus versiones más recientes.' },
        { id: 23, category: 'soporte', q: '¿Por qué la página me muestra error 404 al navegar?', a: 'Un error 404 indica que la página no existe o el enlace ha caducado. Vuelve al menú principal o recarga la pestaña.' },
        { id: 24, category: 'soporte', q: '¿La plataforma cuenta con aplicación móvil oficial?', a: 'Actualmente EduNivelam opera mediante una interfaz web responsiva perfectamente adaptada para teléfonos y tablets.' },
        { id: 25, category: 'soporte', q: '¿Cómo reporto un error visual o fallo en la interfaz?', a: 'Puedes utilizar este mismo formulario de contacto seleccionando el asunto de soporte técnico y detallando el inconveniente.' },
        { id: 26, category: 'soporte', q: '¿Qué hago si el sistema se queda cargando infinitamente?', a: 'Comprueba tu conexión o intenta abrir la plataforma en una pestaña de incógnito para descartar conflictos de extensiones.' },
        { id: 27, category: 'soporte', q: '¿Mis datos e información están seguros en la plataforma?', a: 'Sí, utilizamos encriptación de datos, protección contra ataques CSRF, Rate Limiting y Cloudflare Turnstile.' },
        { id: 28, category: 'soporte', q: '¿Cómo funcionan los tickets de ayuda y asistencia?', a: 'Los tickets enviados por este medio son canalizados directamente a nuestra bandeja IMAP para gestión prioritaria.' },
        { id: 29, category: 'soporte', q: '¿Se requiere instalar algún software adicional en mi PC?', a: 'No, todo funciona directamente desde tu navegador web sin necesidad de descargas o plugins pesados.' },
        { id: 30, category: 'soporte', q: '¿Qué debo hacer si aparece "Petición bloqueada o limitada"?', a: 'Esto ocurre por seguridad si realizas demasiados intentos seguidos. Espera unos minutos e inténtalo de nuevo.' },
        { id: 31, category: 'soporte', q: '¿Cómo funciona la verificación de seguridad de Cloudflare?', a: 'Valida automáticamente en segundo plano que las peticiones provengan de usuarios reales y no de bots maliciosos.' },
        { id: 32, category: 'soporte', q: '¿Por qué aparece un error 419 (Page Expired)?', a: 'Ocurre cuando tu sesión en la página ha expirado por inactividad. Recarga la pestaña y vuelve a intentarlo.' },
        { id: 33, category: 'soporte', q: '¿Dónde consulto el historial de mis mensajes enviados?', a: 'Puedes ver las respuestas y seguimientos en tu correo electrónico personal asociado a la plataforma.' },
        { id: 34, category: 'soporte', q: '¿Se realizan respaldos automáticos de las tareas y notas?', a: 'Sí, el servidor almacena copias de seguridad periódicas de todas las bases de datos académicas.' },
        { id: 35, category: 'soporte', q: '¿Con quién me comunico para emergencias fuera de horario?', a: 'Puedes dejar tu mensaje en este formulario y será atendido en orden de llegada al siguiente día hábil.' },
        { id: 36, category: 'docentes', q: '¿Cómo puedo contactar directamente a los instructores?', a: 'Cuentas con un módulo de mensajería interna y foros de asesoría asignados a cada curso activo.' },
        { id: 37, category: 'docentes', q: '¿Cuánto tardan los profesores en responder mis dudas?', a: 'El tiempo estimado de respuesta por parte del cuerpo docente es de máximo 24 a 48 horas hábiles.' },
        { id: 38, category: 'docentes', q: '¿Cómo sé quién es el profesor encargado de mi nivelación?', a: 'En la tarjeta de descripción de tu curso aparecerá el nombre y correo institucional del docente tutor.' },
        { id: 39, category: 'docentes', q: '¿Puedo solicitar tutorías personalizadas con un instructor?', a: 'Sí, mediante los canales de asesoría acordados en los foros de cada materia dentro de la plataforma.' },
        { id: 40, category: 'docentes', q: '¿Cómo evalúan los instructores las actividades enviadas?', a: 'Utilizan rúbricas de calificación digital que puedes revisar junto con los comentarios de retroalimentación.' },
        { id: 41, category: 'docentes', q: '¿Qué hago si un profesor no califica a tiempo mi tarea?', a: 'Puedes reportarlo mediante el buzón de soporte académico para que la coordinación se comunique con el docente.' },
        { id: 42, category: 'docentes', q: '¿Los profesores tienen horarios de atención sincrónica?', a: 'Sí, cada docente publica sus horas de asesoría virtual o presencial al inicio del período de nivelación.' },
        { id: 43, category: 'docentes', q: '¿Cómo cambio de grupo o de tutor asignado?', a: 'Los cambios de grupo deben justificarse con motivos académicos válidos ante la administración de la institución.' },
        { id: 44, category: 'docentes', q: '¿Pueden los instructores ver mis contraseñas o datos privados?', a: 'No, por privacidad institucional ningún profesor tiene acceso a tus credenciales de acceso.' },
        { id: 45, category: 'docentes', q: '¿Cómo califico el desempeño de un instructor al terminar?', a: 'Al finalizar el curso se habilitará una encuesta de satisfacción docente anónima en tu panel principal.' },
        { id: 46, category: 'docentes', q: '¿Qué son las aprobaciones de docentes en el sistema?', a: 'Es el proceso mediante el cual el administrador valida y autoriza a los profesores registrados para impartir cursos.' },
        { id: 47, category: 'docentes', q: '¿Cómo participan los docentes en el Helpdesk institucional?', a: 'Los profesores pueden colaborar atendiendo tickets técnicos y académicos asignados a su área de conocimiento.' },
        { id: 48, category: 'docentes', q: '¿Pueden los profesores enviar anuncios globales a los cursos?', a: 'Sí, mediante el tablón de anuncios del campus virtual para notificar fechas y eventos importantes.' },
        { id: 49, category: 'docentes', q: '¿Qué hago si tengo un inconveniente personal con un docente?', a: 'Puedes elevar un reporte formal de manera confidencial a través de la dirección académica de la institución.' },
        { id: 50, category: 'docentes', q: '¿Cómo se asignan los instructores a los nuevos cursos?', a: 'La asignación la realiza automáticamente el sistema según la especialidad y disponibilidad horaria registrada.' }
    ];

    const mainFaqIds = [1, 11, 21, 36, 2, 14, 25, 37, 30, 46];
    let currentIds = [...mainFaqIds];

    const accordionContainer = document.getElementById('faqAccordion');
    const resetBtn = document.getElementById('resetFaqBtn');

    if (!accordionContainer) return;

    function renderFaqs(idsToRender) {
        accordionContainer.innerHTML = '';

        idsToRender.forEach((id, index) => {
            const item = faqBank.find(f => f.id === id);
            if (!item) return;

            const collapseId = `collapseFaq${item.id}`;
            const headingId = `headingFaq${item.id}`;

            const html = `
                <div class="accordion-item border rounded-3 mb-2 overflow-hidden shadow-sm">
                    <h2 class="accordion-header" id="${headingId}">
                        <button class="accordion-button collapsed fw-bold text-dark bg-light px-3 py-2 fs-7" 
                                type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#${collapseId}" 
                                aria-expanded="false" 
                                aria-controls="${collapseId}"
                                data-faq-id="${item.id}"
                                data-category="${item.category}">
                            ${item.q}
                        </button>
                    </h2>
                    <div id="${collapseId}" class="accordion-collapse collapse" aria-labelledby="${headingId}" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted small py-2 px-3 bg-white">
                            ${item.a}
                        </div>
                    </div>
                </div>
            `;
            accordionContainer.innerHTML += html;
        });

        attachAccordionEvents();
    }

    function attachAccordionEvents() {
        const buttons = accordionContainer.querySelectorAll('.accordion-button');
        buttons.forEach(button => {
            button.addEventListener('click', function() {
                const clickedId = parseInt(this.getAttribute('data-faq-id'));
                const clickedCategory = this.getAttribute('data-category');
                const availableRelated = faqBank.filter(f => f.category === clickedCategory && !currentIds.includes(f.id));

                if (availableRelated.length > 0) {
                    const newRelatedIds = availableRelated.slice(0, 2).map(f => f.id);
                    let otherCategoryIndexes = [];
                    currentIds.forEach((id, idx) => {
                        const qObj = faqBank.find(f => f.id === id);
                        if (qObj && qObj.category !== clickedCategory && id !== clickedId) {
                            otherCategoryIndexes.push(idx);
                        }
                    });

                    if (otherCategoryIndexes.length >= 2) {
                        const toRemove1 = otherCategoryIndexes[otherCategoryIndexes.length - 1];
                        const toRemove2 = otherCategoryIndexes[otherCategoryIndexes.length - 2];
                        currentIds.splice(Math.max(toRemove1, toRemove2), 1);
                        currentIds.splice(Math.min(toRemove1, toRemove2), 1);
                        currentIds.push(...newRelatedIds);
                        renderFaqs(currentIds);
                        if (resetBtn) resetBtn.style.display = 'block';
                        setTimeout(() => {
                            const targetBtn = accordionContainer.querySelector(`[data-faq-id="${clickedId}"]`);
                            if (targetBtn && targetBtn.classList.contains('collapsed')) {
                                targetBtn.click();
                            }
                        }, 50);
                    }
                }
            });
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            currentIds = [...mainFaqIds];
            renderFaqs(currentIds);
            this.style.display = 'none';
        });
    }

    renderFaqs(currentIds);
});