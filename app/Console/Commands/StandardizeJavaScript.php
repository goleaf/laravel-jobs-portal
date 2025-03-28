<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class StandardizeJavaScript extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'js:standardize {--path=resources/assets/js : Path to JavaScript files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Standardize JavaScript code by removing jQuery and using modern patterns';

    /**
     * Patterns to replace in JavaScript files
     * 
     * @var array
     */
    protected $replacements = [
        // jQuery DOM ready
        '/\$\(document\)\.ready\(function\s*\(\)\s*\{/' => "document.addEventListener('DOMContentLoaded', function() {",
        '/\$\(document\)\.on\(\'turbo:load\',\s*function\s*\(\)\s*\{/' => "document.addEventListener('DOMContentLoaded', function() {",
        '/\$(window|document)\.on\(\'load\',\s*function\s*\(\)\s*\{/' => "window.addEventListener('load', function() {",
        
        // jQuery selectors
        '/\$\(([\'"])(.+?)\\1\)/' => "document.querySelector($1$2$1)",
        '/\$\(([\'"])([#.][^\'"]*)\\1\)\.html\(([^\)]+)\)/' => "document.querySelector($1$2$1).innerHTML = $3",
        '/\$\(([\'"])([#.][^\'"]*)\\1\)\.text\(([^\)]+)\)/' => "document.querySelector($1$2$1).textContent = $3",
        '/\$\(([\'"])([#.][^\'"]*)\\1\)\.val\(([^\)]+)\)/' => "document.querySelector($1$2$1).value = $3",
        '/\$\(([\'"])([#.][^\'"]*)\\1\)\.val\(\)/' => "document.querySelector($1$2$1).value",
        '/\$\(([\'"])([#.][^\'"]*)\\1\)\.attr\(([\'"])([^\'"]+)\\3,\s*([^\)]+)\)/' => "document.querySelector($1$2$1).setAttribute($3$4$3, $5)",
        '/\$\(([\'"])([#.][^\'"]*)\\1\)\.prop\(([\'"])([^\'"]+)\\3,\s*([^\)]+)\)/' => "document.querySelector($1$2$1).$4 = $5",
        '/\$\(([\'"])([#.][^\'"]*)\\1\)\.addClass\(([^\)]+)\)/' => "document.querySelector($1$2$1).classList.add($3)",
        '/\$\(([\'"])([#.][^\'"]*)\\1\)\.removeClass\(([^\)]+)\)/' => "document.querySelector($1$2$1).classList.remove($3)",
        '/\$\(([\'"])([#.][^\'"]*)\\1\)\.toggleClass\(([^\)]+)\)/' => "document.querySelector($1$2$1).classList.toggle($3)",
        '/\$\(([\'"])([#.][^\'"]*)\\1\)\.hide\(\)/' => "document.querySelector($1$2$1).style.display = 'none'",
        '/\$\(([\'"])([#.][^\'"]*)\\1\)\.show\(\)/' => "document.querySelector($1$2$1).style.display = ''",
        
        // Event handlers
        '/\$\(([\'"])([#.][^\'"]*)\\1\)\.on\(\'click\',\s*function\s*\(\s*\)\s*\{/' => "document.querySelector($1$2$1).addEventListener('click', function() {",
        '/\$\(([\'"])([#.][^\'"]*)\\1\)\.click\(function\s*\(\s*\)\s*\{/' => "document.querySelector($1$2$1).addEventListener('click', function() {",
        '/\$\(([\'"])([#.][^\'"]*)\\1\)\.on\(\'change\',\s*function\s*\(\s*\)\s*\{/' => "document.querySelector($1$2$1).addEventListener('change', function() {",
        '/\$\(([\'"])([#.][^\'"]*)\\1\)\.change\(function\s*\(\s*\)\s*\{/' => "document.querySelector($1$2$1).addEventListener('change', function() {",
        '/\$\(([\'"])([#.][^\'"]*)\\1\)\.on\(\'submit\',\s*function\s*\(\s*e\s*\)\s*\{/' => "document.querySelector($1$2$1).addEventListener('submit', function(e) {",
        '/\$\(([\'"])([#.][^\'"]*)\\1\)\.submit\(function\s*\(\s*e\s*\)\s*\{/' => "document.querySelector($1$2$1).addEventListener('submit', function(e) {",
        
        // Ajax calls
        '/\$\.ajax\(\{/' => "fetch(",
        '/type:\s*[\'"]GET[\'"]\s*,/' => "method: 'GET',",
        '/type:\s*[\'"]POST[\'"]\s*,/' => "method: 'POST',",
        '/url:\s*([^,]+)\s*,/' => "url: $1,",
        '/data:\s*([^,]+)\s*,/' => "body: JSON.stringify($1),\n    headers: {\n        'Content-Type': 'application/json',\n        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content')\n    },",
        '/success:\s*function\s*\(([^\)]*)\)\s*\{/' => ").then(response => response.json()).then($1 => {",
        
        // Misc jQuery to JS
        '/\$\.each\(([^,]+),\s*function\s*\(([^,]*),\s*([^\)]*)\)\s*\{/' => "$1.forEach(($3, $2) => {",
        '/\$.trim\(([^\)]+)\)/' => "$1.trim()",
        '/e\.preventDefault\(\);/' => "e.preventDefault();",
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = $this->option('path');
        
        $this->info("Standardizing JavaScript files in path: $path");
        
        if (!File::isDirectory($path)) {
            $this->error("The specified path does not exist or is not a directory.");
            return 1;
        }
        
        $jsFiles = $this->findJsFiles($path);
        $this->info("Found " . count($jsFiles) . " JavaScript files to process.");
        
        $modifiedFiles = 0;
        
        foreach ($jsFiles as $file) {
            $content = File::get($file);
            $originalContent = $content;
            
            // Apply all replacements
            foreach ($this->replacements as $pattern => $replacement) {
                $content = preg_replace($pattern, $replacement, $content);
            }
            
            // Add 'use strict' if not already present
            if (!Str::contains($content, "'use strict'") && !Str::contains($content, '"use strict"')) {
                $content = "'use strict';\n\n" . $content;
            }
            
            // Save file if changed
            if ($content !== $originalContent) {
                File::put($file, $content);
                $modifiedFiles++;
                $this->line("Standardized: $file");
            }
        }
        
        $this->info("Standardization complete! Modified $modifiedFiles JavaScript files.");
        
        return 0;
    }
    
    /**
     * Find all JavaScript files in the specified directory and its subdirectories.
     *
     * @param string $path
     * @return array
     */
    protected function findJsFiles($path)
    {
        return File::glob("$path/**/*.js", GLOB_BRACE);
    }
} 