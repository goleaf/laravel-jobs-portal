<?php

/**
 * Context7 Job & Financial FormRequest Builder - BUILD MODE
 * Completes Priority 3 FormRequests for Job and Financial controllers
 */

class Context7JobFinancialBuilder
{
    private array $generatedFiles = [];
    
    public function build(): void
    {
        echo "🚀 Context7 Job & Financial FormRequest Builder\n";
        echo "==============================================\n\n";
        
        $this->generateJobRequests();
        $this->generateFinancialRequests();
        $this->generateMasterDataCompleteSet();
        $this->generateReport();
    }
    
    private function generateJobRequests(): void
    {
        echo "🔧 Building Job FormRequests...\n";
        
        // JobType FormRequests
        $this->createFormRequest('Job', 'StoreJobTypeRequest', [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);
        
        $this->createFormRequest('Job', 'UpdateJobTypeRequest', [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);
        
        $this->createFormRequest('Job', 'DeleteJobTypeRequest', []);
        
        // JobShift FormRequests
        $this->createFormRequest('Job', 'StoreJobShiftRequest', [
            'shift' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'size' => 'nullable|string|max:255'
        ]);
        
        $this->createFormRequest('Job', 'UpdateJobShiftRequest', [
            'shift' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'size' => 'nullable|string|max:255'
        ]);
        
        $this->createFormRequest('Job', 'DeleteJobShiftRequest', []);
        
        echo "  ✅ Job FormRequests completed\n\n";
    }
    
    private function generateFinancialRequests(): void
    {
        echo "💰 Building Financial FormRequests...\n";
        
        // SalaryCurrency FormRequests
        $this->createFormRequest('Financial', 'StoreSalaryCurrencyRequest', [
            'currency_name' => 'required|string|max:255',
            'currency_code' => 'required|string|max:10',
            'currency_icon' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);
        
        $this->createFormRequest('Financial', 'UpdateSalaryCurrencyRequest', [
            'currency_name' => 'required|string|max:255',
            'currency_code' => 'required|string|max:10',
            'currency_icon' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);
        
        $this->createFormRequest('Financial', 'DeleteSalaryCurrencyRequest', []);
        
        echo "  ✅ Financial FormRequests completed\n\n";
    }
    
    private function generateMasterDataCompleteSet(): void
    {
        echo "📊 Building Additional MasterData FormRequests...\n";
        
        // CareerLevel FormRequests
        $this->createFormRequest('MasterData', 'StoreCareerLevelRequest', [
            'level_name' => 'required|string|max:255',
            'from_year' => 'nullable|integer|min:0|max:50',
            'to_year' => 'nullable|integer|min:0|max:50',
            'is_active' => 'boolean'
        ]);
        
        $this->createFormRequest('MasterData', 'UpdateCareerLevelRequest', [
            'level_name' => 'required|string|max:255',
            'from_year' => 'nullable|integer|min:0|max:50',
            'to_year' => 'nullable|integer|min:0|max:50',
            'is_active' => 'boolean'
        ]);
        
        $this->createFormRequest('MasterData', 'DeleteCareerLevelRequest', []);
        
        // CompanySize FormRequests
        $this->createFormRequest('MasterData', 'StoreCompanySizeRequest', [
            'size' => 'required|string|max:255',
            'from_range' => 'nullable|integer|min:1',
            'to_range' => 'nullable|integer|min:1',
            'is_active' => 'boolean'
        ]);
        
        $this->createFormRequest('MasterData', 'UpdateCompanySizeRequest', [
            'size' => 'required|string|max:255',
            'from_range' => 'nullable|integer|min:1',
            'to_range' => 'nullable|integer|min:1',
            'is_active' => 'boolean'
        ]);
        
        $this->createFormRequest('MasterData', 'DeleteCompanySizeRequest', []);
        
        echo "  ✅ Additional MasterData FormRequests completed\n\n";
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
 * Following proven MasterData pattern
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
        \$data = [];
        
        if (isset(\$this->name)) {
            \$data['name'] = trim(\$this->name);
        }
        
        if (isset(\$this->currency_name)) {
            \$data['currency_name'] = trim(\$this->currency_name);
        }
        
        if (isset(\$this->level_name)) {
            \$data['level_name'] = trim(\$this->level_name);
        }
        
        if (isset(\$this->shift)) {
            \$data['shift'] = trim(\$this->shift);
        }
        
        if (isset(\$this->size)) {
            \$data['size'] = trim(\$this->size);
        }
        
        \$data['is_active'] = filter_var(\$this->is_active, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true;
        
        \$this->merge(\$data);
    }

    public function withValidator(Validator \$validator): void
    {
        \$validator->after(function (\$validator) {
            if (\$this->hasBusinessLogicConflicts()) {
                \$validator->errors()->add('name', __('validation.{$entityLower}_business_conflict'));
            }
        });
    }

    private function hasBusinessLogicConflicts(): bool
    {
        // Add specific business logic validation here
        return false;
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
        echo "📊 Context7 Job & Financial Build Report\n";
        echo "=======================================\n";
        echo "Generated Files: " . count($this->generatedFiles) . "\n\n";
        
        foreach ($this->generatedFiles as $file) {
            echo "✅ $file\n";
        }
        
        echo "\n🎯 BUILD STATUS: Priority 1-3 FormRequests Complete\n";
        echo "• Location FormRequests: ✅ Complete\n";
        echo "• MasterData FormRequests: ✅ Complete\n";
        echo "• Job FormRequests: ✅ Complete\n";
        echo "• Financial FormRequests: ✅ Complete\n";
        echo "\nReady for controller integration and validation testing\n";
    }
}

// Execute the builder
$builder = new Context7JobFinancialBuilder();
$builder->build(); 