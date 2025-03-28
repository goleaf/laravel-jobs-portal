<?php

namespace App\Console\Commands;

use App\Helpers\TranslationHelper;
use Illuminate\Console\Command;

class CheckTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:check {locale?} {--base=en}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for missing translations in a locale compared to the base locale';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $baseLocale = $this->option('base');
        $locale = $this->argument('locale');
        
        if (!$locale) {
            $availableLocales = array_keys(config('app.available_locales', []));
            $locales = array_filter($availableLocales, function ($l) use ($baseLocale) {
                return $l !== $baseLocale;
            });
        } else {
            $locales = [$locale];
        }
        
        foreach ($locales as $locale) {
            $this->info("Checking translations for locale: {$locale}");
            
            $missing = TranslationHelper::getMissingTranslations($locale, $baseLocale);
            
            if (empty($missing)) {
                $this->info("No missing translations found for {$locale}");
                continue;
            }
            
            $this->warn("Found " . count($missing) . " missing translations for {$locale}:");
            
            $headers = ['Key', 'Base Value'];
            $rows = [];
            
            foreach ($missing as $key => $value) {
                $rows[] = [$key, is_array($value) ? 'Array' : $value];
            }
            
            $this->table($headers, $rows);
            
            if ($this->confirm("Do you want to create a template file with the missing translations?")) {
                $path = resource_path("lang/{$locale}/missing.php");
                $content = "<?php\n\nreturn " . var_export($missing, true) . ";\n";
                file_put_contents($path, $content);
                $this->info("Missing translations saved to {$path}");
            }
        }
        
        return Command::SUCCESS;
    }
} 