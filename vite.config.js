import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 
                'resources/js/auth/register-couple.js', 'resources/css/auth/login.css', 
                'resources/css/auth/register-couple.css', 'resources/js/auth/register-vendor.js',
                'resources/css/auth/register-vendor.css', 'resources/css/couple/layout-couple.css',
                'resources/js/couple/layout-couple.js', 'resources/css/couple/dashboard.css',
                'resources/js/couple/dashboard.js', 'resources/css/couple/budget.css',
                'resources/js/couple/budget.js', 'resources/css/couple/settings.css',
                'resources/js/couple/settings.js', 'resources/css/couple/tasks.css',
                'resources/js/couple/tasks.js'],
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
