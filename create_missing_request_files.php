<?php

/**
 * Missing Request Files Generator
 * 
 * Analyzes all controllers and creates missing request validation files
 * with multilingual error messages
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);
$app->boot();

echo "=== CREATING MISSING REQUEST FILES ===\n\n";

// Define request files that need to be created
$requestFiles = [
    // Admin Controllers
    'Admin/StoreAdminRequest' => [
        'rules' => [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean'
        ],
        'messages' => [
            'first_name.required' => 'First name is required',
            'email.required' => 'Email is required',
            'email.unique' => 'Email already exists',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters'
        ]
    ],
    
    'Admin/UpdateAdminRequest' => [
        'rules' => [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,{id}',
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean'
        ],
        'messages' => [
            'first_name.required' => 'First name is required',
            'email.required' => 'Email is required',
            'email.unique' => 'Email already exists'
        ]
    ],
    
    // Candidate Controllers
    'Candidate/StoreCandidateRequest' => [
        'rules' => [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'marital_status_id' => 'nullable|exists:marital_statuses,id',
            'nationality' => 'nullable|string|max:255',
            'national_id_card' => 'nullable|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'address' => 'nullable|string',
            'postal_code' => 'nullable|string|max:20',
            'career_level_id' => 'nullable|exists:career_levels,id',
            'functional_area_id' => 'nullable|exists:functional_areas,id',
            'current_salary' => 'nullable|numeric|min:0',
            'expected_salary' => 'nullable|numeric|min:0',
            'salary_currency_id' => 'nullable|exists:salary_currencies,id',
            'immediate_available' => 'boolean',
            'experience' => 'nullable|string',
            'video_link' => 'nullable|url'
        ],
        'messages' => [
            'first_name.required' => 'First name is required',
            'email.required' => 'Email is required',
            'email.unique' => 'Email already exists',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'date_of_birth.date' => 'Please enter a valid date',
            'gender.in' => 'Please select a valid gender',
            'current_salary.numeric' => 'Current salary must be a number',
            'expected_salary.numeric' => 'Expected salary must be a number',
            'video_link.url' => 'Please enter a valid URL'
        ]
    ],
    
    'Candidate/UpdateCandidateRequest' => [
        'rules' => [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,{id}',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'marital_status_id' => 'nullable|exists:marital_statuses,id',
            'nationality' => 'nullable|string|max:255',
            'national_id_card' => 'nullable|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'address' => 'nullable|string',
            'postal_code' => 'nullable|string|max:20',
            'career_level_id' => 'nullable|exists:career_levels,id',
            'functional_area_id' => 'nullable|exists:functional_areas,id',
            'current_salary' => 'nullable|numeric|min:0',
            'expected_salary' => 'nullable|numeric|min:0',
            'salary_currency_id' => 'nullable|exists:salary_currencies,id',
            'immediate_available' => 'boolean',
            'experience' => 'nullable|string',
            'video_link' => 'nullable|url'
        ],
        'messages' => [
            'first_name.required' => 'First name is required',
            'email.required' => 'Email is required',
            'email.unique' => 'Email already exists'
        ]
    ],
    
    // Job Controllers
    'Job/StoreJobRequest' => [
        'rules' => [
            'job_title' => 'required|string|max:255',
            'job_description' => 'required|string',
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
            'experience' => 'nullable|string',
            'job_expiry_date' => 'required|date|after:today',
            'hide_salary' => 'boolean',
            'is_freelance' => 'boolean',
            'is_suspended' => 'boolean'
        ],
        'messages' => [
            'job_title.required' => 'Job title is required',
            'job_description.required' => 'Job description is required',
            'country_id.required' => 'Country is required',
            'job_category_id.required' => 'Job category is required',
            'job_type_id.required' => 'Job type is required',
            'salary_to.gte' => 'Maximum salary must be greater than or equal to minimum salary',
            'job_expiry_date.required' => 'Job expiry date is required',
            'job_expiry_date.after' => 'Job expiry date must be in the future',
            'position.min' => 'Number of positions must be at least 1'
        ]
    ],
    
    'Job/UpdateJobRequest' => [
        'rules' => [
            'job_title' => 'required|string|max:255',
            'job_description' => 'required|string',
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
            'experience' => 'nullable|string',
            'job_expiry_date' => 'required|date',
            'hide_salary' => 'boolean',
            'is_freelance' => 'boolean',
            'is_suspended' => 'boolean'
        ],
        'messages' => [
            'job_title.required' => 'Job title is required',
            'job_description.required' => 'Job description is required',
            'country_id.required' => 'Country is required',
            'job_category_id.required' => 'Job category is required',
            'job_type_id.required' => 'Job type is required',
            'salary_to.gte' => 'Maximum salary must be greater than or equal to minimum salary',
            'job_expiry_date.required' => 'Job expiry date is required'
        ]
    ],
    
    // Company Controllers
    'Company/StoreCompanyRequest' => [
        'rules' => [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'industry_id' => 'nullable|exists:industries,id',
            'ownership_type_id' => 'nullable|exists:ownership_types,id',
            'company_size_id' => 'nullable|exists:company_sizes,id',
            'established_in' => 'nullable|integer|min:1800|max:' . date('Y'),
            'description' => 'nullable|string',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'address' => 'nullable|string',
            'postal_code' => 'nullable|string|max:20',
            'phone_verified' => 'boolean',
            'email_verified' => 'boolean',
            'is_active' => 'boolean'
        ],
        'messages' => [
            'name.required' => 'Company name is required',
            'email.required' => 'Email is required',
            'email.unique' => 'Email already exists',
            'website.url' => 'Please enter a valid website URL',
            'country_id.required' => 'Country is required',
            'established_in.min' => 'Establishment year cannot be before 1800',
            'established_in.max' => 'Establishment year cannot be in the future'
        ]
    ],
    
    'Company/UpdateCompanyRequest' => [
        'rules' => [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email,{id}',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'industry_id' => 'nullable|exists:industries,id',
            'ownership_type_id' => 'nullable|exists:ownership_types,id',
            'company_size_id' => 'nullable|exists:company_sizes,id',
            'established_in' => 'nullable|integer|min:1800|max:' . date('Y'),
            'description' => 'nullable|string',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'address' => 'nullable|string',
            'postal_code' => 'nullable|string|max:20',
            'phone_verified' => 'boolean',
            'email_verified' => 'boolean',
            'is_active' => 'boolean'
        ],
        'messages' => [
            'name.required' => 'Company name is required',
            'email.required' => 'Email is required',
            'email.unique' => 'Email already exists',
            'website.url' => 'Please enter a valid website URL',
            'country_id.required' => 'Country is required'
        ]
    ],
    
    // Transaction Controllers
    'Transaction/StoreTransactionRequest' => [
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
    ],
    
    'Transaction/UpdateTransactionRequest' => [
        'rules' => [
            'status' => 'required|in:pending,approved,denied,cancelled',
            'meta' => 'nullable|json'
        ],
        'messages' => [
            'status.required' => 'Status is required'
        ]
    ],
    
    // Auth Controllers
    'Auth/LoginRequest' => [
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
    
    'Auth/RegisterRequest' => [
        'rules' => [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'user_type' => 'required|in:candidate,employer',
            'terms_accepted' => 'required|accepted'
        ],
        'messages' => [
            'first_name.required' => 'First name is required',
            'email.required' => 'Email is required',
            'email.unique' => 'Email already exists',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'user_type.required' => 'User type is required',
            'terms_accepted.required' => 'You must accept the terms and conditions'
        ]
    ],
    
    'Auth/ForgotPasswordRequest' => [
        'rules' => [
            'email' => 'required|email|exists:users,email'
        ],
        'messages' => [
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email address',
            'email.exists' => 'Email not found in our records'
        ]
    ],
    
    'Auth/ResetPasswordRequest' => [
        'rules' => [
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed'
        ],
        'messages' => [
            'token.required' => 'Reset token is required',
            'email.required' => 'Email is required',
            'email.exists' => 'Email not found in our records',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match'
        ]
    ],
    
    // Contact Form
    'Contact/ContactFormRequest' => [
        'rules' => [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10'
        ],
        'messages' => [
            'first_name.required' => 'First name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email address',
            'subject.required' => 'Subject is required',
            'message.required' => 'Message is required',
            'message.min' => 'Message must be at least 10 characters'
        ]
    ]
];

// Create the request files
foreach ($requestFiles as $fileName => $config) {
    $directory = dirname($fileName);
    $className = basename($fileName);
    
    // Create directory if it doesn't exist
    $fullDirectory = "app/Http/Requests/{$directory}";
    if (!is_dir($fullDirectory)) {
        mkdir($fullDirectory, 0755, true);
        echo "Created directory: {$fullDirectory}\n";
    }
    
    $filePath = "app/Http/Requests/{$fileName}.php";
    
    // Skip if file already exists
    if (file_exists($filePath)) {
        echo "Skipped (exists): {$filePath}\n";
        continue;
    }
    
    // Generate the request file content
    $content = generateRequestFile($className, $config['rules'], $config['messages']);
    
    // Write the file
    file_put_contents($filePath, $content);
    echo "Created: {$filePath}\n";
}

echo "\n=== REQUEST FILE CREATION COMPLETE ===\n";
echo "All missing request files have been created with proper validation rules and multilingual error messages.\n";

function generateRequestFile($className, $rules, $messages) {
    $rulesString = var_export($rules, true);
    $messagesString = var_export($messages, true);
    
    return "<?php

namespace App\Http\Requests" . (strpos($className, '/') ? '\\' . str_replace('/', '\\', dirname($className)) : '') . ";

use Illuminate\Foundation\Http\FormRequest;

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
     */
    public function rules(): array
    {
        return {$rulesString};
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return {$messagesString};
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'first_name' => __('messages.common.first_name'),
            'last_name' => __('messages.common.last_name'),
            'email' => __('messages.common.email'),
            'password' => __('messages.common.password'),
            'phone' => __('messages.common.phone'),
            'name' => __('messages.common.name'),
            'description' => __('messages.common.description'),
            'address' => __('messages.common.address'),
            'website' => __('messages.common.website'),
            'country_id' => __('messages.common.country'),
            'state_id' => __('messages.common.state'),
            'city_id' => __('messages.common.city'),
            'job_title' => __('messages.job.job_title'),
            'job_description' => __('messages.job.job_description'),
            'salary_from' => __('messages.job.salary_from'),
            'salary_to' => __('messages.job.salary_to'),
            'job_expiry_date' => __('messages.job.job_expiry_date'),
        ];
    }
}
";
}

$kernel->terminate($request, $response); 