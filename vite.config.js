import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue2 from '@vitejs/plugin-vue2';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/sass/new-custom.scss',
                'resources/sass/new-custom-dark.scss',
                'resources/sass/custom-auth.scss',
                'resources/assets/sass/new-custom.scss',
                'resources/assets/sass/pagination-fix.scss',
                'resources/assets/sass/new-custom-dark.scss',
                'resources/assets/sass/custom-auth.scss',
            ],
            refresh: true,
        }),
        vue2(),
    ],
    resolve: {
        alias: {
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
            '~jquery': path.resolve(__dirname, 'node_modules/jquery'),
            '~select2': path.resolve(__dirname, 'node_modules/select2'),
            '~slick-carousel': path.resolve(__dirname, 'node_modules/slick-carousel'),
            '~intl-tel-input': path.resolve(__dirname, 'node_modules/intl-tel-input'),
            '~quill': path.resolve(__dirname, 'node_modules/quill'),
            '~font-awesome': path.resolve(__dirname, 'node_modules/font-awesome'),
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    'third-party': [
                        'jquery',
                        'bootstrap',
                        'slick-carousel',
                        'chart.js',
                        'intl-tel-input',
                        'autonumeric',
                        'quill',
                    ],
                    'front-third-party': [
                        'intl-tel-input',
                        'ion-rangeslider',
                        'select2',
                        'sweetalert',
                        'toastr',
                        'slick-carousel',
                    ],
                    pages: [
                        'resources/assets/js/custom/helpers.js',
                        'resources/assets/js/custom/custom.js',
                        'resources/assets/js/turbo.js',
                    ],
                },
            },
        },
    },
}); 