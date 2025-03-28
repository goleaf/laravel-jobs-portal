<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ConvertRappasoftTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tables:convert {--path=app : Path to search for Rappasoft table files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert Rappasoft datatable files to custom BaseTable';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = $this->option('path');
        $this->info("Searching for Rappasoft datatable files in: $path");
        
        // Search for all PHP files that extend LivewireDatatables
        $phpFiles = File::glob("{$path}/**/*.php");
        $count = 0;
        
        foreach ($phpFiles as $file) {
            $content = File::get($file);
            
            // Check if file is a Rappasoft livewire datatable
            if (strpos($content, 'Rappasoft\LaravelLivewireTables') !== false) {
                $this->info("Found Rappasoft datatable: $file");
                
                // Replace the namespace and base class
                $content = preg_replace(
                    '/use Rappasoft\\\\LaravelLivewireTables\\\\.*?;/m',
                    'use App\Http\Livewire\BaseTable;',
                    $content
                );
                
                // Replace class extension
                $content = preg_replace(
                    '/extends .*?Component/m', 
                    'extends BaseTable',
                    $content
                );
                
                // Remove Rappasoft specific traits and methods
                $content = preg_replace(
                    '/use .*?WithSearch.*?;/m', 
                    '',
                    $content
                );
                
                $content = preg_replace(
                    '/use .*?WithPagination.*?;/m', 
                    '',
                    $content
                );
                
                $content = preg_replace(
                    '/use .*?WithSorting.*?;/m', 
                    '',
                    $content
                );
                
                // Update methods to match our BaseTable
                $content = preg_replace(
                    '/public function columns\(\).*?return \[/s', 
                    'public function getColumns(): array
    {
        return [',
                    $content
                );
                
                // Update query method
                $content = preg_replace(
                    '/public function query\(\).*?{/s', 
                    'public function getQuery(): \Illuminate\Database\Eloquent\Builder
    {',
                    $content
                );
                
                // Save the updated file
                File::put($file, $content);
                $this->info("Updated file: $file");
                $count++;
            }
        }
        
        $this->info("Converted $count Rappasoft datatable files to BaseTable");
        
        return Command::SUCCESS;
    }

    protected function convertTable($filePath)
    {
        $fileName = basename($filePath);
        $className = Str::replaceLast('.php', '', $fileName);
        
        $this->info("Converting {$className}...");
        
        $content = File::get($filePath);
        
        // Skip already converted files
        if (Str::contains($content, 'extends BaseTable')) {
            $this->warn("File {$className} already converted. Skipping...");
            return;
        }
        
        // Extract model from namespace import or from $model property
        preg_match('/use\s+App\\\\Models\\\\([^;]+);/', $content, $modelMatches);
        $modelName = $modelMatches[1] ?? null;
        
        if (!$modelName) {
            preg_match('/protected\s+\$model\s*=\s*([^:]+)::class/', $content, $propertyMatches);
            if (isset($propertyMatches[1])) {
                $parts = explode('\\', $propertyMatches[1]);
                $modelName = end($parts);
            }
        }
        
        if (!$modelName) {
            $this->error("Could not detect model for {$className}. Please convert manually.");
            return;
        }
        
        // Extract UI properties
        preg_match('/public\s+\$showButtonOnHeader\s*=\s*(true|false)/', $content, $buttonHeaderMatches);
        $showButtonOnHeader = $buttonHeaderMatches[1] ?? 'false';
        
        preg_match('/public\s+\$showFilterOnHeader\s*=\s*(true|false)/', $content, $filterHeaderMatches);
        $showFilterOnHeader = $filterHeaderMatches[1] ?? 'false';
        
        preg_match('/public\s+\$buttonComponent\s*=\s*[\'"]([^\'"]+)[\'"]/', $content, $buttonComponentMatches);
        $buttonComponent = $buttonComponentMatches[1] ?? null;
        
        // Extract columns
        preg_match('/public\s+function\s+columns\(\).*?\{(.*?)\}/s', $content, $columnMatches);
        
        $columns = [];
        if (isset($columnMatches[1])) {
            $columnContent = $columnMatches[1];
            preg_match_all('/Column::make\([\'"]([^\'"]+)[\'"](?:,\s*[\'"]?([^\'"]+)[\'"]?)?\)(?:->(.*?))?(?:,|$)/s', $columnContent, $columnDefinitions, PREG_SET_ORDER);
            
            foreach ($columnDefinitions as $definition) {
                $label = $definition[1] ?? '';
                $field = $definition[2] ?? '';
                $methods = $definition[3] ?? '';
                
                $column = [
                    'label' => $label,
                    'field' => $field,
                ];
                
                if (Str::contains($methods, 'sortable()')) {
                    $column['sortable'] = true;
                }
                
                if (Str::contains($methods, 'searchable()')) {
                    $column['searchable'] = true;
                }
                
                if (preg_match('/view\([\'"]([^\'"]+)[\'"]/', $methods, $viewMatch)) {
                    $column['view'] = $viewMatch[1];
                }
                
                $columns[] = $column;
            }
        }
        
        // Create new file content
        $newContent = $this->generateNewTableContent(
            $className,
            $modelName,
            $showButtonOnHeader,
            $showFilterOnHeader,
            $buttonComponent,
            $columns
        );
        
        // Write the new content
        File::put($filePath, $newContent);
        
        $this->info("Successfully converted {$className}");
    }
    
    protected function generateNewTableContent($className, $modelName, $showButtonOnHeader, $showFilterOnHeader, $buttonComponent, $columns)
    {
        $columnDefinitions = [];
        
        foreach ($columns as $column) {
            $columnDefinitions[] = $this->formatColumn($column);
        }
        
        $columnDefinitionsStr = implode(",\n            ", $columnDefinitions);
        
        $buttonComponentLine = $buttonComponent 
            ? "public \$buttonComponent = '{$buttonComponent}';" 
            : '';
        
        return <<<PHP
<?php

namespace App\Livewire;

use App\Models\\{$modelName};
use Illuminate\Database\Eloquent\Builder;

class {$className} extends BaseTable
{
    // UI Options
    public \$showButtonOnHeader = {$showButtonOnHeader};
    public \$showFilterOnHeader = {$showFilterOnHeader};
    {$buttonComponentLine}
    
    public function query(): Builder
    {
        return {$modelName}::query();
    }

    public function columns(): array
    {
        return [
            {$columnDefinitionsStr}
        ];
    }

    // Initialize sorting
    public function mount()
    {
        parent::mount();
        \$this->sortField = 'created_at';
        \$this->sortDirection = 'desc';
    }

    // Custom table classes
    public function getTableClass(): string
    {
        return 'table table-striped';
    }
}
PHP;
    }
    
    protected function formatColumn($column)
    {
        $result = "[";
        $properties = [];
        
        foreach ($column as $key => $value) {
            if (is_bool($value)) {
                $properties[] = "'{$key}' => " . ($value ? 'true' : 'false');
            } else {
                $properties[] = "'{$key}' => '{$value}'";
            }
        }
        
        $result .= implode(", ", $properties);
        $result .= "]";
        
        return $result;
    }
} 