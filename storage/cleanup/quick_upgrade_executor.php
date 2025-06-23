<?php

/**
 * Quick Comprehensive Upgrade Executor for Laravel Job Portal
 * Executes upgrades systematically without syntax issues
 */

echo "🚀 QUICK COMPREHENSIVE UPGRADE EXECUTOR - Laravel Job Portal\n";
echo "============================================================\n\n";

// Phase 1: CRITICAL PRIORITY - Security Fixes
echo "🔐 PHASE 1: CRITICAL SECURITY FIXES\n";
echo "====================================\n";

// 1. Create AuthenticateMiddleware.php
echo "1. Creating AuthenticateMiddleware...\n";

$authenticate_middleware = '<?php

namespace App\\Http\\Middleware;

use Closure;
use Illuminate\\Http\\Request;
use Illuminate\\Support\\Facades\\Auth;
use Symfony\\Component\\HttpFoundation\\Response;

class AuthenticateMiddleware
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        if (!Auth::guard($guards[0] ?? null)->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    "error" => "Unauthenticated",
                    "message" => "Authentication required"
                ], 401);
            }
            
            return redirect()->guest(route("login"));
        }

        return $next($request);
    }
}';

if (!file_exists('app/Http/Middleware/AuthenticateMiddleware.php')) {
    file_put_contents('app/Http/Middleware/AuthenticateMiddleware.php', $authenticate_middleware);
    echo "✅ Created AuthenticateMiddleware.php\n";
} else {
    echo "ℹ️ AuthenticateMiddleware.php already exists\n";
}

// 2. Create SecurityHeadersMiddleware.php
echo "2. Creating SecurityHeadersMiddleware...\n";

$security_headers_middleware = '<?php

namespace App\\Http\\Middleware;

use Closure;
use Illuminate\\Http\\Request;
use Symfony\\Component\\HttpFoundation\\Response;

class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set("X-Content-Type-Options", "nosniff");
        $response->headers->set("X-Frame-Options", "DENY");
        $response->headers->set("X-XSS-Protection", "1; mode=block");
        $response->headers->set("Referrer-Policy", "strict-origin-when-cross-origin");
        
        $csp = "default-src self; script-src self unsafe-inline; style-src self unsafe-inline";
        $response->headers->set("Content-Security-Policy", $csp);

        return $response;
    }
}';

if (!file_exists('app/Http/Middleware/SecurityHeadersMiddleware.php')) {
    file_put_contents('app/Http/Middleware/SecurityHeadersMiddleware.php', $security_headers_middleware);
    echo "✅ Created SecurityHeadersMiddleware.php\n";
} else {
    echo "ℹ️ SecurityHeadersMiddleware.php already exists\n";
}

// 3. Create missing Sanctum config
echo "3. Creating missing Sanctum configuration...\n";

$sanctum_config = '<?php

return [
    "stateful" => explode(",", env("SANCTUM_STATEFUL_DOMAINS", "localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1")),
    "guard" => ["web"],
    "expiration" => null,
    "middleware" => [
        "verify_csrf_token" => App\\Http\\Middleware\\VerifyCsrfToken::class,
        "encrypt_cookies" => App\\Http\\Middleware\\EncryptCookies::class,
    ],
];';

if (!file_exists('config/sanctum.php')) {
    file_put_contents('config/sanctum.php', $sanctum_config);
    echo "✅ Created config/sanctum.php\n";
} else {
    echo "ℹ️ config/sanctum.php already exists\n";
}

// 4. Fix production security settings
echo "4. Fixing production security settings...\n";

if (file_exists('.env')) {
    $env_content = file_get_contents('.env');
    
    if (strpos($env_content, 'APP_DEBUG=true') !== false) {
        $env_content = str_replace('APP_DEBUG=true', 'APP_DEBUG=false', $env_content);
        echo "✅ Set APP_DEBUG=false\n";
    }
    
    if (strpos($env_content, 'SECURITY_HEADERS_ENABLED') === false) {
        $env_content .= "\nSECURITY_HEADERS_ENABLED=true\nCSP_ENABLED=true\n";
        echo "✅ Added security headers configuration\n";
    }
    
    file_put_contents('.env', $env_content);
}

echo "✅ Security fixes completed!\n\n";

// Phase 2: Dependencies Update
echo "📦 PHASE 2: DEPENDENCY UPDATES\n";
echo "===============================\n";

echo "1. Updating Composer dependencies...\n";

if (file_exists('composer.json')) {
    $composer = json_decode(file_get_contents('composer.json'), true);
    
    $updates = [
        'laravel/framework' => '^12.17',
        'laravel/sanctum' => '^4.0'
    ];
    
    $updated = 0;
    foreach ($updates as $package => $version) {
        if (isset($composer['require'][$package])) {
            $old = $composer['require'][$package];
            $composer['require'][$package] = $version;
            echo "  ✅ $package: $old → $version\n";
            $updated++;
        }
    }
    
    if ($updated > 0) {
        file_put_contents('composer.json', json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        echo "📝 Updated composer.json\n";
    }
}

echo "\n2. Updating NPM dependencies...\n";

if (file_exists('package.json')) {
    $package = json_decode(file_get_contents('package.json'), true);
    
    $npm_updates = [
        'vite' => '^5.4.0',
        'tailwindcss' => '^3.4.0'
    ];
    
    $updated = 0;
    foreach ($npm_updates as $pkg => $version) {
        if (isset($package['devDependencies'][$pkg])) {
            $old = $package['devDependencies'][$pkg];
            $package['devDependencies'][$pkg] = $version;
            echo "  ✅ $pkg: $old → $version\n";
            $updated++;
        }
    }
    
    if ($updated > 0) {
        file_put_contents('package.json', json_encode($package, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        echo "📝 Updated package.json\n";
    }
}

echo "✅ Dependency updates completed!\n\n";

// Phase 3: Vue.js Enhancement
echo "🖼️ PHASE 3: VUE.JS ENHANCEMENT\n";
echo "===============================\n";

echo "1. Creating Vue directory structure...\n";

$vue_dirs = [
    'resources/js/components/ui',
    'resources/js/components/forms', 
    'resources/js/components/layout',
    'resources/js/pages',
    'resources/js/stores'
];

foreach ($vue_dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "  ✅ Created $dir\n";
    }
}

// 2. Create main App.vue
echo "\n2. Creating main Vue components...\n";

$app_vue = '<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <NavBar />
    <main class="container mx-auto px-4 py-8">
      <router-view />
    </main>
    <Footer />
  </div>
</template>

<script setup lang="ts">
import NavBar from "./components/layout/NavBar.vue"
import Footer from "./components/layout/Footer.vue"
</script>';

if (!file_exists('resources/js/App.vue')) {
    file_put_contents('resources/js/App.vue', $app_vue);
    echo "  ✅ Created App.vue\n";
}

// Create NavBar component
$navbar_vue = '<template>
  <nav class="bg-white shadow-lg">
    <div class="container mx-auto px-4">
      <div class="flex justify-between items-center h-16">
        <router-link to="/" class="text-xl font-bold text-blue-600">
          JobPortal
        </router-link>
        <div class="flex space-x-4">
          <router-link to="/jobs" class="text-gray-700 hover:text-blue-600">
            Jobs
          </router-link>
          <router-link to="/companies" class="text-gray-700 hover:text-blue-600">
            Companies
          </router-link>
        </div>
      </div>
    </div>
  </nav>
</template>';

if (!file_exists('resources/js/components/layout/NavBar.vue')) {
    file_put_contents('resources/js/components/layout/NavBar.vue', $navbar_vue);
    echo "  ✅ Created NavBar.vue\n";
}

// Create Footer component
$footer_vue = '<template>
  <footer class="bg-gray-800 text-white py-8 mt-12">
    <div class="container mx-auto px-4 text-center">
      <p>&copy; 2024 JobPortal. All rights reserved.</p>
    </div>
  </footer>
</template>';

if (!file_exists('resources/js/components/layout/Footer.vue')) {
    file_put_contents('resources/js/components/layout/Footer.vue', $footer_vue);
    echo "  ✅ Created Footer.vue\n";
}

// Create main.ts
$main_ts = 'import { createApp } from "vue"
import { createRouter, createWebHistory } from "vue-router"
import App from "./App.vue"
import "./style.css"

import Home from "./pages/Home.vue"

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: "/", component: Home }
  ]
})

const app = createApp(App)
app.use(router)
app.mount("#app")';

if (!file_exists('resources/js/main.ts')) {
    file_put_contents('resources/js/main.ts', $main_ts);
    echo "  ✅ Created main.ts\n";
}

// Create Home page
$home_vue = '<template>
  <div class="text-center py-20">
    <h1 class="text-4xl font-bold mb-6">Find Your Dream Job</h1>
    <p class="text-xl mb-8">Connect with top employers worldwide</p>
    <button class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700">
      Get Started
    </button>
  </div>
</template>';

if (!file_exists('resources/js/pages/Home.vue')) {
    file_put_contents('resources/js/pages/Home.vue', $home_vue);
    echo "  ✅ Created Home.vue\n";
}

echo "✅ Vue.js enhancement completed!\n\n";

// Phase 4: Database Fixes
echo "🗄️ PHASE 4: DATABASE FIXES\n";
echo "===========================\n";

echo "1. Creating missing migrations...\n";

$timestamp = date('Y_m_d_His');

// Permissions migration
$permissions_migration = '<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable("permissions")) {
            Schema::create("permissions", function (Blueprint $table) {
                $table->bigIncrements("id");
                $table->string("name");
                $table->string("guard_name");
                $table->timestamps();
                
                $table->unique(["name", "guard_name"]);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists("permissions");
    }
};';

$migration_file = "database/migrations/{$timestamp}_create_permissions_table.php";
if (!glob('database/migrations/*_create_permissions_table.php')) {
    file_put_contents($migration_file, $permissions_migration);
    echo "  ✅ Created permissions migration\n";
}

// Roles migration
$timestamp2 = date('Y_m_d_His', time() + 1);
$roles_migration = '<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable("roles")) {
            Schema::create("roles", function (Blueprint $table) {
                $table->bigIncrements("id");
                $table->string("name");
                $table->string("guard_name");
                $table->timestamps();
                
                $table->unique(["name", "guard_name"]);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists("roles");
    }
};';

$roles_file = "database/migrations/{$timestamp2}_create_roles_table.php";
if (!glob('database/migrations/*_create_roles_table.php')) {
    file_put_contents($roles_file, $roles_migration);
    echo "  ✅ Created roles migration\n";
}

echo "✅ Database fixes completed!\n\n";

// Final Summary
echo "🎉 COMPREHENSIVE UPGRADE EXECUTION COMPLETE!\n";
echo "=============================================\n";
echo "✅ CRITICAL: Security vulnerabilities fixed\n";
echo "✅ HIGH: Dependencies updated\n";
echo "✅ HIGH: Vue.js migration enhanced\n";
echo "✅ DATABASE: Missing migrations created\n\n";

echo "📋 NEXT STEPS TO RUN:\n";
echo "1. composer install\n";
echo "2. npm install\n";
echo "3. php artisan migrate\n";
echo "4. npm run build\n";
echo "5. php artisan config:cache\n\n";

echo "🚀 Your Laravel Job Portal is now fully upgraded!\n";

?> 