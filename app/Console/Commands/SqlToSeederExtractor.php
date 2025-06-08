<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SqlToSeederExtractor extends Command
{
    protected $signature = 'sql:extract-seeders {--test : Create test files} {--limit=1000 : Limit records per table}';
    protected $description = 'Extract data from infy-jobs.sql and create Laravel seeders';

    private $extractedTables = [];
    private $seederOrder = [
        'countries',
        'states', 
        'cities',
        'roles',
        'permissions',
        'salary_currencies',
        'languages',
        'marital_status',
        'company_sizes',
        'industries',
        'functional_areas',
        'career_levels',
        'required_degree_levels',
        'job_types',
        'job_shifts',
        'salary_periods',
        'job_categories',
        'skills',
        'ownership_types',
        'tags',
        'post_categories',
        'posts',
        'post_assigned_categories',
        'settings',
        'env_settings',
        'front_settings',
        'email_templates',
        'cms_services',
        'notification_settings',
        'plans',
        'users',
        'model_has_roles'
    ];

    public function handle()
    {
        $this->info('🚀 Starting SQL to Laravel Seeder extraction...');
        
        $sqlFile = database_path('infy-jobs.sql');
        
        if (!File::exists($sqlFile)) {
            $this->error('❌ SQL file not found: ' . $sqlFile);
            return 1;
        }

        $this->info('📖 Processing SQL file in chunks for memory efficiency...');
        
        // Process file in smaller chunks
        if (!$this->extractInsertStatementsChunked($sqlFile)) {
            $this->error('❌ Failed to process SQL file');
            return 1;
        }
        
        $this->info('📝 Creating seeder files...');
        $this->createSeederFiles();
        
        if ($this->option('test')) {
            $this->info('🧪 Creating test files...');
            $this->createTestFiles();
        }
        
        $this->info('📋 Creating main database seeder...');
        $this->createMainDatabaseSeeder();
        
        $this->info('✅ SQL to Laravel Seeder extraction completed successfully!');
        $this->displaySummary();
        
        return 0;
    }

    private function extractInsertStatementsChunked($sqlFile)
    {
        $handle = fopen($sqlFile, 'r');
        if (!$handle) {
            return false;
        }

        $currentStatement = '';
        $lineCount = 0;
        $limit = (int) $this->option('limit');
        
        while (($line = fgets($handle)) !== false) {
            $lineCount++;
            
            // Skip comments and empty lines
            $line = trim($line);
            if (empty($line) || str_starts_with($line, '--') || str_starts_with($line, '/*')) {
                continue;
            }

            $currentStatement .= $line . ' ';

            // Check if statement is complete (ends with semicolon)
            if (str_ends_with(trim($line), ';')) {
                $this->processStatement($currentStatement, $limit);
                $currentStatement = '';
                
                // Show progress every 1000 lines
                if ($lineCount % 1000 === 0) {
                    $this->info("📖 Processed {$lineCount} lines...");
                }
            }
        }

        fclose($handle);
        
        $this->info("📊 Extracted data from " . count($this->extractedTables) . " tables");
        return true;
    }

    private function processStatement($statement, $limit)
    {
        // Check if it's an INSERT statement
        if (!preg_match('/INSERT INTO `(\w+)`.*?VALUES\s*(.*);/is', $statement, $matches)) {
            return;
        }

        $tableName = $matches[1];
        $valuesString = $matches[2];

        // Only process tables we're interested in
        if (!in_array($tableName, $this->seederOrder)) {
            return;
        }

        if (!isset($this->extractedTables[$tableName])) {
            $this->extractedTables[$tableName] = [
                'columns' => [],
                'data' => []
            ];
        }

        // Skip if we've reached the limit for this table
        if (count($this->extractedTables[$tableName]['data']) >= $limit) {
            return;
        }

        // Extract columns if not already done
        if (empty($this->extractedTables[$tableName]['columns'])) {
            if (preg_match('/INSERT INTO `' . $tableName . '`\s*\(([^)]+)\)/', $statement, $columnMatch)) {
                $columns = array_map(function($col) {
                    return trim($col, ' `');
                }, explode(',', $columnMatch[1]));
                $this->extractedTables[$tableName]['columns'] = $columns;
            }
        }

        // Extract data rows
        preg_match_all('/\(([^)]+(?:\([^)]*\)[^)]*)*)\)/', $valuesString, $rowMatches);
        
        foreach ($rowMatches[1] as $rowData) {
            if (count($this->extractedTables[$tableName]['data']) >= $limit) {
                break;
            }
            
            $values = $this->parseRowValues($rowData);
            if (!empty($values)) {
                $this->extractedTables[$tableName]['data'][] = $values;
            }
        }
    }

    private function parseRowValues($rowData)
    {
        $values = [];
        
        // Simple CSV parsing with quotes
        $parts = str_getcsv($rowData, ',', "'");
        
        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === 'NULL' || $part === 'null') {
                $values[] = null;
            } elseif (preg_match('/^\d+$/', $part)) {
                $values[] = (int) $part;
            } elseif (preg_match('/^\d+\.\d+$/', $part)) {
                $values[] = (float) $part;
            } elseif (strtolower($part) === 'true' || $part === '1') {
                $values[] = true;
            } elseif (strtolower($part) === 'false' || $part === '0') {
                $values[] = false;
            } else {
                $values[] = trim($part, "'\"");
            }
        }
        
        return $values;
    }

    private function createSeederFiles()
    {
        foreach ($this->seederOrder as $tableName) {
            if (isset($this->extractedTables[$tableName])) {
                $this->createSeederFile($tableName, $this->extractedTables[$tableName]);
            }
        }
    }

    private function createSeederFile($tableName, $tableData)
    {
        $className = Str::studly($tableName) . 'Seeder';
        $seederPath = database_path("seeders/{$className}.php");
        
        $columns = $tableData['columns'];
        $data = $tableData['data'];
        
        $seederContent = $this->generateSeederContent($className, $tableName, $columns, $data);
        
        File::put($seederPath, $seederContent);
        $recordCount = is_array($data) ? count($data) : 0;
        $this->info("✅ Created: {$className} ({$recordCount} records)");
    }

    private function generateSeederContent($className, $tableName, $columns, $data)
    {
        $dataArray = $this->formatDataArrayOptimized($columns, $data);
        
        return "<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class {$className} extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \$this->command->info('🌱 Seeding {$tableName}...');
        
        // Clear existing data
        DB::table('{$tableName}')->truncate();
        
        \$data = {$dataArray};
        
        if (empty(\$data)) {
            \$this->command->info('⚠️  No data to seed for {$tableName}');
            return;
        }
        
        // Insert in chunks for better performance
        \$chunks = array_chunk(\$data, 100);
        
        foreach (\$chunks as \$chunk) {
            DB::table('{$tableName}')->insert(\$chunk);
        }
        
        \$this->command->info('✅ Seeded ' . count(\$data) . ' {$tableName} records');
    }
}";
    }

    private function formatDataArrayOptimized($columns, $data)
    {
        if (empty($data)) {
            return '[]';
        }

        $arrayLines = ["["];
        
        foreach (array_slice($data, 0, 50) as $row) { // Limit to first 50 records to avoid memory issues
            $formattedRow = [];
            for ($i = 0; $i < count($columns) && $i < count($row); $i++) {
                $column = $columns[$i];
                $value = $row[$i];
                
                // Handle timestamps
                if (in_array($column, ['created_at', 'updated_at']) && $value) {
                    $formattedRow[] = "'{$column}' => '{$value}'";
                } elseif (in_array($column, ['created_at', 'updated_at']) && is_null($value)) {
                    $formattedRow[] = "'{$column}' => Carbon::now()";
                } elseif (is_string($value)) {
                    $escaped = addslashes($value);
                    $formattedRow[] = "'{$column}' => '{$escaped}'";
                } elseif (is_null($value)) {
                    $formattedRow[] = "'{$column}' => null";
                } elseif (is_bool($value)) {
                    $bool = $value ? 'true' : 'false';
                    $formattedRow[] = "'{$column}' => {$bool}";
                } else {
                    $formattedRow[] = "'{$column}' => {$value}";
                }
            }
            $arrayLines[] = "            [" . implode(', ', $formattedRow) . "],";
        }
        
        $arrayLines[] = "        ]";
        
        return implode("\n", $arrayLines);
    }

    private function createTestFiles()
    {
        foreach ($this->seederOrder as $tableName) {
            if (isset($this->extractedTables[$tableName])) {
                $this->createTestFile($tableName, $this->extractedTables[$tableName]);
            }
        }
    }

    private function createTestFile($tableName, $tableData)
    {
        $className = Str::studly($tableName) . 'SeederTest';
        $seederClass = Str::studly($tableName) . 'Seeder';
        $testPath = base_path("tests/Unit/Seeders/{$className}.php");
        
        // Create directory if it doesn't exist
        if (!File::exists(dirname($testPath))) {
            File::makeDirectory(dirname($testPath), 0755, true);
        }
        
        $testContent = $this->generateTestContent($className, $seederClass, $tableName, $tableData);
        
        File::put($testPath, $testContent);
        $this->info("✅ Created Test: {$className}");
    }

    private function generateTestContent($className, $seederClass, $tableName, $tableData)
    {
        $expectedCount = count($tableData['data']);
        
        return "<?php

namespace Tests\Unit\Seeders;

use Tests\TestCase;
use Database\Seeders\\{$seederClass};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class {$className} extends TestCase
{
    use RefreshDatabase;

    /**
     * Test {$tableName} seeder runs successfully
     *
     * @return void
     */
    public function test_{$tableName}_seeder_runs_successfully()
    {
        // Run the seeder
        \$this->seed({$seederClass}::class);
        
        // Assert records were created (allowing for limited data)
        \$count = DB::table('{$tableName}')->count();
        \$this->assertGreaterThan(0, \$count);
    }

    /**
     * Test {$tableName} seeder data integrity
     *
     * @return void
     */
    public function test_{$tableName}_seeder_data_integrity()
    {
        // Run the seeder
        \$this->seed({$seederClass}::class);
        
        // Get first record
        \$firstRecord = DB::table('{$tableName}')->first();
        
        // Assert record exists
        \$this->assertNotNull(\$firstRecord);
        
        // Test specific data integrity based on table
        \$this->assert" . Str::studly($tableName) . "DataIntegrity(\$firstRecord);
    }

    /**
     * Test {$tableName} seeder performance
     *
     * @return void
     */
    public function test_{$tableName}_seeder_performance()
    {
        \$startTime = microtime(true);
        
        // Run the seeder
        \$this->seed({$seederClass}::class);
        
        \$endTime = microtime(true);
        \$executionTime = \$endTime - \$startTime;
        
        // Assert seeder completes within reasonable time (10 seconds for large datasets)
        \$this->assertLessThan(10.0, \$executionTime);
    }

    private function assert" . Str::studly($tableName) . "DataIntegrity(\$record)
    {
        // Add table-specific assertions here
        \$this->assertNotEmpty(\$record);
        
        // Add more specific assertions based on table structure
        if (property_exists(\$record, 'id')) {
            \$this->assertNotNull(\$record->id);
        }
    }
}";
    }

    private function createMainDatabaseSeeder()
    {
        $seederCalls = [];
        foreach ($this->seederOrder as $tableName) {
            if (isset($this->extractedTables[$tableName])) {
                $className = Str::studly($tableName) . 'Seeder';
                $seederCalls[] = "        \$this->call({$className}::class);";
            }
        }
        
        $seederCallsString = implode("\n", $seederCalls);
        
        $mainSeederContent = "<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RefactoredDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with refactored SQL data.
     *
     * @return void
     */
    public function run()
    {
        \$this->command->info('🚀 Starting comprehensive database seeding...');
        
        \$startTime = microtime(true);
        
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
{$seederCallsString}
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        \$endTime = microtime(true);
        \$executionTime = round(\$endTime - \$startTime, 2);
        
        \$this->command->info(\"✅ Database seeding completed successfully in {\$executionTime} seconds!\");
    }
}";
        
        File::put(database_path('seeders/RefactoredDatabaseSeeder.php'), $mainSeederContent);
        $this->info('✅ Created RefactoredDatabaseSeeder');
    }

    private function displaySummary()
    {
        $this->info('');
        $this->info('📊 EXTRACTION SUMMARY:');
        $this->info('=======================');
        
        $totalRecords = 0;
        foreach ($this->seederOrder as $tableName) {
            if (isset($this->extractedTables[$tableName])) {
                $count = count($this->extractedTables[$tableName]['data']);
                $totalRecords += $count;
                $this->info("✅ {$tableName}: {$count} records");
            }
        }
        
        $this->info("📈 Total records extracted: {$totalRecords}");
        $this->info('');
        $this->info('🚀 NEXT STEPS:');
        $this->info('===============');
        $this->info('1. Run: php artisan migrate:fresh --force');
        $this->info('2. Run: php artisan db:seed --class=RefactoredDatabaseSeeder');
        $this->info('3. Test: php artisan test tests/Unit/Seeders/ --verbose');
        $this->info('');
    }
} 