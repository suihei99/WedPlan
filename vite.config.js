import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 
                'resources/js/auth/register-couple.js', 'resources/css/auth/login.css', 
                'resources/css/auth/register-couple.css', 'resources/js/auth/register-vendor.js',
                'resources/css/auth/register-vendor.css'],
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
