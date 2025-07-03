<?php

namespace App\Console\Commands;

use App\Helpers\LanguageHelper;
use App\Services\TranslationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class TranslationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translation:manage 
                           {action : Action to perform (scan|missing|sync|stats|export|import)}
                           {--locale= : Target locale for operations}
                           {--source= : Source locale for sync operations (default: en)}
                           {--file= : File path for import/export operations}
                           {--format=json : Export format (json|php)}
                           {--merge : Merge with existing translations during import}
                           {--auto-translate : Automatically translate missing keys}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comprehensive translation management tool';

    /**
     * Available locales.
     */
    private array $availableLocales;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->availableLocales = array_keys(Config::get('app.available_locales', []));
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'scan':
                return $this->scanForHardcodedStrings();

            case 'missing':
                return $this->showMissingTranslations();

            case 'sync':
                return $this->syncTranslations();

            case 'stats':
                return $this->showStatistics();

            case 'export':
                return $this->exportTranslations();

            case 'import':
                return $this->importTranslations();

            default:
                $this->error("Unknown action: {$action}");
                $this->showHelp();

                return 1;
        }
    }

    /**
     * Scan for hardcoded strings in the application.
     */
    private function scanForHardcodedStrings(): int
    {
        $this->info('🔍 Scanning for hardcoded strings...');

        $hardcodedStrings = [];
        $scanPaths = [
            app_path(),
            resource_path('views'),
            resource_path('js'),
            base_path('routes'),
        ];

        $patterns = [
            // Common hardcoded strings patterns
            '/["\']([A-Z][a-z\s]{3,50})["\']/',
            // Button text
            '/(?:title|placeholder|label|value)=["\']([A-Za-z\s]{3,30})["\']/',
            // Alert messages
            '/(?:alert|message|error|success)\(["\']([A-Za-z\s]{3,50})["\']/',
        ];

        foreach ($scanPaths as $path) {
            if (! File::exists($path)) {
                continue;
            }

            $files = File::allFiles($path);
            foreach ($files as $file) {
                if (in_array($file->getExtension(), ['php', 'blade.php', 'js', 'vue', 'ts'])) {
                    $content = File::get($file);

                    foreach ($patterns as $pattern) {
                        preg_match_all($pattern, $content, $matches);
                        foreach ($matches[1] as $match) {
                            // Skip if it's likely a translation key
                            if (strpos($match, '.') !== false || strpos($match, '_') !== false) {
                                continue;
                            }

                            $hardcodedStrings[] = [
                                'string' => $match,
                                'file' => $file->getRelativePathname(),
                                'suggested_key' => $this->generateTranslationKey($match),
                            ];
                        }
                    }
                }
            }
        }

        if (empty($hardcodedStrings)) {
            $this->info('✅ No hardcoded strings found!');

            return 0;
        }

        $this->warn('Found '.count($hardcodedStrings).' potentially hardcoded strings:');

        $headers = ['String', 'File', 'Suggested Key'];
        $this->table($headers, array_slice($hardcodedStrings, 0, 20)); // Limit output

        if (count($hardcodedStrings) > 20) {
            $this->info('... and '.(count($hardcodedStrings) - 20).' more');
        }

        return 0;
    }

    /**
     * Show missing translations for a locale.
     */
    private function showMissingTranslations(): int
    {
        $locale = $this->option('locale');

        if (! $locale) {
            $locale = $this->choice('Select locale to check for missing translations:', $this->availableLocales);
        }

        if (! in_array($locale, $this->availableLocales)) {
            $this->error("Unsupported locale: {$locale}");

            return 1;
        }

        $this->info("🔍 Checking missing translations for locale: {$locale}");

        $missingKeys = TranslationService::getMissingKeys($locale);

        if (empty($missingKeys)) {
            $this->info("✅ No missing translations found for {$locale}!");

            return 0;
        }

        $this->warn('Found '.count($missingKeys).' missing translation keys:');

        $tableData = [];
        foreach (array_slice($missingKeys, 0, 20) as $key) {
            $tableData[] = [$key];
        }

        $this->table(['Missing Keys'], $tableData);

        if (count($missingKeys) > 20) {
            $this->info('... and '.(count($missingKeys) - 20).' more');
        }

        return 0;
    }

    /**
     * Sync translations from source to target locale.
     */
    private function syncTranslations(): int
    {
        $targetLocale = $this->option('locale');
        $sourceLocale = $this->option('source') ?? 'en';

        if (! $targetLocale) {
            $targetLocale = $this->choice('Select target locale:', $this->availableLocales);
        }

        if (! in_array($targetLocale, $this->availableLocales) || ! in_array($sourceLocale, $this->availableLocales)) {
            $this->error('Unsupported locale');

            return 1;
        }

        $this->info("🔄 Syncing translations from {$sourceLocale} to {$targetLocale}...");

        $sourceTranslations = TranslationService::getAllTranslations($sourceLocale);
        $targetTranslations = TranslationService::getAllTranslations($targetLocale);
        $missingKeys = TranslationService::getMissingKeys($targetLocale, $sourceLocale);

        if (empty($missingKeys)) {
            $this->info('✅ No missing translations to sync!');

            return 0;
        }

        $synced = 0;
        $autoTranslate = $this->option('auto-translate');

        foreach ($missingKeys as $key) {
            if (isset($sourceTranslations[$key])) {
                if ($autoTranslate) {
                    // In a real implementation, integrate with translation service
                    $targetTranslations[$key] = '[AUTO] '.$sourceTranslations[$key];
                } else {
                    $targetTranslations[$key] = "[{$targetLocale}] ".$sourceTranslations[$key];
                }
                $synced++;
            }
        }

        // Save to JSON file
        $filePath = lang_path("{$targetLocale}.json");
        File::put($filePath, json_encode($targetTranslations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Clear cache
        TranslationService::clearCache();

        $this->info("✅ Synced {$synced} translation keys!");

        return 0;
    }

    /**
     * Show translation statistics.
     */
    private function showStatistics(): int
    {
        $this->info('📊 Translation Statistics');

        $stats = TranslationService::getStatistics();
        $tableData = [];

        foreach ($stats as $locale => $stat) {
            $localeConfig = Config::get("app.available_locales.{$locale}", []);
            $flag = $this->getFlag($locale);
            $rtl = LanguageHelper::isRtl($locale) ? ' (RTL)' : '';

            $tableData[] = [
                "{$flag} {$locale}{$rtl}",
                $localeConfig['native'] ?? $locale,
                $stat['translated_keys'],
                $stat['missing_keys'],
                $stat['coverage_percentage'].'%',
                $stat['is_complete'] ? '✅' : '❌',
            ];
        }

        $headers = ['Locale', 'Language', 'Translated', 'Missing', 'Coverage', 'Complete'];
        $this->table($headers, $tableData);

        return 0;
    }

    /**
     * Export translations.
     */
    private function exportTranslations(): int
    {
        $locale = $this->option('locale');
        $format = $this->option('format');
        $file = $this->option('file');

        if (! $locale) {
            $locale = $this->choice('Select locale to export:', $this->availableLocales);
        }

        if (! in_array($locale, $this->availableLocales)) {
            $this->error("Unsupported locale: {$locale}");

            return 1;
        }

        if (! $file) {
            $file = storage_path("app/exports/{$locale}-translations.{$format}");
        }

        $this->info("📤 Exporting {$locale} translations to {$file}...");

        $translations = TranslationService::getAllTranslations($locale);

        // Ensure directory exists
        File::ensureDirectoryExists(dirname($file));

        if ($format === 'json') {
            $content = json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            $content = "<?php\n\nreturn ".var_export($translations, true).";\n";
        }

        File::put($file, $content);

        $this->info('✅ Exported '.count($translations)." translations to {$file}");

        return 0;
    }

    /**
     * Import translations.
     */
    private function importTranslations(): int
    {
        $locale = $this->option('locale');
        $file = $this->option('file');
        $merge = $this->option('merge');

        if (! $locale) {
            $locale = $this->choice('Select target locale:', $this->availableLocales);
        }

        if (! $file) {
            $this->error('File path is required for import');

            return 1;
        }

        if (! File::exists($file)) {
            $this->error("File not found: {$file}");

            return 1;
        }

        $this->info("📥 Importing translations to {$locale} from {$file}...");

        $content = File::get($file);

        if (str_ends_with($file, '.json')) {
            $importedTranslations = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error('Invalid JSON file');

                return 1;
            }
        } else {
            // Assume PHP file
            $importedTranslations = include $file;
            if (! is_array($importedTranslations)) {
                $this->error('Invalid PHP array file');

                return 1;
            }
        }

        $targetTranslations = [];
        if ($merge) {
            $targetTranslations = TranslationService::getAllTranslations($locale);
        }

        $targetTranslations = array_merge($targetTranslations, $importedTranslations);

        // Save to JSON file
        $filePath = lang_path("{$locale}.json");
        File::put($filePath, json_encode($targetTranslations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Clear cache
        TranslationService::clearCache();

        $this->info('✅ Imported '.count($importedTranslations).' translations!');

        return 0;
    }

    /**
     * Generate a translation key from a string.
     */
    private function generateTranslationKey(string $string): string
    {
        $key = strtolower($string);
        $key = preg_replace('/[^a-z0-9\s]/', '', $key);
        $key = str_replace(' ', '_', trim($key));

        // Add appropriate namespace based on content
        if (preg_match('/\b(save|edit|delete|create|add|cancel|submit)\b/i', $string)) {
            return "common.{$key}";
        }
        if (preg_match('/\b(error|success|warning|info)\b/i', $string)) {
            return "messages.{$key}";
        }

        return "app.{$key}";
    }

    /**
     * Show help information.
     */
    private function showHelp(): void
    {
        $this->info('Available actions:');
        $this->line('  scan      - Scan for hardcoded strings');
        $this->line('  missing   - Show missing translations for a locale');
        $this->line('  sync      - Sync translations from source to target locale');
        $this->line('  stats     - Show translation statistics');
        $this->line('  export    - Export translations to a file');
        $this->line('  import    - Import translations from a file');
    }

    /**
     * Get flag emoji for locale.
     */
    private function getFlag(string $locale): string
    {
        $flags = [
            'en' => '🇺🇸',
            'ar' => '🇸🇦',
            'de' => '🇩🇪',
            'es' => '🇪🇸',
            'fr' => '🇫🇷',
            'pt' => '🇵🇹',
            'ru' => '🇷🇺',
            'tr' => '🇹🇷',
            'zh' => '🇨🇳',
        ];

        return $flags[$locale] ?? '🌐';
    }
}
