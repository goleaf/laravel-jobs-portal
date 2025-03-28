<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class StandardizeTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:standardize {--lang=all : The language to standardize (default: all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Standardize translations across all languages';

    /**
     * The supported languages.
     *
     * @var array
     */
    protected $supportedLanguages = ['en', 'lt'];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $lang = $this->option('lang');
        
        $this->info('Starting translation standardization...');
        
        if ($lang === 'all') {
            foreach ($this->supportedLanguages as $language) {
                $this->standardizeTranslation($language);
            }
        } else {
            if (!in_array($lang, $this->supportedLanguages)) {
                $this->error("Language '$lang' is not supported.");
                return 1;
            }
            
            $this->standardizeTranslation($lang);
        }
        
        $this->info('Translation standardization completed successfully!');
        
        return 0;
    }
    
    /**
     * Standardize the translation for a specific language.
     *
     * @param string $lang
     * @return void
     */
    protected function standardizeTranslation($lang)
    {
        $this->info("Standardizing translations for language: $lang");
        
        $langFile = resource_path("lang/$lang.php");
        
        if (!File::exists($langFile)) {
            $this->error("Language file for '$lang' does not exist.");
            return;
        }
        
        $translations = require $langFile;
        
        // Ensure all translations are under the 'messages' key
        if (!isset($translations['messages'])) {
            $newTranslations = [
                'messages' => []
            ];
            
            // Move all top-level translations under 'messages'
            foreach ($translations as $key => $value) {
                if (is_array($value)) {
                    $newTranslations['messages'][$key] = $value;
                }
            }
            
            $translations = $newTranslations;
        }
        
        // Ensure specific translation groups are present
        $requiredGroups = [
            'common', 'flash', 'job', 'job_type', 'job_category', 
            'company', 'pagination', 'table', 'language'
        ];
        
        foreach ($requiredGroups as $group) {
            if (!isset($translations['messages'][$group])) {
                $translations['messages'][$group] = [];
            }
        }
        
        // Write standardized translations back to file
        $content = "<?php\n\nreturn " . $this->varExport($translations, true) . ";\n";
        File::put($langFile, $content);
        
        $this->info("Translations for '$lang' have been standardized.");
    }
    
    /**
     * Export a variable with proper formatting.
     *
     * @param mixed $var
     * @param bool $return
     * @return string|null
     */
    protected function varExport($var, $return = false)
    {
        if (is_array($var)) {
            $toImplode = [];
            
            foreach ($var as $key => $value) {
                $toImplode[] = var_export($key, true) . ' => ' . $this->varExport($value, true);
            }
            
            $code = 'array (' . PHP_EOL . '  ' . implode(',' . PHP_EOL . '  ', $toImplode) . PHP_EOL . ')';
            
            if ($return) {
                return $code;
            } else {
                echo $code;
            }
        } else {
            $code = var_export($var, true);
            
            if ($return) {
                return $code;
            } else {
                echo $code;
            }
        }
        
        return null;
    }
} 