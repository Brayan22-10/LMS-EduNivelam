import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/home.css',
                'resources/js/home.js',
                'resources/css/login.css',
                'resources/js/login.js',
                'resources/css/register.css',
                'resources/js/register.js',
                'resources/css/dashboard.css',
                'resources/js/dashboard.js',
                'resources/css/about.css',
                'resources/js/about.js',
                'resources/css/courses.css',
                'resources/js/courses.js',
                'resources/css/instructors.css',
                'resources/js/instructors.js',
                'resources/css/admin-users.css',
                'resources/js/admin-users.js',
                'resources/css/email-admin.css',
                'resources/css/logout-animation.css',
                'resources/js/logout-animation.js',
                'resources/css/auth-pages.css',
                'resources/js/auth-pages.js',
                'resources/css/teacher-status.css',
                'resources/js/teacher-status.js',

                'resources/css/settings/settings.css',
                'resources/js/settings/settings.js',
                'resources/js/settings/password-validation.js',
                'resources/js/settings/profile-settings.js',
                'resources/js/settings/appearance-settings.js',
                'resources/js/settings/server-actions.js',

                'resources/js/contact.js',
                'resources/js/inactivity.js',
                'resources/js/banner-form.js',
                'resources/js/news-cropper.js',
                'resources/js/news-modal.js',
                'resources/js/profile-cropper.js',

                'resources/css/errors/error-401.css',
                'resources/css/errors/error-403.css',
                'resources/css/errors/error-404.css',
                'resources/css/errors/error-419.css',
                'resources/css/errors/error-429.css',
                'resources/css/errors/maintenance.css',
                'resources/css/errors/google-error.css',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});