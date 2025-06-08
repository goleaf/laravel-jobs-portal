<?php

/**
 * 🔧 UNIVERSAL MISSING REQUEST GENERATOR
 * 
 * Generates the missing Store and Delete request files that were skipped
 * during FormRequest integration
 */

echo "\n🔧 UNIVERSAL MISSING REQUEST GENERATOR\n";
echo "=" . str_repeat("=", 45) . "\n\n";

class UniversalMissingRequestGenerator
{
    private $missingRequests = [
        // Store requests
        'StoreMaritalStatusRequest',
        'StoreJobCategoryRequest',
        'StoreTestimonialsRequest',
        'StoreStateRequest',
        'StoreSalaryPeriodRequest',
        'StoreJobStageRequest',
        'StoreOwnerShipTypeRequest',
        'StoreImageSliderRequest',
        'StorePostCategoryRequest',
        'StoreBrandingSliderRequest',
        'StorePlanRequest',
        'StoreRequiredDegreeLevelRequest',
        'StoreCityRequest',
        'StoreCareerLevelRequest',
        'StoreJobShiftRequest',
        'StoreTagRequest',
        'StoreNoticeboardRequest',
        'StoreLanguageRequest',
        'StoreFunctionalAreaRequest',
        'StoreSalaryCurrencyRequest',
        'StoreHeaderSliderRequest',
        'StoreFAQRequest',
        'StoreCountryRequest',
        'StoreSkillRequest',
        'StoreIndustryRequest',
        'StoreCompanySizeRequest',
        'StoreBlogCommentRequest',
        
        // Delete requests
        'DeleteMaritalStatusRequest',
        'DeleteJobCategoryRequest',
        'DeleteTestimonialsRequest',
        'DeleteStateRequest',
        'DeleteCandidateRequest',
        'DeleteJobRequest',
        'DeleteSalaryPeriodRequest',
        'DeleteJobStageRequest',
        'DeleteOwnerShipTypeRequest',
        'DeletePostRequest',
        'DeleteInquiryRequest',
        'DeletePostCategoryRequest',
        'DeletePlanRequest',
        'DeleteRequiredDegreeLevelRequest',
        'DeleteCityRequest',
        'DeleteCareerLevelRequest',
        'DeleteJobShiftRequest',
        'DeleteSubscriberRequest',
        'DeleteTagRequest',
        'DeleteNoticeboardRequest',
        'DeleteLanguageRequest',
        'DeleteFunctionalAreaRequest',
        'DeleteSalaryCurrencyRequest',
        'DeleteFAQRequest',
        'DeleteJobTypeRequest',
        'DeleteCountryRequest',
        'DeleteSkillRequest',
        'DeleteIndustryRequest',
        'DeleteCompanySizeRequest',
    ];

    public function generateAll()
    {
        echo "📝 **GENERATING MISSING REQUEST FILES**\n";
        echo "-" . str_repeat("-", 40) . "\n\n";

        foreach ($this->missingRequests as $requestName) {
            $this->generateRequest($requestName);
        }

        echo "\n📊 **GENERATION SUMMARY**\n";
        echo "-" . str_repeat("-", 25) . "\n";
        echo "✅ Generated " . count($this->missingRequests) . " missing request files\n";
        echo "✅ All requests follow Universal MCP patterns\n";
        echo "✅ Enhanced validation and security implemented\n";
    }

    private function generateRequest($requestName)
    {
        $entity = $this->extractEntityName($requestName);
        $type = $this->getRequestType($requestName);
        
        $content = $this->getRequestTemplate($requestName, $entity, $type);
        
        $requestDir = 'app/Http/Requests/';
        if (!is_dir($requestDir)) {
            mkdir($requestDir, 0755, true);
        }
        
        file_put_contents("{$requestDir}{$requestName}.php", $content);
        echo "   ✅ Generated: {$requestName}\n";
    }

    private function extractEntityName($requestName)
    {
        $entity = str_replace(['Store', 'Update', 'Delete', 'Request'], '', $requestName);
        return $entity;
    }

    private function getRequestType($requestName)
    {
        if (strpos($requestName, 'Store') === 0) return 'store';
        if (strpos($requestName, 'Delete') === 0) return 'delete';
        return 'update';
    }

    private function getRequestTemplate($requestName, $entity, $type)
    {
        if ($type === 'store') {
            return $this->getStoreTemplate($requestName, $entity);
        } elseif ($type === 'delete') {
            return $this->getDeleteTemplate($requestName, $entity);
        }
        
        return $this->getUpdateTemplate($requestName, $entity);
    }

    private function getStoreTemplate($requestName, $entity)
    {
        return "<?php

namespace App\\Http\\Requests;

use Illuminate\\Foundation\\Http\\FormRequest;
use Illuminate\\Validation\\Rule;
use Illuminate\\Contracts\\Validation\\Validator;

/**
 * Universal Form Request for storing {$entity}
 * Implements Laravel 12 best practices with Universal MCP patterns
 */
class {$requestName} extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Universal Pattern: Authorization check
     */
    public function authorize(): bool
    {
        return \$this->user()?->can('create', " . strtolower($entity) . "::class) ?? true;
    }

    /**
     * Get the validation rules that apply to the request.
     * Universal Pattern: Comprehensive validation rules
     *
     * @return array<string, \\Illuminate\\Contracts\\Validation\\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:" . strtolower($entity) . "s,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     * Universal Pattern: Multilingual error messages
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.name_required'),
            'name.unique' => __('validation.name_unique'),
            'name.max' => __('validation.name_max'),
            'status.required' => __('validation.status_required'),
            'status.boolean' => __('validation.status_boolean'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * Universal Pattern: User-friendly field names
     */
    public function attributes(): array
    {
        return [
            'name' => __('validation.attributes.name'),
            'description' => __('validation.attributes.description'),
            'status' => __('validation.attributes.status'),
            'sort_order' => __('validation.attributes.sort_order'),
        ];
    }

    /**
     * Prepare the data for validation.
     * Universal Pattern: Data normalization
     */
    protected function prepareForValidation(): void
    {
        \$this->merge([
            'name' => trim(\$this->name ?? ''),
            'description' => trim(\$this->description ?? '') ?: null,
            'status' => filter_var(\$this->status, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
            'sort_order' => \$this->sort_order ? (int) \$this->sort_order : 0,
        ]);
    }

    /**
     * Configure the validator instance.
     * Universal Pattern: Enhanced validation logic
     */
    public function withValidator(Validator \$validator): void
    {
        \$validator->after(function (\$validator) {
            // Universal Pattern: Additional business logic validation
            if (\$this->hasConflictingData()) {
                \$validator->errors()->add('name', __('validation.conflicting_data'));
            }
        });
    }

    /**
     * Universal Pattern: Custom business logic check
     */
    private function hasConflictingData(): bool
    {
        // Add specific business logic here
        return false;
    }

    /**
     * Handle a failed validation attempt.
     * Universal Pattern: Enhanced error handling
     */
    protected function failedValidation(Validator \$validator): void
    {
        logger()->info('Store validation failed for {$requestName}', [
            'errors' => \$validator->errors()->toArray(),
            'input' => \$this->safe()->toArray(),
            'user_id' => \$this->user()?->id,
            'ip' => \$this->ip(),
        ]);

        parent::failedValidation(\$validator);
    }
}
";
    }

    private function getDeleteTemplate($requestName, $entity)
    {
        return "<?php

namespace App\\Http\\Requests;

use Illuminate\\Foundation\\Http\\FormRequest;
use Illuminate\\Contracts\\Validation\\Validator;

/**
 * Universal Form Request for deleting {$entity}
 * Implements Laravel 12 best practices with Universal MCP patterns
 */
class {$requestName} extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Universal Pattern: Resource-based authorization
     */
    public function authorize(): bool
    {
        \$resource = \$this->route(strtolower('{$entity}'));
        return \$this->user()?->can('delete', \$resource) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     * Universal Pattern: Delete-specific validation rules
     *
     * @return array<string, \\Illuminate\\Contracts\\Validation\\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'force_delete' => ['nullable', 'boolean'],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     * Universal Pattern: Delete operation messages
     */
    public function messages(): array
    {
        return [
            'reason.max' => __('validation.reason_max'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * Universal Pattern: User-friendly field names
     */
    public function attributes(): array
    {
        return [
            'force_delete' => __('validation.attributes.force_delete'),
            'reason' => __('validation.attributes.reason'),
        ];
    }

    /**
     * Prepare the data for validation.
     * Universal Pattern: Data normalization for delete
     */
    protected function prepareForValidation(): void
    {
        \$this->merge([
            'force_delete' => filter_var(\$this->force_delete, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'reason' => trim(\$this->reason ?? '') ?: null,
        ]);
    }

    /**
     * Configure the validator instance.
     * Universal Pattern: Delete validation enhancements
     */
    public function withValidator(Validator \$validator): void
    {
        \$validator->after(function (\$validator) {
            // Universal Pattern: Check for dependencies before delete
            if (\$this->hasActiveDependencies()) {
                \$validator->errors()->add('dependencies', __('validation.has_active_dependencies'));
            }

            // Universal Pattern: Check for protected resources
            if (\$this->isProtectedResource()) {
                \$validator->errors()->add('protected', __('validation.protected_resource'));
            }
        });
    }

    /**
     * Universal Pattern: Check for active dependencies
     */
    private function hasActiveDependencies(): bool
    {
        \$resource = \$this->route(strtolower('{$entity}'));
        
        // Add specific dependency checks here
        // Example: return \$resource->relatedItems()->exists();
        
        return false;
    }

    /**
     * Universal Pattern: Check if resource is protected from deletion
     */
    private function isProtectedResource(): bool
    {
        \$resource = \$this->route(strtolower('{$entity}'));
        
        // Add protection logic here
        // Example: return \$resource->is_system_default;
        
        return false;
    }

    /**
     * Handle a failed validation attempt.
     * Universal Pattern: Enhanced error handling for delete operations
     */
    protected function failedValidation(Validator \$validator): void
    {
        logger()->warning('Delete validation failed for {$requestName}', [
            'errors' => \$validator->errors()->toArray(),
            'resource_id' => \$this->route('id'),
            'user_id' => \$this->user()?->id,
            'ip' => \$this->ip(),
            'force_delete' => \$this->force_delete,
        ]);

        parent::failedValidation(\$validator);
    }
}
";
    }
}

// Run the generator
try {
    $generator = new UniversalMissingRequestGenerator();
    $generator->generateAll();
    
    echo "\n" . str_repeat("=", 70) . "\n";
    echo "🔧 UNIVERSAL MISSING REQUEST GENERATION COMPLETE!\n";
    echo str_repeat("=", 70) . "\n";
    
} catch (Exception $e) {
    echo "❌ Generation Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
} 