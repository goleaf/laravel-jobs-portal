<?php

/**
 * Critical Blade Syntax Fixes Script
 * Fixes the most critical syntax errors found in blade analysis
 * 
 * Based on comprehensive analysis that found 925/934 files with syntax errors
 */

require_once __DIR__ . '/vendor/autoload.php';

class CriticalBladeFixer
{
    private $fixedFiles = 0;
    private $totalIssues = 0;
    private $fixes = [];
    
    public function __construct()
    {
        echo "🔧 Starting Critical Blade Syntax Fixes using Universal patterns...\n\n";
    }
    
    /**
     * Fix all critical syntax issues
     */
    public function fixAll()
    {
        $this->fixSvgSpaceIssues();
        $this->fixCommentSyntax();
        $this->fixDoubleDollarSigns();
        $this->generateReport();
        
        return $this;
    }
    
    /**
     * Fix missing spaces in SVG tags
     */
    private function fixSvgSpaceIssues()
    {
        echo "1. 🎯 Fixing SVG space issues...\n";
        
        $iconFiles = glob('resources/views/components/icons/*.blade.php');
        
        foreach ($iconFiles as $file) {
            $content = file_get_contents($file);
            $originalContent = $content;
            
            // Fix missing space between <svg and {{
            $content = preg_replace('/<svg\{\{/', '<svg {{', $content);
            
            if ($content !== $originalContent) {
                file_put_contents($file, $content);
                $this->fixedFiles++;
                $this->fixes[] = "Fixed SVG space in: " . basename($file);
                echo "  ✅ Fixed: " . basename($file) . "\n";
            }
        }
        echo "  ✅ SVG space fixes complete\n\n";
    }
    
    /**
     * Fix incorrect comment syntax
     */
    private function fixCommentSyntax()
    {
        echo "2. 🎯 Fixing comment syntax issues...\n";
        
        $bladeFiles = $this->getAllBladeFiles();
        
        foreach ($bladeFiles as $file) {
            $content = file_get_contents($file);
            $originalContent = $content;
            
            // Fix incorrect comment syntax {{ -- -- }} to {{-- --}}
            $content = preg_replace('/\{\{\s*--\s*(.*?)\s*--\s*\}\}/', '{{-- $1 --}}', $content);
            
            if ($content !== $originalContent) {
                file_put_contents($file, $content);
                $this->fixedFiles++;
                $this->fixes[] = "Fixed comment syntax in: " . basename($file);
                echo "  ✅ Fixed: " . basename($file) . "\n";
            }
        }
        echo "  ✅ Comment syntax fixes complete\n\n";
    }
    
    /**
     * Fix double dollar sign issues (partial fix for common cases)
     */
    private function fixDoubleDollarSigns()
    {
        echo "3. 🎯 Fixing critical double dollar sign issues...\n";
        
        $bladeFiles = $this->getAllBladeFiles();
        
        foreach ($bladeFiles as $file) {
            $content = file_get_contents($file);
            $originalContent = $content;
            
            // Fix common pattern: $$variable -> $variable
            $content = preg_replace('/\$\$([a-zA-Z_][a-zA-Z0-9_]*)/', '$$$1', $content);
            
            // Fix pattern like $$row->$column to $row->column
            $content = preg_replace('/\$\$([a-zA-Z_][a-zA-Z0-9_]*)->\$([a-zA-Z_][a-zA-Z0-9_]*)/', '$$$1->$2', $content);
            
            if ($content !== $originalContent) {
                file_put_contents($file, $content);
                $this->fixedFiles++;
                $this->fixes[] = "Fixed double dollar signs in: " . basename($file);
                echo "  ✅ Fixed: " . basename($file) . "\n";
            }
        }
        echo "  ✅ Double dollar sign fixes complete\n\n";
    }
    
    /**
     * Get all blade files for processing
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
     * Generate fix report
     */
    private function generateReport()
    {
        echo str_repeat("=", 60) . "\n";
        echo "🎯 CRITICAL SYNTAX FIXES COMPLETED\n";
        echo str_repeat("=", 60) . "\n\n";
        
        echo "📊 SUMMARY:\n";
        echo "- Files Fixed: {$this->fixedFiles}\n";
        echo "- Total Fixes Applied: " . count($this->fixes) . "\n\n";
        
        if (!empty($this->fixes)) {
            echo "🔧 FIXES APPLIED:\n";
            foreach (array_slice($this->fixes, 0, 20) as $i => $fix) {
                echo "  " . ($i + 1) . ". {$fix}\n";
            }
            
            if (count($this->fixes) > 20) {
                echo "  ... and " . (count($this->fixes) - 20) . " more fixes\n";
            }
        }
        
        echo "\n💡 NEXT STEPS:\n";
        echo "1. Run blade analysis again to verify fixes\n";
        echo "2. Clear view cache: php artisan view:clear\n";
        echo "3. Test critical pages to ensure no breaking changes\n";
        echo "4. Continue with TailwindCSS migration for Bootstrap files\n";
        echo "5. Address security issues (unescaped output)\n\n";
        
        echo "✅ Critical syntax fixes complete!\n";
    }
}

// Execute the fixes
try {
    $fixer = new CriticalBladeFixer();
    $fixer->fixAll();
    
} catch (Exception $e) {
    echo "❌ Error during fixes: " . $e->getMessage() . "\n";
    exit(1);
} 