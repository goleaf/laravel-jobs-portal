import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue2 from '@vitejs/plugin-vue2';
import path from 'path';
import fs from 'fs';

// Check if files exist before adding to input list
function fileExists(path) {
    try {
        return fs.existsSync(path);
    } catch (err) {
        return false;
    }
}

// Build list of input files that actually exist
const inputFiles = [
    'resources/js/app.js',
];

// SASS files to check
const sassFiles = [
    'resources/sass/app.scss',
    'resources/sass/custom.scss',
    'resources/assets/sass/custom.scss',
    'resources/assets/sass/pagination-fix.scss',
    'resources/assets/sass/custom-dark.scss',
    'resources/assets/sass/custom-auth.scss',
];

// Add existing SASS files to input
sassFiles.forEach(file => {
    if (fileExists(file)) {
        inputFiles.push(file);
    }
});

export default defineConfig({
    plugins: [
        laravel({
            input: inputFiles,
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
                        'autonumeric',
                        'quill',
                    ],
                    'front-third-party': [
                        'ion-rangeslider',
                        'select2',
                        'sweetalert',
                        'toastr',
                    ],
                    'tel-input': [
                        'intl-tel-input',
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