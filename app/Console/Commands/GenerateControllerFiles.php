<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateControllerFiles extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'generate:controller-files 
                           {--controller= : Specific controller}
                           {--type= : Type (request|response|test|all)}
                           {--dry-run : Preview only}';

    /**
     * The console command description.
     */
    protected $description = 'Generate request, response, and test files for controller methods';

    /**
     * Generated files counter.
     */
    protected $stats = [
        'controllers' => 0,
        'methods' => 0,
        'requests' => 0,
        'responses' => 0,
        'tests' => 0,
        'skipped' => 0,
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Generating HTTP Controller Files...');

        $controller = $this->option('controller');
        $type = $this->option('type') ?? 'all';
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE');
        }

        $directories = [
            'app/Http/Controllers',
            'app/Http/Controllers/Admin',
            'app/Http/Controllers/Api/Universal',
            'app/Http/Controllers/Api/V1',
        ];

        foreach ($directories as $dir) {
            if (! File::exists(base_path($dir))) {
                continue;
            }

            $this->info("📁 Processing: {$dir}");
            $controllers = File::glob(base_path($dir).'/*.php');

            foreach ($controllers as $path) {
                $this->processController($path, $type, $dryRun);
            }
        }

        $this->displayStats();
    }

    /**
     * Process a single controller file.
     *
     * @param  mixed  $path
     * @param  mixed  $type
     * @param  mixed  $dryRun
     */
    protected function processController($path, $type, $dryRun)
    {
        $name = basename($path, '.php');

        if (in_array($name, ['Controller', 'UniversalBaseController'])) {
            return;
        }

        try {
            $namespace = $this->getNamespace($path);
            $class = $namespace.'\\'.$name;

            if (! class_exists($class)) {
                return;
            }

            $reflection = new \ReflectionClass($class);
            $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

            $publicMethods = [];
            foreach ($methods as $method) {
                if ($method->getDeclaringClass()->getName() === $class) {
                    $publicMethods[] = $method->getName();
                }
            }

            if (empty($publicMethods)) {
                return;
            }

            $this->line("  🔍 {$name}: ".implode(', ', $publicMethods));

            foreach ($publicMethods as $methodName) {
                $this->generateFiles($path, $name, $methodName, $type, $dryRun);
            }

            $this->stats['controllers']++;
            $this->stats['methods'] += count($publicMethods);
        } catch (\Exception $e) {
            $this->error("❌ Error: {$name} - ".$e->getMessage());
        }
    }

    protected function generateFiles($controllerPath, $controllerName, $methodName, $type, $dryRun)
    {
        $dir = dirname($controllerPath);
        $relative = str_replace(base_path('app/Http/Controllers'), '', $dir);
        $namespace = str_replace('/', '\\', $relative);

        if ($type === 'all' || $type === 'request') {
            $this->generateRequest($namespace, $controllerName, $methodName, $dryRun);
        }

        if ($type === 'all' || $type === 'response') {
            $this->generateResponse($namespace, $controllerName, $methodName, $dryRun);
        }

        if ($type === 'all' || $type === 'test') {
            $this->generateTest($namespace, $controllerName, $dryRun);
        }
    }

    /**
     * Generate request file.
     *
     * @param  mixed  $namespace
     * @param  mixed  $controller
     * @param  mixed  $method
     * @param  mixed  $dryRun
     */
    protected function generateRequest($namespace, $controller, $method, $dryRun)
    {
        $name = ucfirst($method).'Request';
        $ns = 'App\Http\Requests'.$namespace;
        $path = base_path('app/Http/Requests'.$namespace.'/'.$name.'.php');

        if (File::exists($path)) {
            $this->stats['skipped']++;

            return;
        }

        $template = $this->requestTemplate($ns, $name, $method);

        if (! $dryRun) {
            File::ensureDirectoryExists(dirname($path));
            File::put($path, $template);
        }

        $this->line("      ✅ Request: {$name}");
        $this->stats['requests']++;
    }

    /**
     * Generate response file.
     *
     * @param  mixed  $namespace
     * @param  mixed  $controller
     * @param  mixed  $method
     * @param  mixed  $dryRun
     */
    protected function generateResponse($namespace, $controller, $method, $dryRun)
    {
        $name = ucfirst($method).'Resource';
        $ns = 'App\Http\Resources'.$namespace;
        $path = base_path('app/Http/Resources'.$namespace.'/'.$name.'.php');

        if (File::exists($path)) {
            $this->stats['skipped']++;

            return;
        }

        $template = $this->responseTemplate($ns, $name, $method);

        if (! $dryRun) {
            File::ensureDirectoryExists(dirname($path));
            File::put($path, $template);
        }

        $this->line("      ✅ Response: {$name}");
        $this->stats['responses']++;
    }

    /**
     * Generate test file (one per controller).
     *
     * @param  mixed  $namespace
     * @param  mixed  $controller
     * @param  mixed  $dryRun
     */
    protected function generateTest($namespace, $controller, $dryRun)
    {
        $name = $controller.'Test';
        $ns = 'Tests\Feature'.$namespace;
        $path = base_path('tests/Feature'.$namespace.'/'.$name.'.php');

        if (File::exists($path)) {
            return;
        }

        $template = $this->testTemplate($ns, $name, $controller);

        if (! $dryRun) {
            File::ensureDirectoryExists(dirname($path));
            File::put($path, $template);
        }

        $this->line("      ✅ Test: {$name}");
        $this->stats['tests']++;
    }

    /**
     * Get request file template.
     *
     * @param  mixed  $namespace
     * @param  mixed  $className
     * @param  mixed  $method
     */
    protected function requestTemplate($namespace, $className, $method)
    {
        return "<?php

namespace {$namespace};

use Illuminate\\Foundation\\Http\\FormRequest;

class {$className} extends FormRequest
{
    public function authorize(): bool
    {
        return true; // TODO: Implement authorization
    }

    public function rules(): array
    {
        return [
            // TODO: Add validation rules for {$method}
        ];
    }

    public function messages(): array
    {
        return [
            // TODO: Add custom messages
        ];
    }
}
";
    }

    /**
     * Get response file template.
     *
     * @param  mixed  $namespace
     * @param  mixed  $className
     * @param  mixed  $method
     */
    protected function responseTemplate($namespace, $className, $method)
    {
        return "<?php

namespace {$namespace};

use Illuminate\\Http\\Resources\\Json\\JsonResource;

class {$className} extends JsonResource
{
    public function toArray(\$request): array
    {
        return [
            // TODO: Define response structure for {$method}
            'id' => \$this->id ?? null,
            'created_at' => \$this->created_at,
            'updated_at' => \$this->updated_at,
        ];
    }

    public function with(\$request): array
    {
        return [
            'success' => true,
            'message' => 'Operation successful',
        ];
    }
}
";
    }

    /**
     * Get test file template.
     *
     * @param  mixed  $namespace
     * @param  mixed  $className
     * @param  mixed  $controller
     */
    protected function testTemplate($namespace, $className, $controller)
    {
        return "<?php

namespace {$namespace};

use Tests\\TestCase;
use Illuminate\\Foundation\\Testing\\RefreshDatabase;

class {$className} extends TestCase
{
    use RefreshDatabase;

    public function test_{$controller}_methods()
    {
        // TODO: Implement tests for {$controller}
        \$this->assertTrue(true);
    }
}
";
    }

    /**
     * Get controller namespace.
     *
     * @param  mixed  $path
     */
    protected function getNamespace($path)
    {
        $content = File::get($path);

        if (preg_match('/namespace\s+([^;]+);/', $content, $matches)) {
            return $matches[1];
        }

        return 'App\Http\Controllers';
    }

    /**
     * Display generation statistics.
     */
    protected function displayStats()
    {
        $this->newLine();
        $this->info('📊 Generation Complete!');

        $total = $this->stats['requests'] + $this->stats['responses'] + $this->stats['tests'];

        $this->table(['Metric', 'Count'], [
            ['Controllers', $this->stats['controllers']],
            ['Methods', $this->stats['methods']],
            ['Requests Generated', $this->stats['requests']],
            ['Responses Generated', $this->stats['responses']],
            ['Tests Generated', $this->stats['tests']],
            ['Files Skipped', $this->stats['skipped']],
            ['Total Generated', $total],
        ]);

        if ($total > 0) {
            $this->info("🎉 Generated {$total} files!");
        }
    }
}
