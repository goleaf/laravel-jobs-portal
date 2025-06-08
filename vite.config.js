import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/admin.js",
                "resources/js/frontend.js",
                "resources/css/universal/components.scss",
                "resources/css/universal/rtl-support.scss",
                "resources/js/universal/ui-system.js",
                "resources/js/universal/i18n-system.js"
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ["lodash", "axios"],
                    ui: ["@fortawesome/fontawesome-free"],
                    charts: ["chart.js", "apexcharts"],
                    datatables: ["datatables.net", "datatables.net-dt"],
                    universal: ["resources/js/universal/ui-system.js", "resources/js/universal/i18n-system.js"]
                }
            }
        },
        chunkSizeWarningLimit: 1000,
        minify: "terser",
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true
            }
        }
    },
    optimizeDeps: {
        include: ["lodash", "axios"]
    },
    server: {
        hmr: {
            host: "localhost",
        },
    },
});
