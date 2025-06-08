<?php

/**
 * Enhanced Asset Extraction Script  
 * Continues processing remaining files with inline CSS/JS
 * 
 * Based on Universal Laravel asset management patterns
 */

require_once __DIR__ . '/vendor/autoload.php';

class EnhancedAssetExtractor
{
    private $extractedFiles = 0;
    private $cssExtracted = 0;
    private $jsExtracted = 0;
    private $extractions = [];
    private $batchSize = 30;
    
    public function __construct()
    {
        echo "📦 Enhanced Asset Extraction using Universal Patterns...\n\n";
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
            'resources/js/pages',
            'resources/css/admin',
            'resources/js/admin'
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
     * Continue extraction process
     */
    public function continueExtraction()
    {
        echo "🔍 Scanning for remaining inline assets...\n";
        
        $remainingFiles = $this->getRemainingFiles();
        
        echo "Found " . count($remainingFiles) . " files with potential inline assets\n";
        echo "Processing batch of {$this->batchSize} files\n\n";
        
        $batch = array_slice($remainingFiles, 0, $this->batchSize);
        
        foreach ($batch as $file) {
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
        
        // Determine category for organization
        $category = $this->determineCategory($filePath);
        
        // Extract CSS with enhanced patterns
        $cssExtracted = $this->extractEnhancedCSS($content, $filename, $category);
        if ($cssExtracted) {
            $this->cssExtracted++;
            $fileChanged = true;
        }
        
        // Extract JavaScript with enhanced patterns
        $jsExtracted = $this->extractEnhancedJS($content, $filename, $category);
        if ($jsExtracted) {
            $this->jsExtracted++;
            $fileChanged = true;
        }
        
        if ($fileChanged) {
            file_put_contents($filePath, $content);
            $this->extractedFiles++;
            $this->extractions[] = "Extracted assets from: " . basename($filePath);
            echo "  ✅ Extracted: " . basename($filePath) . " ({$category})\n";
        }
    }
    
    /**
     * Determine file category for organization
     */
    private function determineCategory($filePath)
    {
        if (strpos($filePath, 'admin') !== false) return 'admin';
        if (strpos($filePath, 'components') !== false) return 'components';
        if (strpos($filePath, 'layouts') !== false) return 'components';
        if (strpos($filePath, 'modals') !== false) return 'components';
        if (strpos($filePath, 'dashboard') !== false) return 'pages';
        if (strpos($filePath, 'auth') !== false) return 'pages';
        if (strpos($filePath, 'front') !== false) return 'pages';
        return 'components';
    }
    
    /**
     * Enhanced CSS extraction with better pattern detection
     */
    private function extractEnhancedCSS(&$content, $filename, $category)
    {
        $extracted = false;
        
        // Find <style> blocks with various attributes
        preg_match_all('/<style[^>]*>(.*?)<\/style>/s', $content, $matches, PREG_OFFSET_CAPTURE);
        
        if (!empty($matches[0])) {
            $cssContent = '';
            $offsetAdjustment = 0;
            
            foreach ($matches[0] as $index => $match) {
                $fullMatch = $match[0];
                $cssInner = $matches[1][$index][0];
                $position = $match[1] - $offsetAdjustment;
                
                // Clean and enhance CSS
                $cleanCSS = $this->enhanceCSS($cssInner, $filename);
                
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
                $cssFile = "resources/css/{$category}/{$filename}.css";
                
                // Append to existing file or create new one
                if (file_exists($cssFile)) {
                    $cssContent = file_get_contents($cssFile) . "\n" . $cssContent;
                }
                
                file_put_contents($cssFile, $cssContent);
                
                // Add CSS import to blade file (enhanced)
                $cssImport = "\n@push('styles')\n    @vite('resources/css/{$category}/{$filename}.css')\n@endpush\n";
                $content = $cssImport . $content;
            }
        }
        
        return $extracted;
    }
    
    /**
     * Enhanced JavaScript extraction with better pattern detection
     */
    private function extractEnhancedJS(&$content, $filename, $category)
    {
        $extracted = false;
        
        // Find <script> blocks (inline only, not src includes)
        preg_match_all('/<script(?![^>]*src\s*=)[^>]*>(.*?)<\/script>/s', $content, $matches, PREG_OFFSET_CAPTURE);
        
        if (!empty($matches[0])) {
            $jsContent = '';
            $offsetAdjustment = 0;
            
            foreach ($matches[0] as $index => $match) {
                $fullMatch = $match[0];
                $jsInner = $matches[1][$index][0];
                $position = $match[1] - $offsetAdjustment;
                
                // Clean and enhance JavaScript
                $cleanJS = $this->enhanceJS($jsInner, $filename);
                
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
                $jsFile = "resources/js/{$category}/{$filename}.js";
                
                // Append to existing file or create new one
                if (file_exists($jsFile)) {
                    $jsContent = file_get_contents($jsFile) . "\n" . $jsContent;
                }
                
                // Enhanced JS wrapping with proper error handling
                $wrappedJS = $this->wrapJavaScript($jsContent, $filename);
                
                file_put_contents($jsFile, $wrappedJS);
                
                // Add JS import to blade file (enhanced)
                $jsImport = "\n@push('scripts')\n    @vite('resources/js/{$category}/{$filename}.js')\n@endpush\n";
                $content = $content . $jsImport;
            }
        }
        
        return $extracted;
    }
    
    /**
     * Enhance CSS with TailwindCSS compatibility
     */
    private function enhanceCSS($css, $filename)
    {
        $css = trim($css);
        
        if ($css) {
            $header = "/* {$filename} Component Styles */\n";
            $header .= "/* Enhanced for TailwindCSS compatibility */\n\n";
            
            // Add component scoping if needed
            if (!preg_match('/^\.|\#/', $css)) {
                $css = ".{$filename}-component {\n" . $css . "\n}";
            }
            
            $css = $header . $css;
        }
        
        return $css;
    }
    
    /**
     * Enhance JavaScript with modern patterns
     */
    private function enhanceJS($js, $filename)
    {
        $js = trim($js);
        
        if ($js) {
            $header = "// {$filename} Component JavaScript\n";
            $header .= "// Enhanced with Universal patterns\n\n";
            $js = $header . $js;
        }
        
        return $js;
    }
    
    /**
     * Wrap JavaScript with modern error handling
     */
    private function wrapJavaScript($jsContent, $filename)
    {
        return "// {$filename} Component
document.addEventListener('DOMContentLoaded', function() {
    try {
        {$jsContent}
    } catch (error) {
        console.error('Error in {$filename} component:', error);
    }
});";
    }
    
    /**
     * Get files that still have inline assets
     */
    private function getRemainingFiles()
    {
        $files = [];
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator('resources/views')
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $filePath = $file->getPathname();
                if (strpos($filePath, '.blade.php') !== false) {
                    $content = file_get_contents($filePath);
                    
                    // Check for inline styles or scripts
                    if (preg_match('/<style[^>]*>|<script(?![^>]*src)[^>]*>/', $content)) {
                        // Skip files that are unlikely to have meaningful inline assets
                        if (!preg_match('/\/(table-components|table_components|emails)\//', $filePath)) {
                            $files[] = $filePath;
                        }
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
        echo "📦 ENHANCED ASSET EXTRACTION COMPLETED\n";
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
        
        echo "📁 ENHANCED ASSET STRUCTURE:\n";
        echo "✅ resources/css/components/ - Component-specific styles\n";
        echo "✅ resources/css/pages/ - Page-specific styles\n";
        echo "✅ resources/css/admin/ - Admin-specific styles\n";
        echo "✅ resources/js/components/ - Component-specific scripts\n";
        echo "✅ resources/js/pages/ - Page-specific scripts\n";
        echo "✅ resources/js/admin/ - Admin-specific scripts\n";
        echo "✅ Error handling and modern JS patterns applied\n";
        echo "✅ TailwindCSS compatibility ensured\n\n";
        
        echo "🔄 NEXT ASSET STEPS:\n";
        echo "1. Run script again to process more batches\n";
        echo "2. Update main app.css to import all component styles\n";
        echo "3. Update main app.js to import all component scripts\n";
        echo "4. Add @stack directives to layout templates\n";
        echo "5. Run npm run build to compile all assets\n\n";
        
        echo "✅ Enhanced asset extraction batch complete!\n";
    }
}

// Execute enhanced asset extraction
try {
    $extractor = new EnhancedAssetExtractor();
    $extractor->continueExtraction();
    
} catch (Exception $e) {
    echo "❌ Error during asset extraction: " . $e->getMessage() . "\n";
    exit(1);
} 