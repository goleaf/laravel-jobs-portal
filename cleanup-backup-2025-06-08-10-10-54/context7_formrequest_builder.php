<?php

/**
 * Context7 FormRequest Builder - BUILD MODE Implementation
 * Rapidly generates remaining FormRequests for Location and MasterData controllers
 */

class Context7FormRequestBuilder
{
    private array $generatedFiles = [];
    
    public function build(): void
    {
        echo "🚀 Context7 FormRequest Builder - BUILD MODE\n";
        echo "===========================================\n\n";
        
        $this->generateLocationRequests();
        $this->generateMasterDataRequests();
        $this->generateReport();
    }
    
    private function generateLocationRequests(): void
    {
        echo "📍 Building Location FormRequests...\n";
        
        // State FormRequests
        $this->createFormRequest('Location', 'UpdateStateRequest', [
            'name' => 'required|string|max:255',
            'country_id' => 'required|integer|exists:countries,id',
            'code' => 'nullable|string|max:10',
            'is_active' => 'boolean'
        ]);
        
        $this->createFormRequest('Location', 'DeleteStateRequest', []);
        
        // City FormRequests  
        $this->createFormRequest('Location', 'StoreCityRequest', [
            'name' => 'required|string|max:255',
            'state_id' => 'required|integer|exists:states,id',
            'is_active' => 'boolean'
        ]);
        
        $this->createFormRequest('Location', 'UpdateCityRequest', [
            'name' => 'required|string|max:255',
            'state_id' => 'required|integer|exists:states,id',
            'is_active' => 'boolean'
        ]);
        
        $this->createFormRequest('Location', 'DeleteCityRequest', []);
        
        echo "  ✅ Location FormRequests completed\n\n";
    }
    
    private function generateMasterDataRequests(): void
    {
        echo "📊 Building MasterData FormRequests...\n";
        
        // Industry FormRequests
        $this->createFormRequest('MasterData', 'StoreIndustryRequest', [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'size' => 'nullable|string|max:255'
        ]);
        
        $this->createFormRequest('MasterData', 'UpdateIndustryRequest', [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'size' => 'nullable|string|max:255'
        ]);
        
        $this->createFormRequest('MasterData', 'DeleteIndustryRequest', []);
        
        // FunctionalArea FormRequests
        $this->createFormRequest('MasterData', 'StoreFunctionalAreaRequest', [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);
        
        $this->createFormRequest('MasterData', 'UpdateFunctionalAreaRequest', [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);
        
        $this->createFormRequest('MasterData', 'DeleteFunctionalAreaRequest', []);
        
        echo "  ✅ MasterData FormRequests completed\n\n";
    }
    
    private function createFormRequest(string $directory, string $className, array $rules): void
    {
        $entity = str_replace(['Store', 'Update', 'Delete', 'Request'], '', $className);
        $entityLower = strtolower($entity);
        $namespace = "App\\Http\\Requests\\$directory";
        $filePath = "app/Http/Requests/$directory/$className.php";
        
        $rulesStr = $this->generateRulesArray($rules);
        $messagesStr = $this->generateMessages($rules, $entityLower);
        $attributesStr = $this->generateAttributes($rules, $entityLower);
        
        $content = "<?php

namespace $namespace;

use Illuminate\\Foundation\\Http\\FormRequest;
use Illuminate\\Validation\\Rule;
use Illuminate\\Contracts\\Validation\\Validator;

/**
 * Context7 Enhanced Form Request for $className
 * Implements Laravel 12 best practices with Context7 MCP patterns
 */
class $className extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (
            auth()->user()->hasRole('Admin') || 
            auth()->user()->hasRole('Employer')
        );
    }

    public function rules(): array
    {
        return [
$rulesStr
        ];
    }

    public function messages(): array
    {
        return [
$messagesStr
        ];
    }

    public function attributes(): array
    {
        return [
$attributesStr
        ];
    }

    protected function prepareForValidation(): void
    {
        \$this->merge([
            'name' => trim(\$this->name ?? ''),
            'is_active' => filter_var(\$this->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
        ]);
    }
}";

        file_put_contents($filePath, $content);
        $this->generatedFiles[] = $filePath;
        echo "    ✓ $className\n";
    }
    
    private function generateRulesArray(array $rules): string
    {
        if (empty($rules)) return '';
        
        $rulesStrings = [];
        foreach ($rules as $field => $rule) {
            $ruleArray = "'" . str_replace('|', "', '", $rule) . "'";
            $rulesStrings[] = "            '$field' => [$ruleArray],";
        }
        
        return implode("\n", $rulesStrings);
    }
    
    private function generateMessages(array $rules, string $entity): string
    {
        if (empty($rules)) return '';
        
        $messages = [];
        foreach (array_keys($rules) as $field) {
            $messages[] = "            '$field.required' => __('validation.{$entity}_{$field}_required'),";
            $messages[] = "            '$field.max' => __('validation.{$entity}_{$field}_max'),";
        }
        
        return implode("\n", $messages);
    }
    
    private function generateAttributes(array $rules, string $entity): string
    {
        if (empty($rules)) return '';
        
        $attributes = [];
        foreach (array_keys($rules) as $field) {
            $attributes[] = "            '$field' => __('validation.attributes.{$entity}_{$field}'),";
        }
        
        return implode("\n", $attributes);
    }
    
    private function generateReport(): void
    {
        echo "📊 Context7 FormRequest Build Report\n";
        echo "===================================\n";
        echo "Generated Files: " . count($this->generatedFiles) . "\n\n";
        
        foreach ($this->generatedFiles as $file) {
            echo "✅ $file\n";
        }
        
        echo "\n🎯 BUILD STATUS: Location & MasterData FormRequests Complete\n";
        echo "Ready for controller integration and testing phase\n";
    }
}

// Execute the builder
$builder = new Context7FormRequestBuilder();
$builder->build(); 