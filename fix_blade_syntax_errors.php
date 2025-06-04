<?php

class BladeSyntaxFixer
{
    private array $filesToFix = [
        'resources/views/layouts/sub_menu.blade.php',
        'resources/views/contact.blade.php',
    ];

    public function fixAllSyntaxErrors()
    {
        echo "🔧 BLADE SYNTAX FIXER - Starting Syntax Error Fixes\n";
        echo "=" . str_repeat("=", 60) . "\n\n";

        foreach ($this->filesToFix as $file) {
            $this->fixFile($file);
        }

        echo "\n✅ All blade syntax errors have been fixed!\n";
    }

    private function fixFile($filePath)
    {
        if (!file_exists($filePath)) {
            echo "⚠️  File not found: $filePath\n";
            return;
        }

        $content = file_get_contents($filePath);
        $originalContent = $content;

        // Fix unescaped quotes in Request::is() conditions
        $content = $this->fixRequestIsQuotes($content);

        // Fix nested blade syntax
        $content = $this->fixNestedBladeSyntax($content);

        // Fix malformed class attributes
        $content = $this->fixMalformedClassAttributes($content);

        if ($content !== $originalContent) {
            file_put_contents($filePath, $content);
            echo "✅ Fixed: $filePath\n";
        } else {
            echo "✓ Clean: $filePath\n";
        }
    }

    private function fixRequestIsQuotes($content)
    {
        // Fix patterns like Request::is("admin/path*') where quotes don't match
        $content = preg_replace('/Request::is\("([^"]*)\'\)/', 'Request::is("$1")', $content);
        $content = preg_replace('/Request::is\(\'([^\']*)\"\)/', 'Request::is(\'$1\')', $content);

        // Fix patterns with multiple comma-separated paths that have mismatched quotes
        $content = preg_replace_callback('/Request::is\(([^)]+)\)/', function($matches) {
            $params = $matches[1];
            
            // Split by comma and fix each parameter
            $parts = explode(',', $params);
            $fixedParts = [];
            
            foreach ($parts as $part) {
                $part = trim($part);
                
                // If it starts and ends with different quote types, fix it
                if ((str_starts_with($part, '"') && str_ends_with($part, "'")) ||
                    (str_starts_with($part, "'") && str_ends_with($part, '"'))) {
                    $content = trim($part, "\"'");
                    $part = '"' . $content . '"';
                }
                
                $fixedParts[] = $part;
            }
            
            return 'Request::is(' . implode(',', $fixedParts) . ')';
        }, $content);

        return $content;
    }

    private function fixNestedBladeSyntax($content)
    {
        // Fix nested blade echo syntax like {{ {{ }} }}
        $content = preg_replace('/\{\{\s*\{\{\s*([^}]+)\s*\}\}\s*\}\}/', '{{ $1 }}', $content);

        // Fix @error directive with nested @ symbols
        $content = preg_replace('/@error\([^)]*@[^)]*\)/', '@error("$1")', $content);

        return $content;
    }

    private function fixMalformedClassAttributes($content)
    {
        // Fix class attributes with unescaped quotes
        $content = preg_replace('/class="([^"]*)"([^"]*)"([^"]*)"/', 'class="$1$2$3"', $content);

        // Fix class attributes that are not properly closed
        $content = preg_replace('/class="[^"]*$/', 'class=""', $content);

        return $content;
    }
}

// Run the fixer
$fixer = new BladeSyntaxFixer();
$fixer->fixAllSyntaxErrors(); 