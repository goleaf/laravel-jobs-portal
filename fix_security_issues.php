<?php

/**
 * Security Fixes Script for Blade Templates
 * Addresses unescaped output {!! !!} and missing CSRF protection
 * 
 * Based on Context7 Laravel security best practices
 */

require_once __DIR__ . '/vendor/autoload.php';

class SecurityFixer
{
    private $fixedFiles = 0;
    private $securityIssuesFixed = 0;
    private $fixes = [];
    private $warnings = [];
    
    public function __construct()
    {
        echo "🔒 Starting Security Fixes using Context7 Laravel Security Patterns...\n\n";
    }
    
    /**
     * Fix all security issues
     */
    public function fixAll()
    {
        $this->fixUnescapedOutput();
        $this->addMissingCsrfTokens();
        $this->generateSecurityReport();
        
        return $this;
    }
    
    /**
     * Fix unescaped output {!! !!} to escaped {{ }} where safe
     */
    private function fixUnescapedOutput()
    {
        echo "1. 🛡️ Fixing unescaped output issues...\n";
        
        $bladeFiles = $this->getAllBladeFiles();
        $safeToConvert = [
            'components/forms/button.blade.php',
            'components/forms/input.blade.php',
            'components/forms/select.blade.php',
            'components/forms/checkbox.blade.php',
            'components/forms/radio.blade.php',
            'components/forms/submit-button.blade.php'
        ];
        
        foreach ($bladeFiles as $file) {
            $relativePath = str_replace('resources/views/', '', $file);
            
            // Only fix files that are safe to convert
            if ($this->isSafeToConvert($relativePath, $safeToConvert)) {
                $content = file_get_contents($file);
                $originalContent = $content;
                
                // Fix simple variable outputs that should be escaped
                $content = preg_replace('/\{!!\s*\$([a-zA-Z_][a-zA-Z0-9_]*)\s*!!\}/', '{{ $$1 }}', $content);
                
                // Fix object property outputs that should be escaped
                $content = preg_replace('/\{!!\s*\$([a-zA-Z_][a-zA-Z0-9_]*)->([a-zA-Z_][a-zA-Z0-9_]*)\s*!!\}/', '{{ $$1->$2 }}', $content);
                
                // Fix array access that should be escaped
                $content = preg_replace('/\{!!\s*\$([a-zA-Z_][a-zA-Z0-9_]*)\[\'([^\']+)\'\]\s*!!\}/', '{{ $$1[\'$2\'] }}', $content);
                
                if ($content !== $originalContent) {
                    file_put_contents($file, $content);
                    $this->fixedFiles++;
                    $this->securityIssuesFixed++;
                    $this->fixes[] = "Fixed unescaped output in: " . basename($file);
                    echo "  ✅ Fixed: " . basename($file) . "\n";
                }
            } else {
                // Check for unescaped output that needs manual review
                $content = file_get_contents($file);
                if (preg_match('/\{!![^}]*!!\}/', $content)) {
                    $this->warnings[] = "Manual review needed for unescaped output in: " . basename($file);
                }
            }
        }
        echo "  ✅ Unescaped output fixes complete\n\n";
    }
    
    /**
     * Add missing CSRF tokens to forms
     */
    private function addMissingCsrfTokens()
    {
        echo "2. 🔐 Adding missing CSRF protection...\n";
        
        $bladeFiles = $this->getAllBladeFiles();
        
        foreach ($bladeFiles as $file) {
            $content = file_get_contents($file);
            $originalContent = $content;
            
            // Find POST forms without CSRF protection
            if (preg_match('/<form[^>]*method\s*=\s*[\'"]post[\'"][^>]*>/i', $content)) {
                if (!preg_match('/@csrf|csrf_token|_token/', $content)) {
                    // Add @csrf after opening form tag
                    $content = preg_replace(
                        '/(<form[^>]*method\s*=\s*[\'"]post[\'"][^>]*>\s*)/i',
                        '$1' . "\n    @csrf\n",
                        $content
                    );
                    
                    if ($content !== $originalContent) {
                        file_put_contents($file, $content);
                        $this->fixedFiles++;
                        $this->securityIssuesFixed++;
                        $this->fixes[] = "Added CSRF protection to: " . basename($file);
                        echo "  ✅ Added CSRF to: " . basename($file) . "\n";
                    }
                }
            }
        }
        echo "  ✅ CSRF protection fixes complete\n\n";
    }
    
    /**
     * Check if file is safe to automatically convert unescaped output
     */
    private function isSafeToConvert($filePath, $safeFiles)
    {
        foreach ($safeFiles as $safeFile) {
            if (strpos($filePath, $safeFile) !== false) {
                return true;
            }
        }
        
        // Additional safe patterns
        $safePatterns = [
            '/components\/forms\//',
            '/table-components\//',
            '/modals\/.*modal\.blade\.php$/'
        ];
        
        foreach ($safePatterns as $pattern) {
            if (preg_match($pattern, $filePath)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get all blade files
     */
    private function getAllBladeFiles()
    {
        $files = [];
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator('resources/views')
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $filePath = $file->getPathname();
                if (strpos($filePath, '.blade.php') !== false) {
                    $files[] = $filePath;
                }
            }
        }
        
        return $files;
    }
    
    /**
     * Generate security report
     */
    private function generateSecurityReport()
    {
        echo str_repeat("=", 70) . "\n";
        echo "🔒 SECURITY FIXES COMPLETED - CONTEXT7 PATTERNS APPLIED\n";
        echo str_repeat("=", 70) . "\n\n";
        
        echo "📊 SECURITY SUMMARY:\n";
        echo "- Files Fixed: {$this->fixedFiles}\n";
        echo "- Security Issues Resolved: {$this->securityIssuesFixed}\n";
        echo "- Manual Reviews Needed: " . count($this->warnings) . "\n\n";
        
        if (!empty($this->fixes)) {
            echo "🛡️ SECURITY FIXES APPLIED:\n";
            foreach (array_slice($this->fixes, 0, 15) as $i => $fix) {
                echo "  " . ($i + 1) . ". {$fix}\n";
            }
            
            if (count($this->fixes) > 15) {
                echo "  ... and " . (count($this->fixes) - 15) . " more fixes\n";
            }
            echo "\n";
        }
        
        if (!empty($this->warnings)) {
            echo "⚠️  MANUAL REVIEW REQUIRED:\n";
            foreach (array_slice($this->warnings, 0, 10) as $i => $warning) {
                echo "  " . ($i + 1) . ". {$warning}\n";
            }
            
            if (count($this->warnings) > 10) {
                echo "  ... and " . (count($this->warnings) - 10) . " more files need review\n";
            }
            echo "\n";
        }
        
        echo "📋 CONTEXT7 SECURITY BEST PRACTICES APPLIED:\n";
        echo "✅ Escaped output: {{ \$variable }} for user input\n";
        echo "✅ CSRF protection: @csrf directive in POST forms\n";
        echo "✅ Safe unescaped output: Only for trusted content\n";
        echo "✅ Manual review: Complex cases flagged for inspection\n\n";
        
        echo "🔄 NEXT SECURITY STEPS:\n";
        echo "1. Review files flagged for manual inspection\n";
        echo "2. Test all forms to ensure CSRF protection works\n";
        echo "3. Verify no functionality broken by escaping changes\n";
        echo "4. Consider input validation at controller level\n";
        echo "5. Run security scan to verify improvements\n\n";
        
        echo "✅ Critical security fixes complete!\n";
    }
}

// Execute security fixes
try {
    $fixer = new SecurityFixer();
    $fixer->fixAll();
    
} catch (Exception $e) {
    echo "❌ Error during security fixes: " . $e->getMessage() . "\n";
    exit(1);
} 