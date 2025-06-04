import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/admin.js',
                'resources/js/frontend.js'
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
            '~jquery': path.resolve(__dirname, 'node_modules/jquery'),
            '~fontawesome': path.resolve(__dirname, 'node_modules/@fortawesome/fontawesome-free'),
            '~select2': path.resolve(__dirname, 'node_modules/select2'),
            '~datatables': path.resolve(__dirname, 'node_modules/datatables.net'),
            '~sweetalert2': path.resolve(__dirname, 'node_modules/sweetalert2'),
            '~alpinejs': path.resolve(__dirname, 'node_modules/alpinejs'),
            '~moment': path.resolve(__dirname, 'node_modules/moment'),
            '~lodash': path.resolve(__dirname, 'node_modules/lodash'),
            '~axios': path.resolve(__dirname, 'node_modules/axios'),
            '~chartjs': path.resolve(__dirname, 'node_modules/chart.js'),
            '~apexcharts': path.resolve(__dirname, 'node_modules/apexcharts'),
            '~flatpickr': path.resolve(__dirname, 'node_modules/flatpickr'),
            '~dropzone': path.resolve(__dirname, 'node_modules/dropzone'),
            '~swiper': path.resolve(__dirname, 'node_modules/swiper'),
            '~slick': path.resolve(__dirname, 'node_modules/slick-carousel')
        }
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': [
                        'jquery',
                        'bootstrap',
                        '@popperjs/core'
                    ],
                    'ui-libs': [
                        'select2',
                        'datatables.net',
                        'sweetalert2',
                        'alpinejs'
                    ],
                    'charts': [
                        'chart.js',
                        'apexcharts'
                    ],
                    'utilities': [
                        'moment',
                        'lodash',
                        'axios'
                    ]
                }
            }
        },
        cssCodeSplit: true,
        sourcemap: true
    },
    server: {
        hmr: {
            host: 'localhost',
        },
    },
});