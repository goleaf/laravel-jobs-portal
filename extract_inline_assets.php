<?php

/**
 * Inline Asset Extraction Script
 * Extracts inline CSS/JS from blade templates to separate files
 * 
 * Based on Context7 Laravel asset management best practices
 */

require_once __DIR__ . '/vendor/autoload.php';

class InlineAssetExtractor
{
    private $extractedFiles = 0;
    private $cssExtracted = 0;
    private $jsExtracted = 0;
    private $extractions = [];
    
    public function __construct()
    {
        echo "📦 Starting Inline Asset Extraction using Context7 Patterns...\n\n";
        $this->ensureDirectories();
    }
    
    /**
     * Ensure asset directories exist
     */
    private function ensureDirectories()
    {
        $directories = [
            'resources/css/components',
            'resources/js/components',
            'resources/css/pages',
            'resources/js/pages'
        ];
        
        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
                echo "📁 Created directory: {$dir}\n";
            }
        }
        echo "\n";
    }
    
    /**
     * Extract all inline assets
     */
    public function extractAll()
    {
        echo "🔍 Scanning blade templates for inline assets...\n";
        
        $bladeFiles = $this->getAllBladeFiles();
        $priorityFiles = $this->getPriorityFiles($bladeFiles);
        
        echo "Processing " . count($priorityFiles) . " priority files\n\n";
        
        foreach ($priorityFiles as $file) {
            $this->extractFromFile($file);
        }
        
        $this->generateExtractionReport();
        
        return $this;
    }
    
    /**
     * Extract inline assets from a single file
     */
    private function extractFromFile($filePath)
    {
        $content = file_get_contents($filePath);
        $originalContent = $content;
        $filename = basename($filePath, '.blade.php');
        $fileChanged = false;
        
        // Extract CSS
        $cssExtracted = $this->extractInlineCSS($content, $filename);
        if ($cssExtracted) {
            $this->cssExtracted++;
            $fileChanged = true;
        }
        
        // Extract JavaScript
        $jsExtracted = $this->extractInlineJS($content, $filename);
        if ($jsExtracted) {
            $this->jsExtracted++;
            $fileChanged = true;
        }
        
        if ($fileChanged) {
            file_put_contents($filePath, $content);
            $this->extractedFiles++;
            $this->extractions[] = "Extracted assets from: " . basename($filePath);
            echo "  ✅ Extracted: " . basename($filePath) . "\n";
        }
    }
    
    /**
     * Extract inline CSS
     */
    private function extractInlineCSS(&$content, $filename)
    {
        $extracted = false;
        
        // Find <style> blocks
        preg_match_all('/<style[^>]*>(.*?)<\/style>/s', $content, $matches, PREG_OFFSET_CAPTURE);
        
        if (!empty($matches[0])) {
            $cssContent = '';
            $offsetAdjustment = 0;
            
            foreach ($matches[0] as $index => $match) {
                $fullMatch = $match[0];
                $cssInner = $matches[1][$index][0];
                $position = $match[1] - $offsetAdjustment;
                
                // Clean and format CSS
                $cleanCSS = $this->cleanCSS($cssInner);
                
                if (trim($cleanCSS)) {
                    $cssContent .= $cleanCSS . "\n\n";
                    
                    // Remove the <style> block from content
                    $content = substr_replace($content, '', $position, strlen($fullMatch));
                    $offsetAdjustment += strlen($fullMatch);
                    
                    $extracted = true;
                }
            }
            
            // Write CSS file if we have content
            if ($cssContent) {
                $cssFile = "resources/css/components/{$filename}.css";
                
                // Append to existing file or create new one
                if (file_exists($cssFile)) {
                    $cssContent = file_get_contents($cssFile) . "\n" . $cssContent;
                }
                
                file_put_contents($cssFile, $cssContent);
                
                // Add CSS import to blade file
                $cssImport = "\n@push('styles')\n    @vite('resources/css/components/{$filename}.css')\n@endpush\n";
                $content = $cssImport . $content;
            }
        }
        
        return $extracted;
    }
    
    /**
     * Extract inline JavaScript
     */
    private function extractInlineJS(&$content, $filename)
    {
        $extracted = false;
        
        // Find <script> blocks (but not src includes)
        preg_match_all('/<script(?![^>]*src\s*=)[^>]*>(.*?)<\/script>/s', $content, $matches, PREG_OFFSET_CAPTURE);
        
        if (!empty($matches[0])) {
            $jsContent = '';
            $offsetAdjustment = 0;
            
            foreach ($matches[0] as $index => $match) {
                $fullMatch = $match[0];
                $jsInner = $matches[1][$index][0];
                $position = $match[1] - $offsetAdjustment;
                
                // Clean and format JavaScript
                $cleanJS = $this->cleanJS($jsInner);
                
                if (trim($cleanJS)) {
                    $jsContent .= $cleanJS . "\n\n";
                    
                    // Remove the <script> block from content
                    $content = substr_replace($content, '', $position, strlen($fullMatch));
                    $offsetAdjustment += strlen($fullMatch);
                    
                    $extracted = true;
                }
            }
            
            // Write JS file if we have content
            if ($jsContent) {
                $jsFile = "resources/js/components/{$filename}.js";
                
                // Append to existing file or create new one
                if (file_exists($jsFile)) {
                    $jsContent = file_get_contents($jsFile) . "\n" . $jsContent;
                }
                
                // Wrap in document ready for Laravel/jQuery compatibility
                $wrappedJS = "document.addEventListener('DOMContentLoaded', function() {\n" . $jsContent . "\n});";
                
                file_put_contents($jsFile, $wrappedJS);
                
                // Add JS import to blade file
                $jsImport = "\n@push('scripts')\n    @vite('resources/js/components/{$filename}.js')\n@endpush\n";
                $content = $content . $jsImport;
            }
        }
        
        return $extracted;
    }
    
    /**
     * Clean and format CSS
     */
    private function cleanCSS($css)
    {
        // Remove extra whitespace but preserve formatting
        $css = trim($css);
        
        // Add component scoping comment
        if ($css) {
            $css = "/* Component-specific styles */\n" . $css;
        }
        
        return $css;
    }
    
    /**
     * Clean and format JavaScript
     */
    private function cleanJS($js)
    {
        // Remove extra whitespace but preserve functionality
        $js = trim($js);
        
        // Add component scoping comment
        if ($js) {
            $js = "// Component-specific JavaScript\n" . $js;
        }
        
        return $js;
    }
    
    /**
     * Get priority files to extract from first
     */
    private function getPriorityFiles($allFiles)
    {
        $priorityPatterns = [
            'components',
            'modals',
            'layouts',
            'dashboard',
            'auth'
        ];
        
        $priorityFiles = [];
        $regularFiles = [];
        
        foreach ($allFiles as $file) {
            $isPriority = false;
            foreach ($priorityPatterns as $pattern) {
                if (strpos($file, $pattern) !== false) {
                    $priorityFiles[] = $file;
                    $isPriority = true;
                    break;
                }
            }
            
            if (!$isPriority) {
                $regularFiles[] = $file;
            }
        }
        
        // Return priority files first, then limit regular files for this run
        return array_merge($priorityFiles, array_slice($regularFiles, 0, 30));
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
                    // Skip files that are unlikely to have inline assets
                    if (!preg_match('/\/(table-components|table_components)\//', $filePath)) {
                        $files[] = $filePath;
                    }
                }
            }
        }
        
        return $files;
    }
    
    /**
     * Generate extraction report
     */
    private function generateExtractionReport()
    {
        echo "\n" . str_repeat("=", 70) . "\n";
        echo "📦 INLINE ASSET EXTRACTION COMPLETED\n";
        echo str_repeat("=", 70) . "\n\n";
        
        echo "📊 EXTRACTION SUMMARY:\n";
        echo "- Files Processed: {$this->extractedFiles}\n";
        echo "- CSS Files Created: {$this->cssExtracted}\n";
        echo "- JS Files Created: {$this->jsExtracted}\n\n";
        
        if (!empty($this->extractions)) {
            echo "📦 EXTRACTION DETAILS:\n";
            foreach (array_slice($this->extractions, 0, 20) as $i => $extraction) {
                echo "  " . ($i + 1) . ". {$extraction}\n";
            }
            
            if (count($this->extractions) > 20) {
                echo "  ... and " . (count($this->extractions) - 20) . " more extractions\n";
            }
            echo "\n";
        }
        
        echo "📁 ASSET STRUCTURE CREATED:\n";
        echo "✅ resources/css/components/ - Component-specific styles\n";
        echo "✅ resources/js/components/ - Component-specific scripts\n";
        echo "✅ @vite() directives added to blade templates\n";
        echo "✅ @push('styles') and @push('scripts') sections added\n\n";
        
        echo "🔄 NEXT ASSET STEPS:\n";
        echo "1. Update main app.css to import component styles\n";
        echo "2. Update main app.js to import component scripts\n";
        echo "3. Add @stack('styles') and @stack('scripts') to layout\n";
        echo "4. Run npm run build to compile extracted assets\n";
        echo "5. Test components to ensure functionality preserved\n\n";
        
        echo "✅ Asset extraction complete!\n";
    }
}

// Execute asset extraction
try {
    $extractor = new InlineAssetExtractor();
    $extractor->extractAll();
    
} catch (Exception $e) {
    echo "❌ Error during asset extraction: " . $e->getMessage() . "\n";
    exit(1);
} 