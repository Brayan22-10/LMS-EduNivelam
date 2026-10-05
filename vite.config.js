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
                'resources/css/dashboard.css',
                'resources/js/dashboard.js',
                'resources/css/register.css',
                'resources/js/register.js',
                'resources/css/admin-users.css',
                'resources/js/admin-users.js',
                'resources/css/settings/settings.css',
                'resources/js/settings/password-validation.js',
                'resources/js/settings/profile-settings.js',
                'resources/js/settings/appearance-settings.js',
                'resources/js/settings/server-actions.js',
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