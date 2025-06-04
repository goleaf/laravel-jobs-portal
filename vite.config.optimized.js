import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/admin.css',
                'resources/js/admin.js',
                'resources/css/candidate.css',
                'resources/js/candidate.js',
                'resources/css/company.css',
                'resources/js/company.js'
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['vue', 'axios'],
                    admin: ['admin.js'],
                    candidate: ['candidate.js'],
                    company: ['company.js']
                }
            }
        },
        cssCodeSplit: true,
        sourcemap: false,
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true
            }
        }
    },
    optimizeDeps: {
        include: ['vue', 'axios', 'lodash']
    },
    server: {
        hmr: {
            host: 'localhost',
        },
    }
});