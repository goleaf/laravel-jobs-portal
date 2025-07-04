<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class TranslationAudit extends Command
{
    protected $signature = 'translations:audit 
        {--lang=en : The language to audit}
        {--missing : Show only missing translations}
        {--unused : Show unused translation keys}';

    protected $description = 'Audit translation files for completeness and usage';

    public function handle()
    {
        $lang = $this->option('lang');
        $translationPath = resource_path("lang/{$lang}.json");

        if (!File::exists($translationPath)) {
            $this->error("Translation file for {$lang} not found!");
            return 1;
        }

        $translations = json_decode(File::get($translationPath), true);
        $bladeFiles = $this->findBladeFiles();

        $missingKeys = [];
        $unusedKeys = array_keys($translations);

        foreach ($bladeFiles as $file) {
            $content = File::get($file);
            preg_match_all('/\{\{\s*__\([\'"]([^\'"]+)[\'"]\)\s*\}\}/', $content, $matches);
            
            foreach ($matches[1] as $key) {
                $baseKey = explode('.', $key)[0];
                
                // Check if key exists
                if (!$this->keyExists($translations, $key)) {
                    $missingKeys[] = $key;
                }

                // Remove from unused keys
                $unusedKeys = array_filter($unusedKeys, function($existingKey) use ($key) {
                    return $existingKey !== $key;
                });
            }
        }

        if ($this->option('missing')) {
            $this->displayMissingKeys($missingKeys);
        }

        if ($this->option('unused')) {
            $this->displayUnusedKeys($unusedKeys);
        }

        if (!$this->option('missing') && !$this->option('unused')) {
            $this->displayFullReport($missingKeys, $unusedKeys);
        }

        return 0;
    }

    protected function findBladeFiles()
    {
        return File::allFiles(resource_path('views'));
    }

    protected function keyExists($translations, $key)
    {
        $parts = explode('.', $key);
        $current = $translations;

        foreach ($parts as $part) {
            if (!isset($current[$part])) {
                return false;
            }
            $current = $current[$part];
        }

        return true;
    }

    protected function displayMissingKeys($missingKeys)
    {
        $this->info("Missing Translation Keys:");
        foreach (array_unique($missingKeys) as $key) {
            $this->line("- {$key}");
        }
    }

    protected function displayUnusedKeys($unusedKeys)
    {
        $this->info("Unused Translation Keys:");
        foreach ($unusedKeys as $key) {
            $this->line("- {$key}");
        }
    }

    protected function displayFullReport($missingKeys, $unusedKeys)
    {
        $this->info("Translation Audit Report:");
        $this->line("Total Missing Keys: " . count(array_unique($missingKeys)));
        $this->line("Total Unused Keys: " . count($unusedKeys));

        $this->info("\nMissing Keys:");
        $this->displayMissingKeys($missingKeys);

        $this->info("\nUnused Keys:");
        $this->displayUnusedKeys($unusedKeys);
    }
} 