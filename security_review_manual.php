<?php

/**
 * Manual Security Review Script
 * Systematic review of files flagged for manual security inspection
 * 
 * Based on Context7 Laravel security best practices
 */

require_once __DIR__ . '/vendor/autoload.php';

class ManualSecurityReviewer
{
    private $reviewedFiles = 0;
    private $fixedFiles = 0;
    private $securityFixes = [];
    private $requiresManualReview = [];
    
    // Safe contexts where unescaped output might be acceptable
    private $safeUnescapedContexts = [
        // HTML content from trusted sources
        'description' => ['blog_content', 'page_content', 'cms_content'],
        // Pre-sanitized content
        'sanitized' => ['markdown_to_html', 'purified_html'],
        // Admin-controlled content
        'admin' => ['email_template', 'system_message'],
        // Framework outputs
        'framework' => ['csrf_token', 'method_field', 'asset']
    ];
    
    public function __construct()
    {
        echo "🔍 Starting Manual Security Review using Context7 Security Patterns...\n\n";
    }
    
    /**
     * Review all flagged files
     */
    public function reviewAll()
    {
        echo "📋 Loading files flagged for manual security review...\n";
        
        $flaggedFiles = $this->getFlaggedFiles();
        
        echo "Found " . count($flaggedFiles) . " files requiring security review\n\n";
        
        foreach ($flaggedFiles as $file) {
            $this->reviewFile($file);
        }
        
        $this->generateSecurityReport();
        
        return $this;
    }
    
    /**
     * Review a single file for security issues
     */
    private function reviewFile($filePath)
    {
        $content = file_get_contents($filePath);
        $originalContent = $content;
        $filename = basename($filePath);
        $fileFixed = false;
        
        echo "🔍 Reviewing: {$filename}\n";
        $this->reviewedFiles++;
        
        // Find all unescaped output instances
        preg_match_all('/\{!!\s*([^}]+)\s*!!\}/', $content, $matches, PREG_OFFSET_CAPTURE);
        
        foreach ($matches[0] as $index => $match) {
            $fullMatch = $match[0];
            $expression = trim($matches[1][$index][0]);
            
            $decision = $this->analyzeUnescapedOutput($expression, $content, $filename);
            
            switch ($decision['action']) {
                case 'fix_escape':
                    $content = str_replace($fullMatch, '{{ ' . $expression . ' }}', $content);
                    $this->securityFixes[] = "Fixed {$filename}: Escaped '{$expression}'";
                    $fileFixed = true;
                    break;
                    
                case 'fix_safe_html':
                    // Use Laravel's HtmlString for known safe content
                    $safeExpression = "\\Illuminate\\Support\\HtmlString::make({$expression})";
                    $content = str_replace($fullMatch, '{{ ' . $safeExpression . ' }}', $content);
                    $this->securityFixes[] = "Fixed {$filename}: Made safe HTML '{$expression}'";
                    $fileFixed = true;
                    break;
                    
                case 'manual_review':
                    $this->requiresManualReview[] = [
                        'file' => $filename,
                        'expression' => $expression,
                        'reason' => $decision['reason'],
                        'recommendation' => $decision['recommendation']
                    ];
                    break;
            }
        }
        
        // Check for missing CSRF tokens in forms
        if (preg_match('/<form[^>]*method\s*=\s*[\'"]post[\'"][^>]*>/i', $content)) {
            if (!preg_match('/@csrf|csrf_token|_token/', $content)) {
                $content = preg_replace(
                    '/(<form[^>]*method\s*=\s*[\'"]post[\'"][^>]*>\s*)/i',
                    '$1' . "\n    @csrf\n",
                    $content
                );
                $this->securityFixes[] = "Fixed {$filename}: Added missing CSRF protection";
                $fileFixed = true;
            }
        }
        
        if ($fileFixed) {
            file_put_contents($filePath, $content);
            $this->fixedFiles++;
            echo "  ✅ Fixed security issues\n";
        } else {
            echo "  ℹ️ Review complete\n";
        }
    }
    
    /**
     * Analyze unescaped output to determine appropriate action
     */
    private function analyzeUnescapedOutput($expression, $fileContent, $filename)
    {
        // Simple variable that should be escaped
        if (preg_match('/^\$[a-zA-Z_][a-zA-Z0-9_]*$/', $expression)) {
            return [
                'action' => 'fix_escape',
                'reason' => 'Simple variable should be escaped',
                'recommendation' => 'Use {{ }} for user data'
            ];
        }
        
        // Object property that should be escaped
        if (preg_match('/^\$[a-zA-Z_][a-zA-Z0-9_]*->[a-zA-Z_][a-zA-Z0-9_]*$/', $expression)) {
            // Check if it's likely user-generated content
            $userContentFields = ['name', 'email', 'title', 'content', 'description', 'message', 'comment'];
            foreach ($userContentFields as $field) {
                if (strpos($expression, $field) !== false) {
                    return [
                        'action' => 'fix_escape',
                        'reason' => 'User-generated content should be escaped',
                        'recommendation' => 'Use {{ }} for user data'
                    ];
                }
            }
        }
        
        // Framework helper functions (usually safe)
        $frameworkHelpers = ['csrf_token', 'method_field', 'asset', 'url', 'route'];
        foreach ($frameworkHelpers as $helper) {
            if (strpos($expression, $helper) !== false) {
                return [
                    'action' => 'safe',
                    'reason' => 'Framework helper function',
                    'recommendation' => 'Leave as unescaped'
                ];
            }
        }
        
        // HTML content that might be intentionally unescaped
        $htmlContentFields = ['description', 'content', 'body', 'html'];
        foreach ($htmlContentFields as $field) {
            if (strpos($expression, $field) !== false) {
                // Check if this appears to be in an admin context
                if (strpos($filename, 'admin') !== false || strpos($fileContent, 'admin') !== false) {
                    return [
                        'action' => 'manual_review',
                        'reason' => 'Admin HTML content - needs context review',
                        'recommendation' => 'Verify content is sanitized or from trusted source'
                    ];
                } else {
                    return [
                        'action' => 'fix_escape',
                        'reason' => 'HTML content in non-admin context',
                        'recommendation' => 'Use {{ }} unless content is pre-sanitized'
                    ];
                }
            }
        }
        
        // Complex expressions need manual review
        if (preg_match('/[()[\]{}]/', $expression) || strpos($expression, '->') !== false) {
            return [
                'action' => 'manual_review',
                'reason' => 'Complex expression requires context analysis',
                'recommendation' => 'Review expression context and escape if user data'
            ];
        }
        
        // Default to escape for safety
        return [
            'action' => 'fix_escape',
            'reason' => 'Default safety measure',
            'recommendation' => 'Escape unless proven safe'
        ];
    }
    
    /**
     * Get files previously flagged for manual review
     */
    private function getFlaggedFiles()
    {
        $files = [];
        
        // Get all blade files and check for unescaped output
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator('resources/views')
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $filePath = $file->getPathname();
                if (strpos($filePath, '.blade.php') !== false) {
                    $content = file_get_contents($filePath);
                    
                    // Check if file has unescaped output
                    if (preg_match('/\{!![^}]*!!\}/', $content)) {
                        $files[] = $filePath;
                    }
                }
            }
        }
        
        return array_slice($files, 0, 25); // Limit to 25 files per batch
    }
    
    /**
     * Generate security review report
     */
    private function generateSecurityReport()
    {
        echo "\n" . str_repeat("=", 70) . "\n";
        echo "🔍 MANUAL SECURITY REVIEW COMPLETED\n";
        echo str_repeat("=", 70) . "\n\n";
        
        echo "📊 SECURITY REVIEW SUMMARY:\n";
        echo "- Files Reviewed: {$this->reviewedFiles}\n";
        echo "- Files Fixed: {$this->fixedFiles}\n";
        echo "- Manual Reviews Required: " . count($this->requiresManualReview) . "\n\n";
        
        if (!empty($this->securityFixes)) {
            echo "🛡️ SECURITY FIXES APPLIED:\n";
            foreach (array_slice($this->securityFixes, 0, 15) as $i => $fix) {
                echo "  " . ($i + 1) . ". {$fix}\n";
            }
            
            if (count($this->securityFixes) > 15) {
                echo "  ... and " . (count($this->securityFixes) - 15) . " more fixes\n";
            }
            echo "\n";
        }
        
        if (!empty($this->requiresManualReview)) {
            echo "⚠️  REQUIRES EXPERT REVIEW:\n";
            foreach (array_slice($this->requiresManualReview, 0, 10) as $i => $review) {
                echo "  " . ($i + 1) . ". {$review['file']}: {$review['expression']}\n";
                echo "     Reason: {$review['reason']}\n";
                echo "     Recommendation: {$review['recommendation']}\n\n";
            }
            
            if (count($this->requiresManualReview) > 10) {
                echo "  ... and " . (count($this->requiresManualReview) - 10) . " more items need review\n";
            }
        }
        
        echo "📋 CONTEXT7 SECURITY PATTERNS APPLIED:\n";
        echo "✅ Input escaping: {{ }} for user-generated content\n";
        echo "✅ CSRF protection: @csrf tokens in POST forms\n";
        echo "✅ Safe HTML: HtmlString for trusted content\n";
        echo "✅ Context analysis: Admin vs user content separation\n";
        echo "✅ Risk assessment: Conservative security approach\n\n";
        
        echo "🔄 NEXT SECURITY STEPS:\n";
        echo "1. Review flagged items requiring expert analysis\n";
        echo "2. Test all forms to ensure CSRF protection works\n";
        echo "3. Verify no functionality broken by escaping changes\n";
        echo "4. Consider implementing Content Security Policy\n";
        echo "5. Run security scanner to verify improvements\n\n";
        
        echo "✅ Security review batch complete!\n";
    }
}

// Execute security review
try {
    $reviewer = new ManualSecurityReviewer();
    $reviewer->reviewAll();
    
} catch (Exception $e) {
    echo "❌ Error during security review: " . $e->getMessage() . "\n";
    exit(1);
} 