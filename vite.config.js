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
                "resources/js/app.ts"
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
                app: 'resources/js/app.ts',
                styles: 'resources/css/app.css'
            },
            output: {
                manualChunks: {
                    // Core Vue ecosystem - keep together for optimal loading
                    'vendor-vue': ['vue', 'vue-router', 'pinia'],
                    
                    // UI library chunks - split by usage patterns  
                    'vendor-icons': ['@heroicons/vue/24/outline', '@heroicons/vue/24/solid'],
                    
                    // Heavy utility libraries - separate for dynamic loading
                    'vendor-alerts': ['sweetalert2'],
                    
                    // Core application chunks
                    'auth-chunk': [
                        'resources/js/stores/auth.ts',
                        'resources/js/composables/useAuth.ts'
                    ],
                    'api-chunk': [
                        'resources/js/services/api.ts',
                        'resources/js/composables/useApi.ts'
                    ],
                    
                    // Base components - frequently used across roles
                    'components-base': [
                        'resources/js/components/base/BaseButton.vue',
                        'resources/js/components/base/BaseInput.vue'
                    ],
                    
                    // Role-specific component chunks
                    'components-ui': [
                        'resources/js/components/ui/ActionButtons.vue',
                        'resources/js/components/ui/Pagination.vue',
                        'resources/js/components/ui/Badge.vue'
                    ]
                },
                chunkFileNames: (chunkInfo) => {
                    const facadeModuleId = chunkInfo.facadeModuleId 
                        ? chunkInfo.facadeModuleId.split('/').pop().replace('.vue', '') 
                        : 'chunk';
                    
                    if (chunkInfo.name) {
                        // Route-based chunking for better caching
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
                        } else if (chunkInfo.name.includes('vendor')) {
                            return 'vendors/[name]-[hash].js';
                        } else if (chunkInfo.name.includes('components')) {
                            return 'components/[name]-[hash].js';
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
            // Enhanced tree shaking configuration
            treeshake: {
                moduleSideEffects: false,
                propertyReadSideEffects: false,
                tryCatchDeoptimization: false,
                // More aggressive tree shaking for better bundle size
                annotations: true,
                unknownGlobalSideEffects: false
            },
            // External dependencies for better chunking
            external: (id) => {
                // Don't externalize core dependencies, but prepare for CDN if needed
                return false;
            }
        },
        target: 'es2018',
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
                pure_funcs: ['console.log', 'console.warn', 'console.info'],
                // Enhanced compression for better tree shaking
                passes: 2,
                unsafe_comps: true,
                unsafe_math: true,
                unsafe_methods: true,
                keep_fargs: false,
                // Remove unused code more aggressively
                dead_code: true,
                unused: true,
                side_effects: false
            },
            mangle: {
                safari10: true,
                // Better mangling for smaller bundles
                toplevel: true,
                properties: {
                    regex: /^_/
                }
            },
            format: {
                comments: false
            }
        },
        chunkSizeWarningLimit: 800, // Reduced from 1000 for better chunk awareness
        sourcemap: process.env.NODE_ENV === 'development',
        // Enhanced asset optimization
        assetsInlineLimit: 1024, // Reduced for better caching - only very small assets inlined
        manifest: true,
        // Enable CSS code splitting
        cssCodeSplit: true,
        // Enable modern browser optimizations
        modulePreload: {
            polyfill: false // Assume modern browsers
        }
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
            'pinia'
        ],
        exclude: [
            // Exclude heavy dependencies for better chunking
            'sweetalert2'
        ],
        // Enhanced dependency optimization
        force: false,
        esbuildOptions: {
            target: 'es2018'
        }
    },
    css: {
        devSourcemap: true,
        preprocessorOptions: {
            scss: {
                additionalData: ``,
            },
        },
        // Enhanced CSS optimization
        postcss: {
            plugins: [
                // PostCSS plugins are now configured in postcss.config.js
            ]
        }
    },
    define: {
        __VUE_PROD_DEVTOOLS__: false,
        __VUE_OPTIONS_API__: true,
        // Define environment variables for tree shaking
        'process.env.NODE_ENV': JSON.stringify(process.env.NODE_ENV || 'development')
    },
    experimental: {
        renderBuiltUrl(filename, { hostType }) {
            return filename;
        },
    },
    // Enhanced build performance
    esbuild: {
        drop: process.env.NODE_ENV === 'production' ? ['console', 'debugger'] : [],
        legalComments: 'none'
    }
}); 