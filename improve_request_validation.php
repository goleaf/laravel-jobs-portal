<?php

require_once __DIR__ . '/vendor/autoload.php';

class RequestValidationImprover
{
    private $improvedRequests = [];
    private $errors = [];
    private $validationTemplates = [];

    public function __construct()
    {
        // Initialize Laravel app
        $app = require_once __DIR__ . '/bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
        
        $this->initializeValidationTemplates();
    }

    public function improveRequestValidation()
    {
        echo "🔧 Improving request validation system...\n\n";
        
        $this->updateCriticalRequestFiles();
        $this->generateImprovedValidationReport();
    }

    private function initializeValidationTemplates()
    {
        $this->validationTemplates = [
            // Candidate validation templates
            'Candidate' => [
                'store' => [
                    'rules' => [
                        'user.first_name' => 'required|string|max:255',
                        'user.last_name' => 'required|string|max:255',
                        'user.email' => 'required|email|unique:users,email',
                        'user.password' => 'required|string|min:8|confirmed',
                        'user.phone' => 'nullable|string|max:20',
                        'user.dob' => 'nullable|date|before:today',
                        'marital_status_id' => 'nullable|exists:marital_statuses,id',
                        'nationality' => 'nullable|string|max:100',
                        'country_id' => 'nullable|exists:countries,id',
                        'state_id' => 'nullable|exists:states,id',
                        'city_id' => 'nullable|exists:cities,id',
                        'is_active' => 'boolean',
                        'is_verified' => 'boolean'
                    ],
                    'messages' => [
                        'user.first_name.required' => 'First name is required',
                        'user.last_name.required' => 'Last name is required',
                        'user.email.required' => 'Email is required',
                        'user.email.unique' => 'Email already exists',
                        'user.password.required' => 'Password is required',
                        'user.password.min' => 'Password must be at least 8 characters',
                        'user.password.confirmed' => 'Password confirmation does not match'
                    ]
                ],
                'update' => [
                    'rules' => [
                        'user.first_name' => 'required|string|max:255',
                        'user.last_name' => 'required|string|max:255',
                        'user.email' => 'required|email|unique:users,email,{user_id}',
                        'user.phone' => 'nullable|string|max:20',
                        'user.dob' => 'nullable|date|before:today',
                        'marital_status_id' => 'nullable|exists:marital_statuses,id',
                        'nationality' => 'nullable|string|max:100',
                        'country_id' => 'nullable|exists:countries,id',
                        'state_id' => 'nullable|exists:states,id',
                        'city_id' => 'nullable|exists:cities,id',
                        'is_active' => 'boolean',
                        'is_verified' => 'boolean'
                    ],
                    'messages' => [
                        'user.first_name.required' => 'First name is required',
                        'user.last_name.required' => 'Last name is required',
                        'user.email.required' => 'Email is required',
                        'user.email.unique' => 'Email already exists'
                    ]
                ]
            ],
            
            // Job validation templates
            'Job' => [
                'store' => [
                    'rules' => [
                        'job_title' => 'required|string|max:255',
                        'job_description' => 'required|string|min:50',
                        'job_requirement' => 'nullable|string',
                        'job_benefit' => 'nullable|string',
                        'country_id' => 'required|exists:countries,id',
                        'state_id' => 'nullable|exists:states,id',
                        'city_id' => 'nullable|exists:cities,id',
                        'salary_from' => 'nullable|numeric|min:0',
                        'salary_to' => 'nullable|numeric|min:0|gte:salary_from',
                        'salary_currency_id' => 'nullable|exists:salary_currencies,id',
                        'salary_period_id' => 'nullable|exists:salary_periods,id',
                        'job_category_id' => 'required|exists:job_categories,id',
                        'job_type_id' => 'required|exists:job_types,id',
                        'career_level_id' => 'nullable|exists:career_levels,id',
                        'functional_area_id' => 'nullable|exists:functional_areas,id',
                        'job_shift_id' => 'nullable|exists:job_shifts,id',
                        'degree_level_id' => 'nullable|exists:required_degree_levels,id',
                        'position' => 'nullable|integer|min:1',
                        'experience' => 'nullable|string|max:100',
                        'job_expiry_date' => 'required|date|after:today',
                        'hide_salary' => 'boolean',
                        'is_freelance' => 'boolean',
                        'is_suspended' => 'boolean'
                    ],
                    'messages' => [
                        'job_title.required' => 'Job title is required',
                        'job_description.required' => 'Job description is required',
                        'job_description.min' => 'Job description must be at least 50 characters',
                        'country_id.required' => 'Country is required',
                        'job_category_id.required' => 'Job category is required',
                        'job_type_id.required' => 'Job type is required',
                        'salary_to.gte' => 'Maximum salary must be greater than or equal to minimum salary',
                        'job_expiry_date.required' => 'Job expiry date is required',
                        'job_expiry_date.after' => 'Job expiry date must be in the future'
                    ]
                ]
            ],
            
            // Company validation templates
            'Company' => [
                'store' => [
                    'rules' => [
                        'name' => 'required|string|max:255',
                        'email' => 'required|email|unique:companies,email',
                        'phone' => 'nullable|string|max:20',
                        'website' => 'nullable|url|max:255',
                        'industry_id' => 'nullable|exists:industries,id',
                        'ownership_type_id' => 'nullable|exists:ownership_types,id',
                        'company_size_id' => 'nullable|exists:company_sizes,id',
                        'established_in' => 'nullable|integer|min:1800|max:' . date('Y'),
                        'description' => 'nullable|string|max:2000',
                        'country_id' => 'required|exists:countries,id',
                        'state_id' => 'nullable|exists:states,id',
                        'city_id' => 'nullable|exists:cities,id',
                        'address' => 'nullable|string|max:500',
                        'postal_code' => 'nullable|string|max:20',
                        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                        'is_active' => 'boolean'
                    ],
                    'messages' => [
                        'name.required' => 'Company name is required',
                        'email.required' => 'Email is required',
                        'email.unique' => 'Email already exists',
                        'website.url' => 'Please enter a valid website URL',
                        'country_id.required' => 'Country is required',
                        'established_in.min' => 'Establishment year cannot be before 1800',
                        'established_in.max' => 'Establishment year cannot be in the future',
                        'logo.image' => 'Logo must be an image',
                        'logo.max' => 'Logo size cannot exceed 2MB'
                    ]
                ]
            ],
            
            // Transaction validation templates
            'Transaction' => [
                'store' => [
                    'rules' => [
                        'user_id' => 'required|exists:users,id',
                        'subscription_plan_id' => 'required|exists:subscription_plans,id',
                        'transaction_id' => 'required|string|unique:transactions,transaction_id',
                        'amount' => 'required|numeric|min:0',
                        'payment_type' => 'required|in:stripe,paypal,razorpay,paystack,manual',
                        'status' => 'required|in:pending,approved,denied,cancelled',
                        'meta' => 'nullable|json'
                    ],
                    'messages' => [
                        'user_id.required' => 'User is required',
                        'subscription_plan_id.required' => 'Subscription plan is required',
                        'transaction_id.required' => 'Transaction ID is required',
                        'transaction_id.unique' => 'Transaction ID already exists',
                        'amount.required' => 'Amount is required',
                        'amount.numeric' => 'Amount must be a number',
                        'payment_type.required' => 'Payment type is required',
                        'status.required' => 'Status is required'
                    ]
                ]
            ],
            
            // Auth validation templates
            'Auth' => [
                'login' => [
                    'rules' => [
                        'email' => 'required|email',
                        'password' => 'required|string',
                        'remember' => 'boolean'
                    ],
                    'messages' => [
                        'email.required' => 'Email is required',
                        'email.email' => 'Please enter a valid email address',
                        'password.required' => 'Password is required'
                    ]
                ],
                'register' => [
                    'rules' => [
                        'first_name' => 'required|string|max:255',
                        'last_name' => 'required|string|max:255',
                        'email' => 'required|email|unique:users,email',
                        'password' => 'required|string|min:8|confirmed',
                        'phone' => 'nullable|string|max:20',
                        'terms' => 'required|accepted'
                    ],
                    'messages' => [
                        'first_name.required' => 'First name is required',
                        'last_name.required' => 'Last name is required',
                        'email.required' => 'Email is required',
                        'email.unique' => 'Email already exists',
                        'password.required' => 'Password is required',
                        'password.min' => 'Password must be at least 8 characters',
                        'password.confirmed' => 'Password confirmation does not match',
                        'terms.required' => 'You must accept the terms and conditions'
                    ]
                ]
            ]
        ];
    }

    private function updateCriticalRequestFiles()
    {
        echo "🔧 Updating critical request files...\n";
        
        $criticalRequests = [
            'app/Http/Requests/Candidate/StoreCandidateRequest.php' => ['entity' => 'Candidate', 'action' => 'store'],
            'app/Http/Requests/Candidate/UpdateCandidateRequest.php' => ['entity' => 'Candidate', 'action' => 'update'],
            'app/Http/Requests/Job/StoreJobRequest.php' => ['entity' => 'Job', 'action' => 'store'],
            'app/Http/Requests/Job/UpdateJobRequest.php' => ['entity' => 'Job', 'action' => 'store'], // Use same rules as store
            'app/Http/Requests/Company/StoreCompanyRequest.php' => ['entity' => 'Company', 'action' => 'store'],
            'app/Http/Requests/Company/UpdateCompanyRequest.php' => ['entity' => 'Company', 'action' => 'store'],
            'app/Http/Requests/Transaction/StoreTransactionRequest.php' => ['entity' => 'Transaction', 'action' => 'store'],
            'app/Http/Requests/Auth/LoginRequest.php' => ['entity' => 'Auth', 'action' => 'login'],
            'app/Http/Requests/Auth/RegisterRequest.php' => ['entity' => 'Auth', 'action' => 'register']
        ];

        foreach ($criticalRequests as $filePath => $config) {
            $this->updateRequestFile($filePath, $config);
        }
    }

    private function updateRequestFile($filePath, $config)
    {
        $entity = $config['entity'];
        $action = $config['action'];
        
        if (!isset($this->validationTemplates[$entity][$action])) {
            echo "⚠️  No template found for {$entity}.{$action}\n";
            return;
        }

        $template = $this->validationTemplates[$entity][$action];
        $className = basename($filePath, '.php');
        
        // Create directory if it doesn't exist
        $directory = dirname($filePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $requestContent = $this->generateRequestFileContent($className, $template, $entity, $action);
        
        file_put_contents($filePath, $requestContent);
        $this->improvedRequests[] = $filePath;
        echo "✅ Updated: $filePath\n";
    }

    private function generateRequestFileContent($className, $template, $entity, $action)
    {
        $rules = json_encode($template['rules'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $messages = json_encode($template['messages'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        
        return "<?php

namespace App\Http\Requests\\{$entity};

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NoMaliciousContent;

/**
 * Request validation for {$entity}Controller::{$action}
 * 
 * @enhanced by RequestValidationImprover
 */
class {$className} extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // TODO: Implement proper authorization logic based on user permissions
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return {$rules};
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return {$messages};
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'user.first_name' => 'first name',
            'user.last_name' => 'last name',
            'user.email' => 'email address',
            'user.phone' => 'phone number',
            'job_title' => 'job title',
            'job_description' => 'job description',
            'job_expiry_date' => 'job expiry date',
            'salary_from' => 'minimum salary',
            'salary_to' => 'maximum salary'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize input data
        if (\$this->has('job_title')) {
            \$this->merge([
                'job_title' => strip_tags(\$this->job_title)
            ]);
        }
        
        if (\$this->has('job_description')) {
            \$this->merge([
                'job_description' => strip_tags(\$this->job_description, '<p><br><ul><ol><li><strong><em>')
            ]);
        }
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  \$validator
     * @return void
     */
    public function withValidator(\$validator): void
    {
        \$validator->after(function (\$validator) {
            // Add custom validation logic here
            if (\$this->has('salary_from') && \$this->has('salary_to')) {
                if (\$this->salary_from > \$this->salary_to) {
                    \$validator->errors()->add('salary_to', 'Maximum salary must be greater than minimum salary');
                }
            }
            
            // Check for malicious content in text fields
            foreach (['job_description', 'job_requirement', 'job_benefit'] as \$field) {
                if (\$this->has(\$field) && \$this->{\$field}) {
                    \$rule = new NoMaliciousContent();
                    if (!\$rule->passes(\$field, \$this->{\$field})) {
                        \$validator->errors()->add(\$field, \$rule->message());
                    }
                }
            }
        });
    }
}
";
    }

    private function generateImprovedValidationReport()
    {
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "📊 REQUEST VALIDATION IMPROVEMENT REPORT\n";
        echo str_repeat("=", 80) . "\n\n";
        echo "✅ Improved Request Files: " . count($this->improvedRequests) . "\n";
        echo "❌ Errors: " . count($this->errors) . "\n\n";

        if (!empty($this->improvedRequests)) {
            echo "📁 IMPROVED REQUEST FILES:\n";
            foreach ($this->improvedRequests as $file) {
                echo "• $file\n";
            }
            echo "\n";
        }

        if (!empty($this->errors)) {
            echo "❌ ERRORS:\n";
            foreach ($this->errors as $error) {
                echo "• $error\n";
            }
            echo "\n";
        }

        echo "✨ IMPROVEMENTS MADE:\n";
        echo "• Added comprehensive validation rules for all major entities\n";
        echo "• Added custom error messages with user-friendly text\n";
        echo "• Added security validation to prevent XSS attacks\n";
        echo "• Added data sanitization in prepareForValidation()\n";
        echo "• Added custom validator logic for complex rules\n";
        echo "• Added proper attribute names for better error messages\n";
        echo "\n";
        
        echo str_repeat("=", 80) . "\n";
    }
}

// Run the request validation improvement
try {
    $improver = new RequestValidationImprover();
    $improver->improveRequestValidation();
    echo "\n✅ Request validation improvement completed successfully!\n";
} catch (Exception $e) {
    echo "\n❌ Error during improvement: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
} 