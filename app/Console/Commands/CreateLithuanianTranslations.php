<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;

class CreateLithuanianTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:create-lithuanian';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Lithuanian translations from English translations';

    /**
     * Lithuanian translations for common terms
     */
    protected array $commonTranslations = [
        // Common words
        'search' => 'Paieška',
        'reset' => 'Atstatyti',
        'actions' => 'Veiksmai',
        'save' => 'Išsaugoti',
        'cancel' => 'Atšaukti',
        'edit' => 'Redaguoti',
        'delete' => 'Ištrinti',
        'view' => 'Peržiūrėti',
        'back' => 'Atgal',
        'loading' => 'Kraunama...',
        'confirmation' => 'Patvirtinimas',
        'yes' => 'Taip',
        'no' => 'Ne',
        'active' => 'Aktyvus',
        'inactive' => 'Neaktyvus',
        'status' => 'Būsena',
        'date' => 'Data',
        'showing' => 'Rodoma',
        'to' => 'iki',
        'of' => 'iš',
        'results' => 'rezultatų',
        'no_results' => 'Rezultatų nėra',
        'per_page' => 'per puslapį',
        
        // Job-related terms
        'job' => 'Darbas',
        'jobs' => 'Darbai',
        'job_title' => 'Darbo pavadinimas',
        'company' => 'Įmonė',
        'companies' => 'Įmonės',
        'description' => 'Aprašymas',
        'salary' => 'Atlyginimas',
        'position' => 'Pozicija',
        'type' => 'Tipas',
        'category' => 'Kategorija',
        'categories' => 'Kategorijos',
        'featured' => 'Išskirtas',
        'suspended' => 'Sustabdytas',
        'freelance' => 'Laisvai samdomas',
        'experience' => 'Patirtis',
        'requirements' => 'Reikalavimai',
        'location' => 'Vieta',
        'published' => 'Paskelbta',
        'draft' => 'Juodraštis',
        'archived' => 'Archyvuotas',
        'apply' => 'Kandidatuoti',
        'details' => 'Detalės',
        
        // User-related terms
        'password' => 'Slaptažodis',
        'email' => 'El. paštas',
        'gender' => 'Lytis',
        'country' => 'Šalis',
        'state' => 'Valstija',
        'city' => 'Miestas',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Lithuanian translations creation...');
        
        // Load English translations
        $englishTranslationsPath = resource_path('lang/en.php');
        if (!File::exists($englishTranslationsPath)) {
            $this->error('English translations file not found. Run translations:consolidate first.');
            return Command::FAILURE;
        }
        
        $englishTranslations = require $englishTranslationsPath;
        
        // Create Lithuanian translations directory if it doesn't exist
        $lithuanianPath = resource_path('lang/lt.php');
        
        // Start with existing Lithuanian translations if available
        if (File::exists($lithuanianPath)) {
            $this->info('Found existing Lithuanian translations. Merging with new translations.');
            $lithuanianTranslations = require $lithuanianPath;
        } else {
            $lithuanianTranslations = [];
        }
        
        // Add auth translations
        if (!isset($lithuanianTranslations['auth'])) {
            $lithuanianTranslations['auth'] = [
                'failed' => 'Šie kredencialai neatitinka mūsų įrašų.',
                'password' => 'Pateiktas slaptažodis yra neteisingas.',
                'throttle' => 'Per daug bandymų prisijungti. Bandykite dar kartą po :seconds sekundžių.',
            ];
        }
        
        // Add pagination translations
        if (!isset($lithuanianTranslations['pagination'])) {
            $lithuanianTranslations['pagination'] = [
                'previous' => '&laquo; Ankstesnis',
                'next' => 'Sekantis &raquo;',
                'showing' => 'Rodoma',
                'to' => 'iki',
                'of' => 'iš',
                'results' => 'rezultatų',
                'go_to_page' => 'Eiti į puslapį',
            ];
        }
        
        // Add passwords translations
        if (!isset($lithuanianTranslations['passwords'])) {
            $lithuanianTranslations['passwords'] = [
                'reset' => 'Jūsų slaptažodis pakeistas!',
                'sent' => 'Slaptažodžio atkūrimo nuoroda išsiųsta!',
                'throttled' => 'Palaukite prieš bandydami dar kartą.',
                'token' => 'Šis slaptažodžio atkūrimo raktas yra neteisingas.',
                'user' => 'Negalime rasti vartotojo su šiuo el. pašto adresu.',
            ];
        }
        
        // Process all English translations and create Lithuanian versions
        foreach ($englishTranslations as $section => $translations) {
            if (!isset($lithuanianTranslations[$section])) {
                $lithuanianTranslations[$section] = [];
            }
            
            $lithuanianTranslations[$section] = $this->translateSection($section, $translations, $lithuanianTranslations[$section]);
        }
        
        // Write Lithuanian translations to file
        $content = "<?php\n\nreturn " . var_export($lithuanianTranslations, true) . ";\n";
        File::put($lithuanianPath, $content);
        
        $this->info('Lithuanian translations created successfully!');
        
        return Command::SUCCESS;
    }
    
    /**
     * Translate a section of translations
     *
     * @param string $section
     * @param array $englishTranslations
     * @param array $existingLithuanianTranslations
     * @return array
     */
    private function translateSection(string $section, array $englishTranslations, array $existingLithuanianTranslations = []): array
    {
        $result = $existingLithuanianTranslations;
        
        foreach ($englishTranslations as $key => $value) {
            // Skip if translation already exists
            if (isset($result[$key])) {
                continue;
            }
            
            if (is_array($value)) {
                // Recursively translate nested arrays
                $result[$key] = $this->translateSection("{$section}.{$key}", $value, $result[$key] ?? []);
            } else {
                // Translate string value
                $result[$key] = $this->translateString($value, "{$section}.{$key}");
            }
        }
        
        return $result;
    }
    
    /**
     * Translate a string to Lithuanian
     *
     * @param string $englishString
     * @param string $translationKey
     * @return string
     */
    private function translateString(string $englishString, string $translationKey): string
    {
        // Find direct match in common translations
        foreach ($this->commonTranslations as $english => $lithuanian) {
            if (strtolower($englishString) === strtolower($english)) {
                return $lithuanian;
            }
        }
        
        // Create a placeholder for missing translation
        return "[REIKIA_IŠVERSTI] {$englishString}";
    }
} 