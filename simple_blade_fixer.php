<?php

class SimpleBladeFixer
{
    private $fixedFiles = [];
    private $errors = [];

    public function fixCriticalBladeErrors()
    {
        echo "🔧 Starting critical blade error fixing...\n\n";
        
        $this->fixSpecificFiles();
        $this->generateReport();
    }

    private function fixSpecificFiles()
    {
        // Fix the most critical files identified in the analysis
        $criticalFiles = [
            'candidates/table-components/name_email.blade.php',
            'candidates/table-components/action_button.blade.php',
            'candidates/table-components/email_verified.blade.php',
            'candidates/table-components/available.blade.php',
            'required_degree_levels/table-components/action_button.blade.php'
        ];

        foreach ($criticalFiles as $file) {
            $this->fixSingleFile($file);
        }
    }

    private function fixSingleFile($relativePath)
    {
        $filePath = __DIR__ . '/resources/views/' . $relativePath;
        
        if (!file_exists($filePath)) {
            echo "⚠️  File not found: $relativePath\n";
            return;
        }

        $content = file_get_contents($filePath);
        $originalContent = $content;

        // Apply fixes
        $content = $this->fixMultilineRoutes($content);
        $content = $this->fixBladeDirectives($content);
        $content = $this->fixQuoteIssues($content);

        // Save if changed
        if ($content !== $originalContent) {
            file_put_contents($filePath, $content);
            $this->fixedFiles[] = $relativePath;
            echo "✅ Fixed: $relativePath\n";
        } else {
            echo "ℹ️  No changes needed: $relativePath\n";
        }
    }

    private function fixMultilineRoutes($content)
    {
        // Fix route calls that are split across lines
        $content = preg_replace(
            '/href="\{\{\s*route\(\s*\'([^\']+)\',\s*\n\s*\$([^)]+)\)\s*\}\}"/m',
            'href="{{ route(\'$1\', $2) }}"',
            $content
        );

        return $content;
    }

    private function fixBladeDirectives($content)
    {
        // Fix @if statements with space issues
        $content = preg_replace('/@if\s*\(\s*!\s*\$([^)]+)\s*\)/', '@if(!$1)', $content);
        $content = preg_replace('/@if\s*\(\s*\$([^)]+)\s*==\s*([^)]+)\s*\)/', '@if($1 == $2)', $content);

        return $content;
    }

    private function fixQuoteIssues($content)
    {
        // Fix common quote issues in href attributes
        $content = preg_replace('/href="\{\{route\(/', 'href="{{ route(', $content);
        $content = preg_replace('/\)\}\}"/', ') }}"', $content);

        return $content;
    }

    private function generateReport()
    {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "📊 SIMPLE BLADE FIX REPORT\n";
        echo str_repeat("=", 60) . "\n\n";
        echo "✅ Fixed Files: " . count($this->fixedFiles) . "\n";
        echo "❌ Errors: " . count($this->errors) . "\n\n";

        if (!empty($this->fixedFiles)) {
            echo "📁 FIXED FILES:\n";
            foreach ($this->fixedFiles as $file) {
                echo "• $file\n";
            }
        }
        echo str_repeat("=", 60) . "\n";
    }
}

// Run the fixes
try {
    $fixer = new SimpleBladeFixer();
    $fixer->fixCriticalBladeErrors();
    echo "\n✅ Critical blade error fixing completed!\n";
} catch (Exception $e) {
    echo "\n❌ Error during fixing: " . $e->getMessage() . "\n";
} 