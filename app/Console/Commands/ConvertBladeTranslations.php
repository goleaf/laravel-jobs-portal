<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Enhanced Blade Translation Converter Command
 * Laravel Artisan command to convert hardcoded strings to translation functions.
 */
class ConvertBladeTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translate:convert-blades 
                            {--dry-run : Show what would be converted without making changes}
                            {--category= : Only process specific category (auth, forms, navigation, etc.)}
                            {--file= : Only process specific file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert hardcoded strings in Blade templates to translation functions using Enhanced patterns';

    private $processedFiles = 0;
    private $convertedStrings = 0;
    private $translationKeys = [];
    private $dryRun = false;

    // Common strings that should NOT be translated
    private $excludeStrings = [
        'id', 'name', 'email', 'password', 'class', 'style', 'href', 'src', 'alt',
        'data-', 'aria-', 'role', 'type', 'value', 'title',
        'GET', 'POST', 'PUT', 'DELETE', 'PATCH',
        'true', 'false', 'null', 'undefined',
        'btn', 'form', 'input', 'div', 'span', 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'px', 'py', 'mx', 'my', 'w-', 'h-', 'text-', 'bg-', 'border-', 'rounded-',
        '#', '.', '/', '\\', '?', '&', '=', '%', '@', '$',
        'localhost', '127.0.0.1', 'http', 'https', 'www',
    ];

    // Translation categories for organized keys
    private $categories = [
        'navigation' => ['menu', 'nav', 'link', 'home', 'about', 'contact', 'dashboard'],
        'auth' => ['login', 'register', 'logout', 'password', 'email', 'username', 'sign'],
        'forms' => ['submit', 'cancel', 'save', 'delete', 'edit', 'add', 'create', 'update'],
        'messages' => ['success', 'error', 'warning', 'info', 'alert', 'notification'],
        'common' => ['yes', 'no', 'ok', 'cancel', 'close', 'open', 'view', 'show', 'hide'],
        'jobs' => ['job', 'position', 'company', 'salary', 'apply', 'application'],
        'admin' => ['admin', 'manage', 'settings', 'users', 'permissions', 'roles'],
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->dryRun = $this->option('dry-run');

        $this->info('🌍 Enhanced Blade Translation Converter');
        $this->info('=====================================');

        if ($this->dryRun) {
            $this->warn('🔍 DRY RUN MODE - No files will be modified');
        }

        $bladeFiles = $this->findBladeFiles();
        $this->info('Found '.count($bladeFiles)." blade files to process\n");

        $progressBar = $this->output->createProgressBar(count($bladeFiles));
        $progressBar->start();

        foreach ($bladeFiles as $file) {
            $this->processBladeFile($file);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        if (! $this->dryRun) {
            $this->generateTranslationFiles();
        }

        $this->generateReport();

        return Command::SUCCESS;
    }

    private function findBladeFiles()
    {
        $viewsPath = resource_path('views');

        if ($this->option('file')) {
            $specificFile = $viewsPath.'/'.$this->option('file');

            return file_exists($specificFile) ? [$specificFile] : [];
        }

        $finder = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($viewsPath, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        $bladeFiles = [];
        foreach ($finder as $file) {
            if ($file->getExtension() === 'php' && strpos($file->getFilename(), '.blade.') !== false) {
                $bladeFiles[] = $file->getPathname();
            }
        }

        return $bladeFiles;
    }

    private function processBladeFile($filePath)
    {
        $content = file_get_contents($filePath);
        $originalContent = $content;

        // Convert various string patterns
        $content = $this->convertQuotedStrings($content, $filePath);
        $content = $this->convertHtmlText($content, $filePath);
        $content = $this->convertPlaceholders($content, $filePath);

        // Only write if changes were made and not in dry run mode
        if ($content !== $originalContent) {
            if (! $this->dryRun) {
                file_put_contents($filePath, $content);
            }
            $this->processedFiles++;
        }
    }

    private function convertQuotedStrings($content, $filePath)
    {
        // Pattern to match simple quoted strings that should be translated
        $pattern = '/([\'"])([A-Z][a-zA-Z\s]{3,50})\1/';

        return preg_replace_callback($pattern, function ($matches) use ($filePath) {
            $text = $matches[2];

            if ($this->shouldTranslate($text)) {
                $key = $this->generateTranslationKey($text, $filePath);
                $this->addTranslationKey($key, $text);
                $this->convertedStrings++;

                return "{{ __('".$key."') }}";
            }

            return $matches[0];
        }, $content);
    }

    private function convertHtmlText($content, $filePath)
    {
        // Convert text content within HTML tags
        $pattern = '/>([A-Z][a-zA-Z\s,.\-!?]{3,100})</';

        return preg_replace_callback($pattern, function ($matches) use ($filePath) {
            $text = trim($matches[1]);

            if ($this->shouldTranslate($text) && ! $this->containsBladeCode($text)) {
                $key = $this->generateTranslationKey($text, $filePath);
                $this->addTranslationKey($key, $text);
                $this->convertedStrings++;

                return ">{{ __('".$key."') }}<";
            }

            return $matches[0];
        }, $content);
    }

    private function convertPlaceholders($content, $filePath)
    {
        // Convert placeholder attributes
        $pattern = '/placeholder=[\'"]([A-Z][a-zA-Z\s]{3,50})[\'"]*/';

        return preg_replace_callback($pattern, function ($matches) use ($filePath) {
            $text = $matches[1];

            if ($this->shouldTranslate($text)) {
                $key = $this->generateTranslationKey($text, $filePath, 'placeholder');
                $this->addTranslationKey($key, $text);
                $this->convertedStrings++;

                return "placeholder=\"{{ __('".$key."') }}\"";
            }

            return $matches[0];
        }, $content);
    }

    private function shouldTranslate($text)
    {
        $text = trim($text);

        // Skip if too short or too long
        if (strlen($text) < 3 || strlen($text) > 100) {
            return false;
        }

        // Skip if contains excluded strings
        foreach ($this->excludeStrings as $exclude) {
            if (stripos($text, $exclude) !== false) {
                return false;
            }
        }

        // Skip if it's already a translation
        if (strpos($text, '__(') !== false || strpos($text, 'trans(') !== false) {
            return false;
        }

        // Skip if it's a variable or blade expression
        if (strpos($text, '$') !== false || strpos($text, '{{') !== false || strpos($text, '{!!') !== false) {
            return false;
        }

        // Must start with letter
        if (! preg_match('/^[A-Z]/', $text)) {
            return false;
        }

        return true;
    }

    private function containsBladeCode($text)
    {
        return strpos($text, '{{') !== false
               || strpos($text, '{!!') !== false
               || strpos($text, '@') !== false
               || strpos($text, '$') !== false;
    }

    private function generateTranslationKey($text, $filePath, $prefix = '')
    {
        // Determine category
        $category = $this->categorizeText($text, $filePath);

        // Generate clean key
        $key = strtolower($text);
        $key = preg_replace('/[^\w\s]/', '', $key); // Remove special characters
        $key = preg_replace('/\s+/', '_', $key); // Replace spaces with underscores
        $key = trim($key, '_');

        // Add prefix if provided
        if ($prefix) {
            $key = $prefix.'_'.$key;
        }

        // Add category
        return $category.'.'.$key;
    }

    private function categorizeText($text, $filePath)
    {
        $text = strtolower($text);
        $path = strtolower($filePath);

        // Check file path for context
        foreach ($this->categories as $category => $keywords) {
            if (strpos($path, $category) !== false) {
                return $category;
            }
        }

        // Check text content for category
        foreach ($this->categories as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($text, $keyword) !== false) {
                    return $category;
                }
            }
        }

        return 'common';
    }

    private function addTranslationKey($key, $text)
    {
        if (! isset($this->translationKeys[$key])) {
            $this->translationKeys[$key] = $text;
        }
    }

    private function generateTranslationFiles()
    {
        $this->info('📝 Generating translation files...');

        // Group keys by category
        $groupedKeys = [];
        foreach ($this->translationKeys as $key => $text) {
            $parts = explode('.', $key);
            $category = $parts[0];
            $actualKey = implode('.', array_slice($parts, 1));

            $groupedKeys[$category][$actualKey] = $text;
        }

        // Generate files for each category
        foreach ($groupedKeys as $category => $keys) {
            $this->generateCategoryFile($category, $keys);
        }
    }

    private function generateCategoryFile($category, $keys)
    {
        $filePath = base_path("lang/en_json/{$category}.json");

        // Ensure directory exists
        $directory = dirname($filePath);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Load existing translations if file exists
        $existingTranslations = [];
        if (file_exists($filePath)) {
            $existingTranslations = json_decode(file_get_contents($filePath), true) ?: [];
        }

        // Merge with new keys
        $allTranslations = array_merge($existingTranslations, $keys);
        ksort($allTranslations);

        // Write JSON file
        $json = json_encode($allTranslations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($filePath, $json);

        $this->line("  ✅ Updated {$category}.json with ".count($keys).' new translations');
    }

    private function generateReport()
    {
        $this->newLine();
        $this->info('🎉 ENHANCED BLADE TRANSLATION CONVERSION COMPLETED');
        $this->info(str_repeat('=', 60));

        $this->table(['Metric', 'Count'], [
            ['Files Processed', $this->processedFiles],
            ['Strings Converted', $this->convertedStrings],
            ['Translation Keys Created', count($this->translationKeys)],
        ]);

        if (! empty($this->translationKeys)) {
            $this->info('📂 Translation Categories:');
            $categoryStats = [];
            foreach ($this->translationKeys as $key => $text) {
                $category = explode('.', $key)[0];
                $categoryStats[$category] = ($categoryStats[$category] ?? 0) + 1;
            }

            foreach ($categoryStats as $category => $count) {
                $this->line("  • {$category}: {$count} keys");
            }
        }

        $this->newLine();
        $this->info('✅ All blade templates have been processed for translation!');

        if (! $this->dryRun) {
            $this->info('🔄 Next steps:');
            $this->line('  1. Review generated translation files in lang/en_json/');
            $this->line('  2. Run AI translator to generate other language files');
            $this->line('  3. Test language switching functionality');
            $this->info('🌍 Ready for multilingual deployment!');
        }
    }
}
