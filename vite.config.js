import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',

                // Auth
                'resources/js/auth/register-couple.js',
                'resources/css/auth/login.css',
                'resources/css/auth/register-couple.css',
                'resources/js/auth/register-vendor.js',
                'resources/css/auth/register-vendor.css',

                // Couple layout and pages
                'resources/css/couple/layout-couple.css',
                'resources/js/couple/layout-couple.js',
                'resources/css/couple/dashboard.css',
                'resources/js/couple/dashboard.js',
                'resources/css/couple/budget.css',
                'resources/js/couple/budget.js',
                'resources/css/couple/ai-budget.css',
                'resources/js/couple/ai-budget.js',
                'resources/css/couple/settings.css',
                'resources/js/couple/settings.js',
                'resources/css/couple/tasks.css',
                'resources/js/couple/tasks.js',
                'resources/css/couple/vendorlist.css',
                'resources/js/couple/vendorlist.js',

                // Vendor
                'resources/css/vendor/layout-vendor.css',
                'resources/js/vendor/layout-vendor.js',
                'resources/css/vendor/dashboard.css',
                'resources/js/vendor/dashboard.js',
                'resources/css/vendor/settings.css',
                'resources/js/vendor/settings.js',
                'resources/css/vendor/service.css',
                'resources/js/vendor/service.js',
                'resources/css/vendor/booking.css',
                'resources/js/vendor/booking.js',
                'resources/css/vendor/notification.css',
                'resources/js/vendor/notification.js',

                // Admin
                'resources/css/admin/admin.css',
                'resources/js/admin/admin.js',
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
