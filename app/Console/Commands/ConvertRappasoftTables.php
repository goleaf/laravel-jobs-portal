<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ConvertRappasoftTables extends Command
{
    protected $signature = 'tables:convert';
    protected $description = 'Convert Rappasoft data tables to custom implementation';

    protected $baseNamespace = 'App\\Livewire\\';
    protected $livewirePath = 'app/Livewire/';

    public function handle()
    {
        $this->info('Starting conversion of Rappasoft tables...');

        $files = File::glob($this->livewirePath . '*Table.php');
        $this->info('Found ' . count($files) . ' table files to convert.');

        foreach ($files as $file) {
            $this->convertTable($file);
        }

        $this->info('All tables converted successfully!');
        $this->info('Please check each table file to ensure proper conversion.');
        $this->info('You can now remove Rappasoft package from composer.json');
        
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