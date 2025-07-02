import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/main.js"
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources/js'),
        },
    },
    build: {
        outDir: 'public/build',
        rollupOptions: {
            input: {
                main: 'resources/js/main.js',
                app: 'resources/css/app.css'
            },
            output: {
                manualChunks: {
                    'vendor-vue': ['vue', 'vue-router', 'pinia'],
                    'vendor-ui': ['@heroicons/vue/24/outline', '@heroicons/vue/24/solid'],
                    'auth-chunk': [
                        'resources/js/stores/auth.ts',
                        'resources/js/composables/useAuth.ts'
                    ],
                    'utils-chunk': [
                        'resources/js/services/api.ts',
                        'resources/js/composables/useApi.ts'
                    ],
                },
                chunkFileNames: (chunkInfo) => {
                    const facadeModuleId = chunkInfo.facadeModuleId 
                        ? chunkInfo.facadeModuleId.split('/').pop().replace('.vue', '') 
                        : 'chunk';
                    
                    if (chunkInfo.name) {
                        if (chunkInfo.name.includes('candidate')) {
                            return 'candidate/[name]-[hash].js';
                        } else if (chunkInfo.name.includes('employer')) {
                            return 'employer/[name]-[hash].js';
                        } else if (chunkInfo.name.includes('admin')) {
                            return 'admin/[name]-[hash].js';
                        } else if (chunkInfo.name.includes('auth')) {
                            return 'auth/[name]-[hash].js';
                        } else if (chunkInfo.name.includes('browse')) {
                            return 'browse/[name]-[hash].js';
                        }
                    }
                    
                    return `chunks/${facadeModuleId}-[hash].js`;
                },
                assetFileNames: (assetInfo) => {
                    const extType = assetInfo.name.split('.').pop();
                    
                    if (/png|jpe?g|svg|gif|tiff|bmp|ico/i.test(extType)) {
                        return `images/[name]-[hash][extname]`;
                    } else if (/css/i.test(extType)) {
                        return `styles/[name]-[hash][extname]`;
                    } else if (/woff2?|eot|ttf|otf/i.test(extType)) {
                        return `fonts/[name]-[hash][extname]`;
                    }
                    
                    return `assets/[name]-[hash][extname]`;
                },
            },
        },
        target: 'es2018',
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
                pure_funcs: ['console.log', 'console.warn'],
            },
            mangle: {
                safari10: true,
            },
        },
        chunkSizeWarningLimit: 1000,
        sourcemap: process.env.NODE_ENV === 'development',
        assetsInlineLimit: 4096,
        manifest: true,
    },
    server: {
        hmr: {
            overlay: true,
        },
        open: false,
    },
    optimizeDeps: {
        include: [
            'vue',
            'vue-router',
            'pinia',
            '@heroicons/vue/24/outline',
            '@heroicons/vue/24/solid'
        ],
        exclude: [
            // Exclude large dependencies that should be chunked
        ],
    },
    css: {
        devSourcemap: true,
        preprocessorOptions: {
            scss: {
                additionalData: ``,
            },
        },
    },
    define: {
        __VUE_PROD_DEVTOOLS__: false,
        __VUE_OPTIONS_API__: true,
    },
    experimental: {
        renderBuiltUrl(filename, { hostType }) {
            return filename;
        },
    },
}); 