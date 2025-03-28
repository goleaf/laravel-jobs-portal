<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CleanupTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:cleanup {--locale=all} {--dry-run} {--scan=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove unused translation keys';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $localeOption = $this->option('locale');
        $scanPath = $this->option('scan') ?: resource_path('views');
        
        if ($isDryRun) {
            $this->info('Running in dry-run mode. No changes will be made.');
        }
        
        // Determine locales to process
        if ($localeOption === 'all') {
            $locales = array_keys(config('app.available_locales', []));
        } else {
            $locales = [$localeOption];
        }
        
        // Scan for used translation keys
        $this->info('Scanning for used translation keys in: ' . $scanPath);
        $usedKeys = $this->scanForUsedKeys($scanPath);
        $this->info('Found ' . count($usedKeys) . ' unique translation keys used in the application.');
        
        // Process each locale
        foreach ($locales as $locale) {
            $this->info("Processing locale: {$locale}");
            
            $localePath = resource_path("lang/{$locale}");
            if (!File::exists($localePath)) {
                $this->error("Locale directory not found: {$localePath}");
                continue;
            }
            
            $files = File::files($localePath);
            
            foreach ($files as $file) {
                $filename = $file->getFilename();
                $fileBaseName = $file->getBasename('.php');
                
                if ($file->getExtension() !== 'php') {
                    continue;
                }
                
                $this->info("Processing file: {$filename}");
                
                $translations = require $file->getPathname();
                $flatTranslations = Arr::dot($translations);
                
                $unusedKeys = [];
                $keysRemoved = 0;
                
                // Find unused keys
                foreach ($flatTranslations as $key => $value) {
                    $fullKey = "{$fileBaseName}.{$key}";
                    
                    if (!in_array($fullKey, $usedKeys)) {
                        $unusedKeys[$key] = $value;
                        $keysRemoved++;
                    }
                }
                
                // Report unused keys
                if (count($unusedKeys) > 0) {
                    $this->warn("Found {$keysRemoved} unused keys in {$filename}");
                    
                    if ($this->option('verbose')) {
                        $headers = ['Key', 'Value'];
                        $rows = [];
                        
                        foreach ($unusedKeys as $key => $value) {
                            $rows[] = [$key, is_array($value) ? 'Array' : Str::limit($value, 50)];
                        }
                        
                        $this->table($headers, $rows);
                    }
                    
                    // Remove unused keys if not in dry-run mode
                    if (!$isDryRun) {
                        $updatedTranslations = $translations;
                        
                        foreach ($unusedKeys as $key => $value) {
                            Arr::forget($updatedTranslations, $key);
                        }
                        
                        // Write back to file
                        $content = "<?php\n\nreturn " . var_export($updatedTranslations, true) . ";\n";
                        File::put($file->getPathname(), $content);
                        
                        $this->info("Removed {$keysRemoved} unused keys from {$filename}");
                    }
                } else {
                    $this->info("No unused keys found in {$filename}");
                }
            }
        }
        
        $this->info('Translation cleanup completed!');
        
        return Command::SUCCESS;
    }
    
    /**
     * Scan for used translation keys in the application
     *
     * @param string $path
     * @return array
     */
    private function scanForUsedKeys(string $path): array
    {
        $usedKeys = [];
        
        // Add hard-coded keys that might not be detected by the scan
        $hardCodedKeys = [
            'pagination.previous',
            'pagination.next',
            'validation.accepted',
            'validation.required',
            'auth.failed',
            'auth.password',
        ];
        
        $usedKeys = array_merge($usedKeys, $hardCodedKeys);
        
        // Recursively find all PHP and Blade files
        $files = File::allFiles($path);
        
        // Add additional paths to scan
        $additionalPaths = [
            app_path(),
            base_path('routes'),
        ];
        
        foreach ($additionalPaths as $additionalPath) {
            if (File::exists($additionalPath)) {
                $files = array_merge($files, File::allFiles($additionalPath));
            }
        }
        
        // Patterns to find translation calls
        $patterns = [
            // __('key') or trans('key')
            "/(?:__|trans|@lang)\(\s*['\"]([^'\"]+)['\"]\s*(?:,|\))/",
            
            // trans_choice('key', $count)
            "/trans_choice\(\s*['\"]([^'\"]+)['\"]\s*,/",
            
            // @lang('key')
            "/@lang\(\s*['\"]([^'\"]+)['\"]\s*(?:,|\))/",
            
            // {{ __('key') }}
            "/\{\{\s*(?:__|trans|@lang)\(\s*['\"]([^'\"]+)['\"]\s*(?:,|\))/",
            
            // Lang::get('key')
            "/Lang::get\(\s*['\"]([^'\"]+)['\"]\s*(?:,|\))/",
            
            // trans()->get('key')
            "/trans\(\)->get\(\s*['\"]([^'\"]+)['\"]\s*(?:,|\))/",
        ];
        
        foreach ($files as $file) {
            // Skip files in vendor directory
            if (Str::contains($file->getPathname(), 'vendor/')) {
                continue;
            }
            
            // Only process PHP and Blade files
            if (!in_array($file->getExtension(), ['php', 'blade.php'])) {
                continue;
            }
            
            $content = File::get($file->getPathname());
            
            foreach ($patterns as $pattern) {
                if (preg_match_all($pattern, $content, $matches)) {
                    $usedKeys = array_merge($usedKeys, $matches[1]);
                }
            }
        }
        
        // Remove duplicates and sort
        $usedKeys = array_unique($usedKeys);
        sort($usedKeys);
        
        return $usedKeys;
    }
} 