<?php

namespace App\Http\Requests\Api;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;

/**
 * Comprehensive Form Request for API User Access
 * Implements Laravel 12 best practices with secure API validation.
 */
class ShowUserApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Comprehensive API authorization with rate limiting.
     */
    public function authorize(): bool
    {
        // Check if user is authenticated via API token or session
        if (!Auth::check() && !$this->hasValidApiToken()) {
            return false;
        }
        
        // Apply rate limiting for API requests
        if ($this->isApiRequest() && $this->exceedsRateLimit()) {
            return false;
        }
        
        $user = Auth::user();
        $targetUserId = $this->route('user') ?? $this->input('user_id');
        
        if (!$user) {
            return false;
        }

        // Admin can view any user
        if ($user->hasRole('Admin') || $user->hasRole('Super Admin')) {
            return true;
        }
        
        // HR managers can view candidate profiles
        if ($user->hasRole('HR Manager') && $this->isTargetCandidate($targetUserId)) {
            return true;
        }
        
        // Employers can view candidates who applied to their jobs
        if ($user->hasRole('Employer') && $this->canEmployerViewUser($user, $targetUserId)) {
            return true;
        }
        
        // Users can view their own profile
        if ($targetUserId && (int) $targetUserId === $user->id) {
            return true;
        }
        
        // Users can view public profiles
        return $this->isPublicProfile($targetUserId);
    }

    /**
     * Get the validation rules that apply to the request.
     * Comprehensive API parameter validation.
     */
    public function rules(): array
    {
        return [
            // User identification
            'user_id' => [
                'sometimes',
                'integer',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $user = User::find($value);
                    if ($user && $user->is_suspended) {
                        $fail(__('api.validation.user_suspended'));
                    }
                    if ($user && !$user->is_active) {
                        $fail(__('api.validation.user_inactive'));
                    }
                },
            ],

            // Response formatting options
            'format' => [
                'sometimes',
                'string',
                Rule::in(['json', 'xml', 'minimal', 'full']),
            ],
            
            'fields' => [
                'sometimes',
                'array',
                'max:50',
            ],
            'fields.*' => [
                'string',
                'max:50',
                Rule::in([
                    'id', 'name', 'email', 'phone', 'avatar', 'bio', 'location',
                    'skills', 'experience', 'education', 'certifications',
                    'social_links', 'portfolio', 'availability', 'preferences'
                ]),
                'distinct',
            ],

            // Pagination and filtering
            'include' => [
                'sometimes',
                'array',
                'max:20',
            ],
            'include.*' => [
                'string',
                Rule::in([
                    'profile', 'skills', 'experience', 'education', 'applications',
                    'reviews', 'portfolio', 'certifications', 'social_links'
                ]),
                'distinct',
            ],

            // Security and tracking
            'purpose' => [
                'sometimes',
                'string',
                'max:100',
                Rule::in(['profile_view', 'contact_info', 'recruitment', 'networking', 'verification']),
            ],
            
            'client_id' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9\-\_]+$/',
            ],
            
            'source' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9\-\_\.]+$/',
            ],

            // Cache control
            'cache' => [
                'sometimes',
                'boolean',
            ],
            
            'fresh' => [
                'sometimes',
                'boolean',
            ],

            // API versioning
            'version' => [
                'sometimes',
                'string',
                Rule::in(['v1', 'v2', 'latest']),
            ],

            // Localization
            'locale' => [
                'sometimes',
                'string',
                'size:2',
                Rule::in(['en', 'ar', 'es', 'fr', 'de', 'pt', 'ru', 'tr', 'zh']),
            ],
            
            'timezone' => [
                'sometimes',
                'string',
                'max:50',
                'regex:/^[A-Za-z_\/\+\-0-9]+$/',
            ],

            // Rate limiting bypass (for privileged clients)
            'priority' => [
                'sometimes',
                'string',
                Rule::in(['low', 'normal', 'high']),
            ],

            // Debug and monitoring
            'debug' => [
                'sometimes',
                'boolean',
                function ($attribute, $value, $fail) {
                    if ($value && !Auth::user()?->hasRole('Admin')) {
                        $fail(__('api.validation.debug_not_authorized'));
                    }
                },
            ],

            // Callback and webhook
            'callback_url' => [
                'sometimes',
                'url',
                'max:500',
                'regex:/^https:\/\//',
            ],

            // Request metadata
            'metadata' => [
                'sometimes',
                'array',
                'max:20',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     * Multilingual API error messages.
     */
    public function messages(): array
    {
        return [
            // User validation
            'user_id.integer' => __('api.validation.user_id_invalid'),
            'user_id.exists' => __('api.validation.user_not_found'),
            
            // Format validation
            'format.in' => __('api.validation.format_invalid'),
            'fields.array' => __('api.validation.fields_must_be_array'),
            'fields.max' => __('api.validation.too_many_fields'),
            'fields.*.in' => __('api.validation.invalid_field_name'),
            'fields.*.distinct' => __('api.validation.duplicate_fields'),
            
            // Include validation
            'include.array' => __('api.validation.include_must_be_array'),
            'include.max' => __('api.validation.too_many_includes'),
            'include.*.in' => __('api.validation.invalid_include'),
            'include.*.distinct' => __('api.validation.duplicate_includes'),
            
            // Security validation
            'purpose.in' => __('api.validation.purpose_invalid'),
            'client_id.regex' => __('api.validation.client_id_format'),
            'source.regex' => __('api.validation.source_format'),
            
            // Localization
            'locale.size' => __('api.validation.locale_size'),
            'locale.in' => __('api.validation.locale_invalid'),
            'timezone.regex' => __('api.validation.timezone_format'),
            
            // Callback validation
            'callback_url.url' => __('api.validation.callback_url_invalid'),
            'callback_url.regex' => __('api.validation.callback_url_https'),
            
            // Version validation
            'version.in' => __('api.validation.version_invalid'),
            'priority.in' => __('api.validation.priority_invalid'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * User-friendly API field names.
     */
    public function attributes(): array
    {
        return [
            'user_id' => __('api.fields.user_id'),
            'format' => __('api.fields.response_format'),
            'fields' => __('api.fields.requested_fields'),
            'include' => __('api.fields.include_relations'),
            'purpose' => __('api.fields.request_purpose'),
            'client_id' => __('api.fields.client_identifier'),
            'source' => __('api.fields.request_source'),
            'locale' => __('api.fields.language'),
            'timezone' => __('api.fields.timezone'),
            'version' => __('api.fields.api_version'),
            'priority' => __('api.fields.request_priority'),
            'callback_url' => __('api.fields.callback_url'),
            'metadata' => __('api.fields.request_metadata'),
        ];
    }

    /**
     * Configure the validator instance.
     * Enhanced API validation logic.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Check for conflicting parameters
            if ($this->hasConflictingParameters()) {
                $validator->errors()->add('format', __('api.validation.conflicting_parameters'));
            }
            
            // Validate field access permissions
            if ($this->hasUnauthorizedFieldAccess()) {
                $validator->errors()->add('fields', __('api.validation.unauthorized_field_access'));
            }
            
            // Check API quota limits
            if ($this->exceedsApiQuota()) {
                $validator->errors()->add('general', __('api.validation.quota_exceeded'));
            }

            // Validate request rate and patterns
            if ($this->hasSuspiciousRequestPattern()) {
                $validator->errors()->add('general', __('api.validation.suspicious_pattern'));
            }
        });
    }

    /**
     * Prepare the data for validation.
     * API data normalization.
     */
    protected function prepareForValidation(): void
    {
        // Set default values for API requests
        $this->merge([
            'format' => $this->format ?? 'json',
            'version' => $this->version ?? 'v1',
            'locale' => $this->locale ?? 'en',
            'cache' => filter_var($this->cache ?? true, FILTER_VALIDATE_BOOLEAN),
            'fresh' => filter_var($this->fresh ?? false, FILTER_VALIDATE_BOOLEAN),
            'debug' => filter_var($this->debug ?? false, FILTER_VALIDATE_BOOLEAN),
            'priority' => $this->priority ?? 'normal',
        ]);
        
        // Normalize arrays
        if ($this->filled('fields')) {
            $this->merge([
                'fields' => array_filter(array_unique((array) $this->fields)),
            ]);
        }
        
        if ($this->filled('include')) {
            $this->merge([
                'include' => array_filter(array_unique((array) $this->include)),
            ]);
        }
        
        // Add request tracking data
        $this->merge([
            '_request_id' => $this->header('X-Request-ID') ?? \Str::uuid(),
            '_ip_address' => $this->ip(),
            '_user_agent' => $this->userAgent(),
            '_timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Handle a failed validation attempt.
     * Enhanced API error logging.
     */
    protected function failedValidation(Validator $validator): void
    {
        \Log::warning('API user access validation failed', [
            'errors' => $validator->errors()->toArray(),
            'request_data' => $this->except(['password', 'token']),
            'user_id' => Auth::id(),
            'target_user_id' => $this->route('user') ?? $this->input('user_id'),
            'ip_address' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'api_endpoint' => $this->url(),
            'request_method' => $this->method(),
            'request_id' => $this->input('_request_id'),
            'timestamp' => now()->toISOString(),
        ]);

        parent::failedValidation($validator);
    }

    /**
     * Get processed data for API response.
     */
    public function getApiParameters(): array
    {
        $data = $this->validated();
        
        // Add computed parameters
        $data['_computed'] = [
            'is_api_request' => $this->isApiRequest(),
            'authenticated_user_id' => Auth::id(),
            'request_timestamp' => now(),
            'rate_limit_remaining' => $this->getRateLimitRemaining(),
        ];
        
        return $data;
    }

    /**
     * Check if request has valid API token.
     */
    private function hasValidApiToken(): bool
    {
        $token = $this->bearerToken() ?? $this->input('api_token');
        
        if (!$token) {
            return false;
        }
        
        // Validate token format and check against database
        return \Str::length($token) >= 40 && $this->validateApiToken($token);
    }

    /**
     * Check if this is an API request.
     */
    private function isApiRequest(): bool
    {
        return $this->is('api/*') || 
               $this->expectsJson() || 
               $this->hasHeader('X-API-Key') ||
               $this->hasHeader('Authorization');
    }

    /**
     * Check rate limiting for API requests.
     */
    private function exceedsRateLimit(): bool
    {
        $key = 'api_rate_limit:' . ($this->user()?->id ?? $this->ip());
        
        return RateLimiter::tooManyAttempts($key, 100); // 100 requests per minute
    }

    /**
     * Check if target user is a candidate.
     */
    private function isTargetCandidate(int $targetUserId): bool
    {
        return User::where('id', $targetUserId)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Candidate');
            })
            ->exists();
    }

    /**
     * Check if employer can view specific user.
     */
    private function canEmployerViewUser(User $employer, int $targetUserId): bool
    {
        // Check if candidate applied to employer's jobs
        return \DB::table('job_applications')
            ->join('jobs', 'job_applications.job_id', '=', 'jobs.id')
            ->where('jobs.employer_id', $employer->id)
            ->where('job_applications.candidate_id', $targetUserId)
            ->exists();
    }

    /**
     * Check if profile is public.
     */
    private function isPublicProfile(int $targetUserId): bool
    {
        return User::where('id', $targetUserId)
            ->where('profile_visibility', 'public')
            ->exists();
    }

    /**
     * Check for conflicting parameters.
     */
    private function hasConflictingParameters(): bool
    {
        // Check if minimal format conflicts with detailed fields
        if ($this->format === 'minimal' && 
            $this->filled('fields') && 
            count($this->fields) > 5) {
            return true;
        }
        
        // Check if fresh and cache are both true
        if ($this->fresh === true && $this->cache === true) {
            return true;
        }
        
        return false;
    }

    /**
     * Check for unauthorized field access.
     */
    private function hasUnauthorizedFieldAccess(): bool
    {
        $requestedFields = $this->fields ?? [];
        $sensitiveFields = ['email', 'phone', 'social_links'];
        
        $user = Auth::user();
        if (!$user || !$user->hasRole(['Admin', 'HR Manager'])) {
            $unauthorizedFields = array_intersect($requestedFields, $sensitiveFields);
            return !empty($unauthorizedFields);
        }

        return false;
    }

    /**
     * Check API quota limits.
     */
    private function exceedsApiQuota(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        
        $quotaKey = 'api_quota:' . $user->id . ':' . now()->format('Y-m-d');
        $currentUsage = Cache::get($quotaKey, 0);
        
        // Different quotas based on user role
        $dailyQuota = match (true) {
            $user->hasRole('Admin') => 10000,
            $user->hasRole('HR Manager') => 5000,
            $user->hasRole('Employer') => 1000,
            default => 100
        };
        
        return $currentUsage >= $dailyQuota;
    }

    /**
     * Detect suspicious request patterns.
     */
    private function hasSuspiciousRequestPattern(): bool
    {
        $ip = $this->ip();
        $pattern = 'suspicious_api:' . $ip;
        
        // Check for rapid successive requests
        $recentRequests = Cache::get($pattern, 0);
        if ($recentRequests > 50) { // More than 50 requests in last minute
            return true;
        }
        
        Cache::put($pattern, $recentRequests + 1, 60); // Store for 1 minute
        
        return false;
    }

    /**
     * Validate API token against database.
     */
    private function validateApiToken(string $token): bool
    {
        // This would check against personal access tokens table
        return \DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $token))
            ->where('expires_at', '>', now())
            ->exists();
    }

    /**
     * Get remaining rate limit for user.
     */
    private function getRateLimitRemaining(): int
    {
        $key = 'api_rate_limit:' . ($this->user()?->id ?? $this->ip());
        return RateLimiter::remaining($key, 100);
    }
}
