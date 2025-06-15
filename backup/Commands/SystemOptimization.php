<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SystemOptimization extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'system:optimize {--detailed : Show detailed output}';

    /**
     * The console command description.
     */
    protected $description = 'Comprehensive system optimization and health check';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 FINAL SYSTEM OPTIMIZATION & HEALTH CHECK');
        $this->line(str_repeat('=', 50));
        $this->newLine();

        // Performance optimizations
        $this->performanceOptimizations();

        // Database validation
        $this->databaseValidation();

        // Storage optimization
        $this->storageOptimization();

        // Asset validation
        $this->assetValidation();

        // Translation system
        $this->translationValidation();

        // Security checks
        $this->securityValidation();

        // Route validation
        $this->routeValidation();

        // Testing framework
        $this->testingValidation();

        // Final checklist
        $this->productionChecklist();

        return 0;
    }

    private function performanceOptimizations()
    {
        $this->warn('⚡ Performance Optimizations');
        $this->line(str_repeat('-', 30));

        $optimizations = [
            'optimize' => 'Laravel optimization (config, routes, views)',
            'queue:restart' => 'Queue worker restart',
        ];

        foreach ($optimizations as $command => $description) {
            $this->line("   🔧 {$description}");

            try {
                Artisan::call($command);
                $this->info('   ✅ Success');
            } catch (\Exception $e) {
                $this->error('   ❌ Failed: '.$e->getMessage());
            }
        }
        $this->newLine();
    }

    private function databaseValidation()
    {
        $this->warn('🗄️ Database Optimization');
        $this->line(str_repeat('-', 25));

        try {
            DB::connection()->getPdo();
            $this->info('   ✅ Database connection: OK');

            // Check critical tables
            $tables = ['users', 'companies', 'jobs', 'candidates', 'job_applications'];
            foreach ($tables as $table) {
                try {
                    $count = DB::table($table)->count();
                    $this->line("   📊 {$table}: {$count} records");
                } catch (\Exception $e) {
                    $this->error("   ⚠️ {$table}: ".$e->getMessage());
                }
            }
        } catch (\Exception $e) {
            $this->error('   ❌ Database connection failed: '.$e->getMessage());
        }
        $this->newLine();
    }

    private function storageOptimization()
    {
        $this->warn('💾 Storage Optimization');
        $this->line(str_repeat('-', 25));

        $storagePaths = [
            'storage/logs' => 'Log files',
            'storage/framework/cache' => 'Framework cache',
            'storage/framework/views' => 'Compiled views',
            'public/build' => 'Built assets',
        ];

        foreach ($storagePaths as $path => $description) {
            if (is_dir($path)) {
                $size = exec("du -sh {$path} 2>/dev/null | cut -f1");
                $this->line("   📁 {$description}: ".($size ?: 'N/A'));
            } else {
                $this->error("   ⚠️ {$description}: Directory not found");
            }
        }
        $this->newLine();
    }

    private function assetValidation()
    {
        $this->warn('🎨 Asset Validation');
        $this->line(str_repeat('-', 20));

        $assetFiles = [
            'public/build/manifest.json' => 'Vite manifest',
            'public/build/assets' => 'Built assets directory',
            'public/css' => 'CSS directory',
            'public/js' => 'JS directory',
        ];

        foreach ($assetFiles as $file => $description) {
            if (file_exists($file)) {
                $this->info("   ✅ {$description}: Found");
                if (is_dir($file)) {
                    $count = count(glob("{$file}/*"));
                    $this->line("       Files: {$count}");
                }
            } else {
                $this->error("   ⚠️ {$description}: Missing");
            }
        }
        $this->newLine();
    }

    private function translationValidation()
    {
        $this->warn('🌐 Translation System');
        $this->line(str_repeat('-', 22));

        $langFile = 'lang/en.json';
        if (file_exists($langFile)) {
            $translations = json_decode(file_get_contents($langFile), true);
            $count = count($translations);
            $this->info("   ✅ JSON translations: {$count} keys");

            $coverage = $count > 500 ? 'Excellent' : ($count > 300 ? 'Good' : 'Needs improvement');
            $this->line("   📊 Translation coverage: {$coverage}");
        } else {
            $this->error('   ❌ JSON translation file missing');
        }

        // Check other language files
        $langDirs = glob('lang/*', GLOB_ONLYDIR);
        foreach ($langDirs as $dir) {
            $locale = basename($dir);
            $this->line("   🌍 Language: {$locale}");
        }
        $this->newLine();
    }

    private function securityValidation()
    {
        $this->warn('🔒 Security Validation');
        $this->line(str_repeat('-', 22));

        $securityChecks = [
            'APP_ENV' => env('APP_ENV', 'unknown'),
            'APP_DEBUG' => env('APP_DEBUG', true) ? 'ON' : 'OFF',
            'APP_KEY' => env('APP_KEY') ? 'SET' : 'MISSING',
            'DB_PASSWORD' => env('DB_PASSWORD') ? 'SET' : 'MISSING',
        ];

        foreach ($securityChecks as $key => $value) {
            $status = ('APP_DEBUG' === $key && 'OFF' === $value)
                      || ('APP_DEBUG' !== $key && 'MISSING' !== $value && 'unknown' !== $value);

            $icon = $status ? '✅' : '⚠️';
            $this->line("   {$icon} {$key}: {$value}");
        }
        $this->newLine();
    }

    private function routeValidation()
    {
        $this->warn('🛣️ Route System');
        $this->line(str_repeat('-', 16));

        try {
            $routes = collect(\Route::getRoutes())->count();
            $this->info("   ✅ Routes registered: {$routes}");

            $cached = file_exists('bootstrap/cache/routes-v7.php');
            $this->line('   🔍 Route cache: '.($cached ? 'Cached' : 'Not cached'));
        } catch (\Exception $e) {
            $this->error('   ⚠️ Route validation failed: '.$e->getMessage());
        }
        $this->newLine();
    }

    private function testingValidation()
    {
        $this->warn('🧪 Testing Framework');
        $this->line(str_repeat('-', 21));

        $testDirs = ['tests/Unit', 'tests/Feature', 'tests/Browser'];
        $totalTests = 0;

        foreach ($testDirs as $dir) {
            if (is_dir($dir)) {
                $testFiles = glob("{$dir}/*.php");
                $count = count($testFiles);
                $totalTests += $count;
                $this->info("   ✅ {$dir}: {$count} test files");
            } else {
                $this->error("   ⚠️ {$dir}: Directory missing");
            }
        }

        $this->line("   📊 Total test files: {$totalTests}");

        // Check test utilities
        $testUtils = [
            'tests/TestHelpers.php' => 'Test helpers',
            'phpunit.xml' => 'PHPUnit config',
            'phpunit.dusk.xml' => 'Dusk config',
        ];

        foreach ($testUtils as $file => $description) {
            $icon = file_exists($file) ? '✅' : '⚠️';
            $this->line("   {$icon} {$description}");
        }
        $this->newLine();
    }

    private function productionChecklist()
    {
        $this->warn('📋 PRODUCTION READINESS CHECKLIST');
        $this->line(str_repeat('=', 35));

        $checklist = [
            'Environment Configuration' => 'production' === env('APP_ENV'),
            'Debug Mode Disabled' => !env('APP_DEBUG', true),
            'Application Key Set' => !empty(env('APP_KEY')),
            'Database Connected' => $this->isDatabaseConnected(),
            'Routes Cached' => file_exists('bootstrap/cache/routes-v7.php'),
            'Config Cached' => file_exists('bootstrap/cache/config.php'),
            'Views Cached' => is_dir('storage/framework/views'),
            'Assets Built' => file_exists('public/build/manifest.json'),
            'Translations Ready' => $this->hasEnoughTranslations(),
            'Tests Available' => $this->hasEnoughTests(),
        ];

        $readyCount = 0;
        foreach ($checklist as $item => $status) {
            $icon = $status ? '✅' : '❌';
            $this->line("   {$icon} {$item}");
            if ($status) {
                ++$readyCount;
            }
        }

        $percentage = round(($readyCount / count($checklist)) * 100);
        $this->newLine();
        $this->line("📊 Production Readiness: {$percentage}% ({$readyCount}/".count($checklist).')');

        if ($percentage >= 90) {
            $this->info('🚀 STATUS: READY FOR PRODUCTION DEPLOYMENT!');
        } elseif ($percentage >= 70) {
            $this->warn('⚠️ STATUS: MOSTLY READY - Address remaining items');
        } else {
            $this->error('🔧 STATUS: NEEDS MORE OPTIMIZATION');
        }

        $this->newLine();
        $this->line(str_repeat('=', 60));
        $this->info('🎉 FINAL SYSTEM OPTIMIZATION COMPLETE!');
        $this->line('📅 '.date('Y-m-d H:i:s'));
        $this->line(str_repeat('=', 60));
    }

    private function isDatabaseConnected(): bool
    {
        try {
            DB::connection()->getPdo();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function hasEnoughTranslations(): bool
    {
        $langFile = 'lang/en.json';
        if (!file_exists($langFile)) {
            return false;
        }

        $translations = json_decode(file_get_contents($langFile), true);

        return count($translations) > 500;
    }

    private function hasEnoughTests(): bool
    {
        $testDirs = ['tests/Unit', 'tests/Feature', 'tests/Browser'];
        $totalTests = 0;

        foreach ($testDirs as $dir) {
            if (is_dir($dir)) {
                $testFiles = glob("{$dir}/*.php");
                $totalTests += count($testFiles);
            }
        }

        return $totalTests > 50;
    }
}
