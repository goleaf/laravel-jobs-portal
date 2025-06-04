<?php

/**
 * Comprehensive Request Files Generator
 * Creates Laravel Form Request files for ALL controller functions with validation
 */

class RequestFileGenerator
{
    private $controllerFunctions;
    
    public function __construct()
    {
        $this->controllerFunctions = [
            // Admin Controllers
            'AdminController' => [
                'store' => [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'nullable|string|max:255',
                    'email' => 'required|email|unique:admins,email',
                    'password' => 'required|string|min:8|confirmed',
                    'role' => 'required|string|in:super_admin,admin'
                ],
                'update' => [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'nullable|string|max:255',
                    'email' => 'required|email|unique:admins,email,{id}',
                    'password' => 'nullable|string|min:8|confirmed',
                    'role' => 'required|string|in:super_admin,admin'
                ]
            ],
            
            // Candidate Controllers
            'CandidateController' => [
                'store' => [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'nullable|string|max:255',
                    'email' => 'required|email|unique:users,email',
                    'phone' => 'nullable|string|max:20',
                    'password' => 'required|string|min:8|confirmed',
                    'date_of_birth' => 'nullable|date',
                    'gender' => 'nullable|in:male,female,other',
                    'address' => 'nullable|string'
                ],
                'update' => [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'nullable|string|max:255',
                    'email' => 'required|email|unique:users,email,{id}',
                    'phone' => 'nullable|string|max:20',
                    'date_of_birth' => 'nullable|date',
                    'gender' => 'nullable|in:male,female,other',
                    'address' => 'nullable|string'
                ],
                'updateProfile' => [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'nullable|string|max:255',
                    'phone' => 'nullable|string|max:20',
                    'date_of_birth' => 'nullable|date',
                    'gender' => 'nullable|in:male,female,other',
                    'address' => 'nullable|string',
                    'linkedin_url' => 'nullable|url',
                    'website_url' => 'nullable|url',
                    'github_url' => 'nullable|url',
                    'bio' => 'nullable|string|max:1000'
                ],
                'updateExperience' => [
                    'company' => 'required|string|max:255',
                    'position' => 'required|string|max:255',
                    'start_date' => 'required|date',
                    'end_date' => 'nullable|date|after:start_date',
                    'description' => 'nullable|string|max:1000',
                    'is_current' => 'boolean'
                ],
                'updateEducation' => [
                    'institution' => 'required|string|max:255',
                    'degree' => 'required|string|max:255',
                    'field_of_study' => 'required|string|max:255',
                    'start_date' => 'required|date',
                    'end_date' => 'nullable|date|after:start_date',
                    'grade' => 'nullable|string|max:10',
                    'is_current' => 'boolean'
                ]
            ],
            
            // Job Controllers
            'JobController' => [
                'store' => [
                    'title' => 'required|string|max:255',
                    'description' => 'required|string',
                    'job_type_id' => 'required|exists:job_types,id',
                    'job_category_id' => 'required|exists:job_categories,id',
                    'company_id' => 'required|exists:companies,id',
                    'location' => 'required|string|max:255',
                    'salary_min' => 'nullable|numeric|min:0',
                    'salary_max' => 'nullable|numeric|gt:salary_min',
                    'salary_currency_id' => 'nullable|exists:salary_currencies,id',
                    'experience_required' => 'nullable|string',
                    'skills_required' => 'nullable|array',
                    'application_deadline' => 'nullable|date|after:today',
                    'is_featured' => 'boolean',
                    'is_remote' => 'boolean',
                    'status' => 'required|in:draft,published,closed'
                ],
                'update' => [
                    'title' => 'required|string|max:255',
                    'description' => 'required|string',
                    'job_type_id' => 'required|exists:job_types,id',
                    'job_category_id' => 'required|exists:job_categories,id',
                    'location' => 'required|string|max:255',
                    'salary_min' => 'nullable|numeric|min:0',
                    'salary_max' => 'nullable|numeric|gt:salary_min',
                    'salary_currency_id' => 'nullable|exists:salary_currencies,id',
                    'experience_required' => 'nullable|string',
                    'skills_required' => 'nullable|array',
                    'application_deadline' => 'nullable|date|after:today',
                    'is_featured' => 'boolean',
                    'is_remote' => 'boolean',
                    'status' => 'required|in:draft,published,closed'
                ]
            ],
            
            // Company Controllers
            'CompanyController' => [
                'store' => [
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|unique:companies,email',
                    'phone' => 'nullable|string|max:20',
                    'website' => 'nullable|url',
                    'description' => 'nullable|string',
                    'address' => 'nullable|string',
                    'city_id' => 'nullable|exists:cities,id',
                    'state_id' => 'nullable|exists:states,id',
                    'country_id' => 'nullable|exists:countries,id',
                    'industry_id' => 'nullable|exists:industries,id',
                    'company_size_id' => 'nullable|exists:company_sizes,id',
                    'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'established_year' => 'nullable|digits:4|min:1800|max:2024'
                ],
                'update' => [
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|unique:companies,email,{id}',
                    'phone' => 'nullable|string|max:20',
                    'website' => 'nullable|url',
                    'description' => 'nullable|string',
                    'address' => 'nullable|string',
                    'city_id' => 'nullable|exists:cities,id',
                    'state_id' => 'nullable|exists:states,id',
                    'country_id' => 'nullable|exists:countries,id',
                    'industry_id' => 'nullable|exists:industries,id',
                    'company_size_id' => 'nullable|exists:company_sizes,id',
                    'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'established_year' => 'nullable|digits:4|min:1800|max:2024'
                ]
            ],
            
            // Contact Form
            'ContactController' => [
                'store' => [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'nullable|string|max:255',
                    'email' => 'required|email',
                    'phone' => 'nullable|string|max:20',
                    'subject' => 'required|string|max:255',
                    'message' => 'required|string|min:10'
                ]
            ],
            
            // Authentication
            'AuthController' => [
                'login' => [
                    'email' => 'required|email',
                    'password' => 'required|string',
                    'remember' => 'boolean'
                ],
                'register' => [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'nullable|string|max:255',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required|string|min:8|confirmed',
                    'phone' => 'nullable|string|max:20',
                    'terms_accepted' => 'required|accepted'
                ],
                'resetPassword' => [
                    'email' => 'required|email|exists:users,email'
                ],
                'updatePassword' => [
                    'current_password' => 'required|string',
                    'password' => 'required|string|min:8|confirmed'
                ]
            ],
            
            // Settings
            'SettingsController' => [
                'updateGeneral' => [
                    'app_name' => 'required|string|max:255',
                    'app_description' => 'nullable|string',
                    'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'app_favicon' => 'nullable|image|mimes:ico,png|max:1024',
                    'contact_email' => 'required|email',
                    'contact_phone' => 'nullable|string|max:20'
                ],
                'updateSeo' => [
                    'meta_title' => 'required|string|max:60',
                    'meta_description' => 'required|string|max:160',
                    'meta_keywords' => 'nullable|string|max:255'
                ]
            ],
            
            // Job Applications
            'JobApplicationController' => [
                'store' => [
                    'job_id' => 'required|exists:jobs,id',
                    'cover_letter' => 'nullable|string|max:2000',
                    'resume_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120'
                ],
                'update' => [
                    'status' => 'required|in:pending,reviewed,shortlisted,rejected,hired',
                    'notes' => 'nullable|string|max:1000'
                ]
            ],
            
            // Posts/Blog
            'PostController' => [
                'store' => [
                    'title' => 'required|string|max:255',
                    'content' => 'required|string',
                    'excerpt' => 'nullable|string|max:500',
                    'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'status' => 'required|in:draft,published',
                    'category_id' => 'nullable|exists:blog_categories,id',
                    'tags' => 'nullable|array'
                ],
                'update' => [
                    'title' => 'required|string|max:255',
                    'content' => 'required|string',
                    'excerpt' => 'nullable|string|max:500',
                    'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                    'status' => 'required|in:draft,published',
                    'category_id' => 'nullable|exists:blog_categories,id',
                    'tags' => 'nullable|array'
                ]
            ]
        ];
    }

    private $messagesTemplate = [
        'required' => 'The :attribute field is required.',
        'email' => 'Please enter a valid email address.',
        'unique' => 'This :attribute has already been taken.',
        'min' => 'The :attribute must be at least :min characters.',
        'max' => 'The :attribute may not be greater than :max characters.',
        'confirmed' => 'The :attribute confirmation does not match.',
        'exists' => 'The selected :attribute is invalid.',
        'image' => 'The :attribute must be an image.',
        'mimes' => 'The :attribute must be a file of type: :values.',
        'numeric' => 'The :attribute must be a number.',
        'date' => 'The :attribute is not a valid date.',
        'after' => 'The :attribute must be a date after :date.',
        'url' => 'The :attribute format is invalid.',
        'boolean' => 'The :attribute field must be true or false.',
        'array' => 'The :attribute must be an array.',
        'accepted' => 'The :attribute must be accepted.'
    ];

    public function generateAllRequestFiles()
    {
        echo "🚀 GENERATING COMPREHENSIVE REQUEST FILES\n";
        echo "==========================================\n\n";

        foreach ($this->controllerFunctions as $controller => $functions) {
            foreach ($functions as $method => $rules) {
                $this->generateRequestFile($controller, $method, $rules);
            }
        }

        echo "✅ All request files generated successfully!\n\n";
    }

    private function generateRequestFile($controller, $method, $rules)
    {
        $className = $this->getRequestClassName($controller, $method);
        $filename = $this->getRequestFilename($className);
        
        echo "📝 Generating: {$className} for {$controller}::{$method}\n";

        // Create directory if it doesn't exist
        $directory = dirname($filename);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $content = $this->generateRequestFileContent($className, $rules);
        file_put_contents($filename, $content);
    }

    private function getRequestClassName($controller, $method)
    {
        $base = str_replace('Controller', '', $controller);
        return ucfirst($method) . $base . 'Request';
    }

    private function getRequestFilename($className)
    {
        return "app/Http/Requests/{$className}.php";
    }

    private function generateRequestFileContent($className, $rules)
    {
        $rulesArray = $this->formatRulesArray($rules);
        $messagesArray = $this->formatMessagesArray();

        return "<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class {$className} extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return {$rulesArray};
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return {$messagesArray};
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
" . $this->generateAttributeNames($rules) . "
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Add any data preparation logic here
        // Example: Convert empty strings to null
        \$this->merge([
            // Add any automatic data transformations
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(\$validator): void
    {
        \$validator->after(function (\$validator) {
            // Add any custom validation logic here
        });
    }
}
";
    }

    private function formatRulesArray($rules)
    {
        $formatted = "[\n";
        foreach ($rules as $field => $rule) {
            $formatted .= "            '{$field}' => '{$rule}',\n";
        }
        $formatted .= "        ]";
        return $formatted;
    }

    private function formatMessagesArray()
    {
        $formatted = "[\n";
        foreach ($this->messagesTemplate as $rule => $message) {
            $formatted .= "            '{$rule}' => '{$message}',\n";
        }
        $formatted .= "        ]";
        return $formatted;
    }

    private function generateAttributeNames($rules)
    {
        $attributes = "";
        foreach (array_keys($rules) as $field) {
            $label = ucwords(str_replace('_', ' ', $field));
            $attributes .= "            '{$field}' => '{$label}',\n";
        }
        return rtrim($attributes, "\n");
    }
}

// Run the generator
if (php_sapi_name() === 'cli') {
    $generator = new RequestFileGenerator();
    $generator->generateAllRequestFiles();
    
    echo "🎉 REQUEST FILE GENERATION COMPLETE!\n";
    echo "=====================================\n";
    echo "✅ Created comprehensive validation for:\n";
    echo "   - Admin Management\n";
    echo "   - Candidate Management\n";
    echo "   - Job Management\n";
    echo "   - Company Management\n";
    echo "   - Contact Forms\n";
    echo "   - Authentication\n";
    echo "   - Settings\n";
    echo "   - Job Applications\n";
    echo "   - Blog/Posts\n\n";
    echo "📁 Files created in: app/Http/Requests/\n";
    echo "🔧 Next: Update controllers to use these request files\n\n";
} 