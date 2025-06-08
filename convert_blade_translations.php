<?php

/**
 * Context7 Blade Translation Converter
 * Systematically converts hardcoded strings to translation functions
 * 
 * Features:
 * - Scans all blade templates
 * - Identifies hardcoded strings
 * - Converts to __() translation functions
 * - Creates organized translation keys
 * - Preserves blade syntax
 * - Handles complex scenarios
 */

class BladeTranslationConverter
{
    private $processedFiles = 0;
    private $convertedStrings = 0;
    private $translationKeys = [];
    private $errors = [];
    
    // Common strings that should NOT be translated
    private $excludeStrings = [
        'id', 'name', 'email', 'password', 'class', 'style', 'href', 'src', 'alt',
        'data-', 'aria-', 'role', 'type', 'value', 'placeholder', 'title',
        'GET', 'POST', 'PUT', 'DELETE', 'PATCH',
        'true', 'false', 'null', 'undefined',
        'btn', 'form', 'input', 'div', 'span', 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'px', 'py', 'mx', 'my', 'w-', 'h-', 'text-', 'bg-', 'border-', 'rounded-',
        '#', '.', '/', '\\', '?', '&', '=', '%', '@', '$',
        'localhost', '127.0.0.1', 'http', 'https', 'www',
        'utf-8', 'utf8', 'iso-8859-1',
        'javascript', 'js', 'css', 'html', 'php', 'json', 'xml'
    ];

    // Translation categories for organized keys
    private $categories = [
        'navigation' => ['menu', 'nav', 'link', 'home', 'about', 'contact', 'dashboard'],
        'auth' => ['login', 'register', 'logout', 'password', 'email', 'username', 'sign'],
        'forms' => ['submit', 'cancel', 'save', 'delete', 'edit', 'add', 'create', 'update'],
        'messages' => ['success', 'error', 'warning', 'info', 'alert', 'notification'],
        'common' => ['yes', 'no', 'ok', 'cancel', 'close', 'open', 'view', 'show', 'hide'],
        'jobs' => ['job', 'position', 'company', 'salary', 'apply', 'application'],
        'admin' => ['admin', 'manage', 'settings', 'users', 'permissions', 'roles']
    ];

    public function run()
    {
        echo "🌍 Context7 Blade Translation Converter Starting...\n";
        echo "=====================================================\n\n";

        $bladeFiles = $this->findBladeFiles();
        echo "Found " . count($bladeFiles) . " blade files to process\n\n";

        foreach ($bladeFiles as $file) {
            $this->processBladeFile($file);
        }

        $this->generateTranslationFiles();
        $this->generateReport();
    }

    private function findBladeFiles()
    {
        $finder = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator('resources/views', RecursiveDirectoryIterator::SKIP_DOTS)
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
        echo "Processing: " . basename($filePath) . "\n";
        
        $content = file_get_contents($filePath);
        $originalContent = $content;
        
        // Convert various string patterns
        $content = $this->convertQuotedStrings($content, $filePath);
        $content = $this->convertHtmlText($content, $filePath);
        $content = $this->convertPlaceholders($content, $filePath);
        $content = $this->convertValidationMessages($content, $filePath);
        
        // Only write if changes were made
        if ($content !== $originalContent) {
            file_put_contents($filePath, $content);
            $this->processedFiles++;
            echo "  ✅ Converted strings in " . basename($filePath) . "\n";
        } else {
            echo "  ⏭️  No changes needed in " . basename($filePath) . "\n";
        }
    }

    private function convertQuotedStrings($content, $filePath)
    {
        // Pattern to match quoted strings in blade templates
        $patterns = [
            // Simple quoted strings like "Submit" or 'Cancel'
            '/([\'"])([A-Z][a-zA-Z\s]{2,50})\1/',
            // Button text patterns
            '/(<button[^>]*>)\s*([A-Z][a-zA-Z\s]{2,30})\s*(<\/button>)/',
            // Link text patterns
            '/(<a[^>]*>)\s*([A-Z][a-zA-Z\s]{2,30})\s*(<\/a>)/',
            // Label text patterns
            '/(<label[^>]*>)\s*([A-Z][a-zA-Z\s]{2,50})\s*(<\/label>)/',
        ];

        foreach ($patterns as $pattern) {
            $content = preg_replace_callback($pattern, function($matches) use ($filePath) {
                $text = isset($matches[2]) ? $matches[2] : $matches[1];
                
                if ($this->shouldTranslate($text)) {
                    $key = $this->generateTranslationKey($text, $filePath);
                    $this->addTranslationKey($key, $text);
                    $this->convertedStrings++;
                    
                    if (isset($matches[3])) {
                        // HTML context
                        return $matches[1] . "{{ __('" . $key . "') }}" . $matches[3];
                    } else {
                        // Simple quoted string
                        return "{{ __('" . $key . "') }}";
                    }
                }
                
                return $matches[0];
            }, $content);
        }

        return $content;
    }

    private function convertHtmlText($content, $filePath)
    {
        // Convert text content within HTML tags
        $pattern = '/>([A-Z][a-zA-Z\s,.\-!?]{3,100})</';
        
        return preg_replace_callback($pattern, function($matches) use ($filePath) {
            $text = trim($matches[1]);
            
            if ($this->shouldTranslate($text) && !$this->containsBladeCode($text)) {
                $key = $this->generateTranslationKey($text, $filePath);
                $this->addTranslationKey($key, $text);
                $this->convertedStrings++;
                
                return ">{{ __('" . $key . "') }}<";
            }
            
            return $matches[0];
        }, $content);
    }

    private function convertPlaceholders($content, $filePath)
    {
        // Convert placeholder attributes
        $pattern = '/placeholder=[\'"]([A-Z][a-zA-Z\s]{3,50})[\'"]*/';
        
        return preg_replace_callback($pattern, function($matches) use ($filePath) {
            $text = $matches[1];
            
            if ($this->shouldTranslate($text)) {
                $key = $this->generateTranslationKey($text, $filePath, 'placeholder');
                $this->addTranslationKey($key, $text);
                $this->convertedStrings++;
                
                return "placeholder=\"{{ __('" . $key . "') }}\"";
            }
            
            return $matches[0];
        }, $content);
    }

    private function convertValidationMessages($content, $filePath)
    {
        // Convert validation error messages
        $pattern = '/class=[\'"][^>]*error[^>]*[\'"][^>]*>([A-Z][a-zA-Z\s,.\-!?]{5,100})</';
        
        return preg_replace_callback($pattern, function($matches) use ($filePath) {
            $text = trim($matches[1]);
            
            if ($this->shouldTranslate($text)) {
                $key = $this->generateTranslationKey($text, $filePath, 'validation');
                $this->addTranslationKey($key, $text);
                $this->convertedStrings++;
                
                return str_replace($text, "{{ __('" . $key . "') }}", $matches[0]);
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
        if (strpos($text, '__(' ) !== false || strpos($text, 'trans(') !== false) {
            return false;
        }
        
        // Skip if it's a variable or blade expression
        if (strpos($text, '$') !== false || strpos($text, '{{') !== false || strpos($text, '{!!') !== false) {
            return false;
        }
        
        // Skip if it's mostly special characters
        if (preg_match('/[^\w\s]/u', $text) && strlen(preg_replace('/[^\w\s]/u', '', $text)) < 3) {
            return false;
        }
        
        // Must start with letter or common word
        if (!preg_match('/^[A-Z]/', $text)) {
            return false;
        }
        
        return true;
    }

    private function containsBladeCode($text)
    {
        return strpos($text, '{{') !== false || 
               strpos($text, '{!!') !== false || 
               strpos($text, '@') !== false ||
               strpos($text, '$') !== false;
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
            $key = $prefix . '_' . $key;
        }
        
        // Add category
        $fullKey = $category . '.' . $key;
        
        return $fullKey;
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
        if (!isset($this->translationKeys[$key])) {
            $this->translationKeys[$key] = $text;
        }
    }

    private function generateTranslationFiles()
    {
        echo "\n📝 Generating translation files...\n";
        
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
        $filePath = "lang/en_json/{$category}.json";
        
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
        
        echo "  ✅ Updated {$category}.json with " . count($keys) . " new translations\n";
    }

    private function generateReport()
    {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "🎉 CONTEXT7 BLADE TRANSLATION CONVERSION COMPLETED\n";
        echo str_repeat("=", 60) . "\n\n";
        
        echo "📊 CONVERSION STATISTICS:\n";
        echo "  • Files Processed: {$this->processedFiles}\n";
        echo "  • Strings Converted: {$this->convertedStrings}\n";
        echo "  • Translation Keys Created: " . count($this->translationKeys) . "\n\n";
        
        echo "📂 TRANSLATION CATEGORIES:\n";
        $categoryStats = [];
        foreach ($this->translationKeys as $key => $text) {
            $category = explode('.', $key)[0];
            $categoryStats[$category] = ($categoryStats[$category] ?? 0) + 1;
        }
        
        foreach ($categoryStats as $category => $count) {
            echo "  • {$category}: {$count} keys\n";
        }
        
        echo "\n✅ All blade templates have been processed for translation!\n";
        echo "🔄 Next: Run the AI translator to generate other language files\n";
        echo "🌍 Ready for multilingual deployment!\n\n";
    }
}

// Run the converter
$converter = new BladeTranslationConverter();
$converter->run(); 