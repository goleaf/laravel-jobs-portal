<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * Universal Form Request for storing Noticeboard
 * Implements Laravel 12 best practices with Universal MCP patterns.
 */
class StoreNoticeboardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Universal Pattern: Authorization check.
     */
    public function authorize(): bool
    {
        // Only admin and managers can create notices
        return Auth::check() && (
            Auth::user()->hasRole('admin') ||
            Auth::user()->hasRole('manager') ||
            Auth::user()->can('create-notices')
        );
    }

    /**
     * Get the validation rules that apply to the request.
     * Universal Pattern: Comprehensive validation rules.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            // Notice title
            'title' => [
                'required',
                'string',
                'min:5',
                'max:200',
                'regex:/^[\p{L}\p{N}\s\-\.\?\!\,\:\;\(\)]+$/u',
                Rule::unique('noticeboards', 'title')->whereNull('deleted_at'),
            ],

            // Notice content/description
            'description' => [
                'required',
                'string',
                'min:10',
                'max:5000',
            ],

            // Notice type/category
            'type' => [
                'required',
                'string',
                Rule::in(['announcement', 'news', 'policy', 'update', 'maintenance', 'event', 'general']),
            ],

            // Priority level
            'priority' => [
                'sometimes',
                'string',
                Rule::in(['low', 'normal', 'high', 'urgent']),
            ],

            // Target audience
            'target_audience' => [
                'sometimes',
                'string',
                Rule::in(['all', 'companies', 'candidates', 'admin', 'public']),
            ],

            // Status
            'status' => [
                'sometimes',
                'string',
                Rule::in(['draft', 'published', 'archived', 'scheduled']),
            ],

            // Publication date
            'published_at' => [
                'sometimes',
                'date',
                'after:now',
                function ($attribute, $value, $fail) {
                    if ($this->status === 'scheduled' && ! $value) {
                        $fail(__('validation.required_when_scheduled'));
                    }
                },
            ],

            // Expiration date
            'expires_at' => [
                'sometimes',
                'date',
                'after:published_at',
                'after:now',
            ],

            // Is pinned/featured
            'is_pinned' => [
                'sometimes',
                'boolean',
            ],

            // Requires acknowledgment
            'requires_acknowledgment' => [
                'sometimes',
                'boolean',
            ],

            // Email notification
            'send_email_notification' => [
                'sometimes',
                'boolean',
            ],

            // Tags for categorization
            'tags' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'tags.*' => [
                'string',
                'min:2',
                'max:30',
                'regex:/^[a-zA-Z0-9\s\-]+$/',
            ],

            // Attachments/files
            'attachments' => [
                'sometimes',
                'array',
                'max:5',
            ],

            'attachments.*' => [
                'file',
                'max:10240', // 10MB
                'mimes:pdf,doc,docx,jpg,jpeg,png,gif,txt',
            ],

            // Display settings
            'display_order' => [
                'sometimes',
                'integer',
                'min:0',
                'max:999',
            ],

            // Department/division specific
            'department' => [
                'sometimes',
                'string',
                'max:100',
            ],

            // Language for multilingual support
            'language' => [
                'sometimes',
                'string',
                'size:2',
                'exists:languages,code',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     * Universal Pattern: Multilingual error messages.
     */
    public function messages(): array
    {
        return [
            'title.required' => __('validation.required_field', ['field' => __('validation.attributes.notice_title')]),
            'title.unique' => __('validation.unique_field', ['field' => __('validation.attributes.notice_title')]),
            'title.regex' => __('validation.notice_title_format'),

            'description.required' => __('validation.required_field', ['field' => __('validation.attributes.description')]),
            'description.min' => __('validation.min_chars', ['attribute' => __('validation.attributes.description'), 'min' => 10]),
            'description.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.description'), 'max' => 5000]),

            'type.required' => __('validation.required_field', ['field' => __('validation.attributes.notice_type')]),
            'type.in' => __('validation.in_list', ['attribute' => __('validation.attributes.notice_type')]),

            'priority.in' => __('validation.in_list', ['attribute' => __('validation.attributes.priority')]),
            'target_audience.in' => __('validation.in_list', ['attribute' => __('validation.attributes.target_audience')]),
            'status.in' => __('validation.in_list', ['attribute' => __('validation.attributes.status')]),

            'published_at.after' => __('validation.future_date', ['attribute' => __('validation.attributes.published_at')]),
            'expires_at.after' => __('validation.after_field', ['attribute' => __('validation.attributes.expires_at'), 'after' => __('validation.attributes.published_at')]),

            'tags.max' => __('validation.max_items', ['attribute' => __('validation.attributes.tags'), 'max' => 10]),
            'tags.*.regex' => __('validation.tag_format'),

            'attachments.max' => __('validation.max_files', ['attribute' => __('validation.attributes.attachments'), 'max' => 5]),
            'attachments.*.max' => __('validation.max_file_size', ['attribute' => __('validation.attributes.attachment'), 'max' => '10MB']),
            'attachments.*.mimes' => __('validation.file_types', ['attribute' => __('validation.attributes.attachment')]),

            'language.exists' => __('validation.exists', ['attribute' => __('validation.attributes.language')]),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * Universal Pattern: User-friendly field names.
     */
    public function attributes(): array
    {
        return [
            'title' => __('validation.attributes.notice_title'),
            'description' => __('validation.attributes.description'),
            'type' => __('validation.attributes.notice_type'),
            'priority' => __('validation.attributes.priority'),
            'target_audience' => __('validation.attributes.target_audience'),
            'status' => __('validation.attributes.status'),
            'published_at' => __('validation.attributes.published_at'),
            'expires_at' => __('validation.attributes.expires_at'),
            'is_pinned' => __('validation.attributes.is_pinned'),
            'requires_acknowledgment' => __('validation.attributes.requires_acknowledgment'),
            'send_email_notification' => __('validation.attributes.send_email_notification'),
            'tags' => __('validation.attributes.tags'),
            'attachments' => __('validation.attributes.attachments'),
            'display_order' => __('validation.attributes.display_order'),
            'department' => __('validation.attributes.department'),
            'language' => __('validation.attributes.language'),
        ];
    }

    /**
     * Configure the validator instance.
     * Universal Pattern: Enhanced validation logic.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Universal Pattern: Additional business logic validation
            if ($this->hasConflictingData()) {
                $validator->errors()->add('name', __('validation.conflicting_data'));
            }
        });
    }

    /**
     * Prepare the data for validation.
     * Universal Pattern: Data normalization.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'status' => $this->status ?? 'draft',
            'priority' => $this->priority ?? 'normal',
            'target_audience' => $this->target_audience ?? 'all',
            'is_pinned' => $this->boolean('is_pinned', false),
            'requires_acknowledgment' => $this->boolean('requires_acknowledgment', false),
            'send_email_notification' => $this->boolean('send_email_notification', false),
            'display_order' => $this->display_order ?? 0,
            'language' => $this->language ?? 'en',
        ]);

        // Clean title and description
        if ($this->has('title')) {
            $this->merge([
                'title' => trim($this->title),
            ]);
        }

        if ($this->has('description')) {
            $this->merge([
                'description' => trim($this->description),
            ]);
        }

        // Process tags
        if ($this->has('tags') && is_array($this->tags)) {
            $this->merge([
                'tags' => array_map('trim', array_filter($this->tags)),
            ]);
        }

        // Auto-set published_at for immediate publication
        if ($this->status === 'published' && ! $this->has('published_at')) {
            $this->merge([
                'published_at' => now(),
            ]);
        }

        // Log notice creation attempt
        Log::info('Notice creation attempt', [
            'title' => $this->title ?? null,
            'type' => $this->type ?? null,
            'priority' => $this->priority ?? null,
            'user_id' => Auth::id(),
            'ip' => $this->ip(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Handle a failed validation attempt.
     * Universal Pattern: Enhanced error handling.
     */
    protected function failedValidation(Validator $validator): void
    {
        logger()->info('Store validation failed for StoreNoticeboardRequest', [
            'errors' => $validator->errors()->toArray(),
            'input' => $this->safe()->toArray(),
            'user_id' => $this->user()?->id,
            'ip' => $this->ip(),
        ]);

        parent::failedValidation($validator);
    }

    /**
     * Universal Pattern: Custom business logic check.
     */
    private function hasConflictingData(): bool
    {
        // Add specific business logic here
        return false;
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Set creator information
        $this->merge([
            'created_by' => Auth::id(),
            'validated_at' => now(),
        ]);

        // Generate slug from title
        if ($this->has('title')) {
            $this->merge([
                'slug' => \Str::slug($this->title).'-'.time(),
            ]);
        }
    }
}
