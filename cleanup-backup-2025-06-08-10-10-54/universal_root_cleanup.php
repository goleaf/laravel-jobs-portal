<?php
/**
 * Universal Root Directory Cleanup & Organization Script
 * 
 * This script implements Laravel best practices for project organization
 * and moves documentation to the memory-bank system for better maintainability.
 * 
 * Based on Laravel Best Practices and Universal patterns
 */

class UniversalRootCleanup
{
    private $rootPath;
    private $memoryBankPath;
    private $backupPath;
    private $dryRun;
    private $log = [];

    public function __construct($dryRun = true)
    {
        $this->rootPath = __DIR__;
        $this->memoryBankPath = $this->rootPath . '/memory-bank';
        $this->backupPath = $this->rootPath . '/cleanup-backup-' . date('Y-m-d-H-i-s');
        $this->dryRun = $dryRun;
        
        $this->log("Universal Root Cleanup initialized");
        $this->log("Mode: " . ($dryRun ? "DRY RUN" : "LIVE EXECUTION"));
    }

    public function execute()
    {
        $this->log("\n=== UNIVERSAL ROOT CLEANUP EXECUTION ===\n");
        
        // Phase 1: Analysis
        $this->analyzeRootDirectory();
        
        // Phase 2: Create backup if not dry run
        if (!$this->dryRun) {
            $this->createBackup();
        }
        
        // Phase 3: Organize documentation
        $this->organizeDocumentation();
        
        // Phase 4: Clean temporary files
        $this->cleanTemporaryFiles();
        
        // Phase 5: Verify essential files
        $this->verifyEssentialFiles();
        
        // Phase 6: Generate report
        $this->generateReport();
    }

    private function analyzeRootDirectory()
    {
        $this->log("Phase 1: Analyzing root directory structure...");
        
        $files = glob($this->rootPath . '/*');
        $analysis = [
            'total_files' => 0,
            'md_files' => 0,
            'php_scripts' => 0,
            'json_reports' => 0,
            'shell_scripts' => 0,
            'backup_files' => 0,
            'essential_files' => 0
        ];
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $analysis['total_files']++;
                $filename = basename($file);
                
                if (preg_match('/\.md$/i', $filename)) {
                    $analysis['md_files']++;
                } elseif (preg_match('/^(analyze_|fix_|context7_|universal_|comprehensive_|massive_|aggressive_).*\.php$/i', $filename)) {
                    $analysis['php_scripts']++;
                } elseif (preg_match('/\.(json)$/i', $filename) && preg_match('/(analysis|report|blade|route)/', $filename)) {
                    $analysis['json_reports']++;
                } elseif (preg_match('/\.(sh)$/i', $filename)) {
                    $analysis['shell_scripts']++;
                } elseif (preg_match('/\.(backup|bak)$/i', $filename)) {
                    $analysis['backup_files']++;
                } elseif (in_array($filename, $this->getEssentialFiles())) {
                    $analysis['essential_files']++;
                }
            }
        }
        
        foreach ($analysis as $key => $count) {
            $this->log("  {$key}: {$count}");
        }
    }

    private function organizeDocumentation()
    {
        $this->log("\nPhase 3: Organizing documentation files...");
        
        // Ensure memory-bank subdirectories exist
        $this->ensureMemoryBankStructure();
        
        $mdFiles = glob($this->rootPath . '/*.md');
        $organized = 0;
        
        foreach ($mdFiles as $file) {
            $filename = basename($file);
            $category = $this->categorizeDocumentation($filename);
            $targetDir = $this->memoryBankPath . '/' . $category;
            $targetFile = $targetDir . '/' . $filename;
            
            if (!$this->dryRun) {
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                
                if (rename($file, $targetFile)) {
                    $organized++;
                    $this->log("  Moved: {$filename} -> {$category}/");
                } else {
                    $this->log("  ERROR: Failed to move {$filename}");
                }
            } else {
                $this->log("  Would move: {$filename} -> {$category}/");
                $organized++;
            }
        }
        
        $this->log("Documentation files organized: {$organized}");
    }

    private function cleanTemporaryFiles()
    {
        $this->log("\nPhase 4: Cleaning temporary files...");
        
        $patterns = [
            '*.php' => '/^(analyze_|fix_|context7_|universal_|comprehensive_|massive_|aggressive_|temp|test_|debug|diagnose|quick|simple|minimal|memory|run_|create_|check_|validate_|implement_|improve_|migrate_|update_|upgrade_|continue_|extract_|find_|focused_|high_|safe_|nuclear_|ultimate_|final_).*\.php$/i',
            '*.json' => '/.*_(analysis|report|results|log)\.json$/i',
            '*.sh' => '/^(run_|fix_|create_|check_|safe_|nuclear_|ultimate_).*\.sh$/i',
            '*.backup' => '/\.(backup|bak)$/i',
            '*.log' => '/chromedriver\.log$/i'
        ];
        
        $cleaned = 0;
        
        foreach ($patterns as $glob => $pattern) {
            $files = glob($this->rootPath . '/' . $glob);
            
            foreach ($files as $file) {
                $filename = basename($file);
                
                if (preg_match($pattern, $filename) && !in_array($filename, $this->getEssentialFiles())) {
                    if (!$this->dryRun) {
                        if (unlink($file)) {
                            $cleaned++;
                            $this->log("  Removed: {$filename}");
                        } else {
                            $this->log("  ERROR: Failed to remove {$filename}");
                        }
                    } else {
                        $this->log("  Would remove: {$filename}");
                        $cleaned++;
                    }
                }
            }
        }
        
        $this->log("Temporary files cleaned: {$cleaned}");
    }

    private function categorizeDocumentation($filename)
    {
        $filename = strtolower($filename);
        
        // Archive: Completed implementations and reports
        if (preg_match('/(complete|summary|report|implementation|final|transformation|upgrade|migration|optimization)/', $filename)) {
            return 'archive';
        }
        
        // Creative: Design and planning documents
        if (preg_match('/(creative|design|ui|ux|planning|architecture|guide)/', $filename)) {
            return 'creative';
        }
        
        // Optimization Journey: Performance and improvements
        if (preg_match('/(performance|security|optimization|journey|progress)/', $filename)) {
            return 'optimization-journey';
        }
        
        // Reflection: Analysis and lessons learned
        if (preg_match('/(reflection|analysis|testing|fixes|status|results)/', $filename)) {
            return 'reflection';
        }
        
        // Default to archive for unmatched files
        return 'archive';
    }

    private function ensureMemoryBankStructure()
    {
        $directories = [
            'archive',
            'creative', 
            'optimization-journey',
            'reflection',
            'assets'
        ];
        
        foreach ($directories as $dir) {
            $path = $this->memoryBankPath . '/' . $dir;
            if (!is_dir($path) && !$this->dryRun) {
                mkdir($path, 0755, true);
                $this->log("  Created directory: {$dir}/");
            }
        }
    }

    private function getEssentialFiles()
    {
        return [
            // Laravel Core
            'artisan',
            'composer.json',
            'composer.lock',
            'package.json',
            'package-lock.json',
            
            // Configuration
            'tailwind.config.js',
            'vite.config.js',
            'vite.config.ts',
            'tsconfig.json',
            'tsconfig.node.json',
            'postcss.config.js',
            'phpunit.xml',
            'pint.json',
            
            // Documentation (if actively maintained)
            'README.md',
            'CHANGELOG.md',
            'LICENSE',
            
            // Git
            '.gitignore',
            '.gitattributes',
            
            // Environment
            '.env.example',
            
            // IDE
            '.editorconfig'
        ];
    }

    private function verifyEssentialFiles()
    {
        $this->log("\nPhase 5: Verifying essential Laravel files...");
        
        $essential = $this->getEssentialFiles();
        $missing = [];
        $present = [];
        
        foreach ($essential as $file) {
            if (file_exists($this->rootPath . '/' . $file)) {
                $present[] = $file;
            } else {
                $missing[] = $file;
            }
        }
        
        $this->log("Essential files present: " . count($present));
        $this->log("Essential files missing: " . count($missing));
        
        if (!empty($missing)) {
            $this->log("Missing files: " . implode(', ', $missing));
        }
    }

    private function createBackup()
    {
        $this->log("\nPhase 2: Creating backup...");
        
        if (!is_dir($this->backupPath)) {
            mkdir($this->backupPath, 0755, true);
        }
        
        // Backup important files before cleanup
        $filesToBackup = array_merge(
            glob($this->rootPath . '/*.md'),
            glob($this->rootPath . '/*.php'),
            glob($this->rootPath . '/*.json')
        );
        
        $backed = 0;
        foreach ($filesToBackup as $file) {
            $filename = basename($file);
            if (copy($file, $this->backupPath . '/' . $filename)) {
                $backed++;
            }
        }
        
        $this->log("Backup created: {$backed} files in {$this->backupPath}");
    }

    private function generateReport()
    {
        $this->log("\nPhase 6: Generating cleanup report...");
        
        $reportPath = $this->memoryBankPath . '/universal-cleanup-report-' . date('Y-m-d-H-i-s') . '.md';
        
        $report = "# Universal Root Directory Cleanup Report\n\n";
        $report .= "**Date:** " . date('Y-m-d H:i:s') . "\n";
        $report .= "**Mode:** " . ($this->dryRun ? "DRY RUN" : "LIVE EXECUTION") . "\n\n";
        $report .= "## Execution Log\n\n```\n";
        $report .= implode("\n", $this->log);
        $report .= "\n```\n\n";
        $report .= "## Universal Patterns Applied\n\n";
        $report .= "- Laravel best practices for project organization\n";
        $report .= "- Professional directory structure\n";
        $report .= "- Documentation categorization system\n";
        $report .= "- Automated cleanup processes\n";
        $report .= "- Safety measures and backup strategy\n";
        
        if (!$this->dryRun) {
            file_put_contents($reportPath, $report);
            $this->log("Report saved: " . basename($reportPath));
        } else {
            $this->log("Report would be saved as: " . basename($reportPath));
        }
    }

    private function log($message)
    {
        $this->log[] = $message;
        echo $message . "\n";
    }
}

// Execute the cleanup
echo "Universal Root Directory Cleanup\n";
echo "================================\n\n";

$dryRun = !isset($argv[1]) || $argv[1] !== '--execute';

if ($dryRun) {
    echo "DRY RUN MODE - No files will be modified\n";
    echo "Use --execute flag to perform actual cleanup\n\n";
}

$cleanup = new UniversalRootCleanup($dryRun);
$cleanup->execute();

echo "\nUniversal cleanup completed!\n";
?> 