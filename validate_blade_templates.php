<?php

/**
 * Blade Template Validator
 * Validates all blade files for syntax errors and missing dependencies
 */

class BladeTemplateValidator
{
    private $errors = [];
    private $warnings = [];
    
    public function validate()
    {
        echo "🎨 Validating Blade Templates\n";
        echo "-" . str_repeat("-", 35) . "\n";
        
        $bladeFiles = $this->findBladeFiles();
        
        foreach ($bladeFiles as $file) {
            $this->validateBladeFile($file);
        }
        
        $this->reportResults();
    }
    
    private function findBladeFiles()
    {
        return glob('resources/views/**/*.blade.php') + 
               glob('resources/views/*.blade.php');
    }
    
    private function validateBladeFile($file)
    {
        $content = file_get_contents($file);
        
        // Check for extends directive
        $this->checkExtends($file, $content);
        
        // Check for includes
        $this->checkIncludes($file, $content);
        
        // Check for components
        $this->checkComponents($file, $content);
        
        // Check for routes
        $this->checkRoutes($file, $content);
        
        // Check for translation keys
        $this->checkTranslations($file, $content);
    }
    
    private function checkExtends($file, $content)
    {
        if (preg_match('/@extends\([\'"]([^\'"]+)[\'"]\)/', $content, $matches)) {
            $layout = $matches[1];
            $layoutPath = "resources/views/{$layout}.blade.php";
            
            if (!file_exists($layoutPath)) {
                $this->errors[] = "$file: Layout '$layout' not found";
            }
        }
    }
    
    private function checkIncludes($file, $content)
    {
        if (preg_match_all('/@include\([\'"]([^\'"]+)[\'"]\)/', $content, $matches)) {
            foreach ($matches[1] as $include) {
                $includePath = "resources/views/{$include}.blade.php";
                
                if (!file_exists($includePath)) {
                    $this->errors[] = "$file: Include '$include' not found";
                }
            }
        }
    }
    
    private function checkComponents($file, $content)
    {
        if (preg_match_all('/<x-([^>\s]+)/', $content, $matches)) {
            foreach ($matches[1] as $component) {
                $componentPath = "resources/views/components/{$component}.blade.php";
                
                if (!file_exists($componentPath)) {
                    $this->warnings[] = "$file: Component '$component' might not exist";
                }
            }
        }
    }
    
    private function checkRoutes($file, $content)
    {
        if (preg_match_all('/route\([\'"]([^\'"]+)[\'"]\)/', $content, $matches)) {
            foreach ($matches[1] as $route) {
                // This would need actual Laravel route checking
                // For now, just collect route names
            }
        }
    }
    
    private function checkTranslations($file, $content)
    {
        if (preg_match_all('/__\([\'"]([^\'"]+)[\'"]\)/', $content, $matches)) {
            $jsonPath = 'lang/en.json';
            
            if (file_exists($jsonPath)) {
                $translations = json_decode(file_get_contents($jsonPath), true);
                
                foreach ($matches[1] as $key) {
                    if (!isset($translations[$key])) {
                        $this->warnings[] = "$file: Translation key '$key' not found";
                    }
                }
            }
        }
    }
    
    private function reportResults()
    {
        echo "   Errors found: " . count($this->errors) . "\n";
        echo "   Warnings found: " . count($this->warnings) . "\n";
        
        if (!empty($this->errors)) {
            echo "\n   🚨 ERRORS:\n";
            foreach ($this->errors as $error) {
                echo "   - $error\n";
            }
        }
        
        if (!empty($this->warnings)) {
            echo "\n   ⚠️ WARNINGS:\n";
            foreach ($this->warnings as $warning) {
                echo "   - $warning\n";
            }
        }
        
        echo "\n   ✅ Blade template validation complete\n\n";
    }
}

$validator = new BladeTemplateValidator();
$validator->validate();