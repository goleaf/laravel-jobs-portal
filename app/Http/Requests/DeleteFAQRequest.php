<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * Universal Form Request for deleting FAQ
 * Implements Laravel 12 best practices with Universal MCP patterns.
 */
class DeleteFAQRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Universal Pattern: Resource-based authorization.
     */
    public function authorize(): bool
    {
        // Only admin and content managers can delete FAQs
        return true; // Based on user requirements: no auth system
    }

    /**
     * Get the validation rules that apply to the request.
     * Universal Pattern: Delete-specific validation rules.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            // FAQ ID - required for deletion
            'id' => [
                'required',
                'integer',
                'min:1',
                'exists:faqs,id',
                function ($attribute, $value, $fail) {
                    if (!$this->validateFAQDeletable($value)) {
                        $fail(__('validation.faq_cannot_be_deleted'));
                    }
                },
            ],

            // Confirmation field
            'confirm_deletion' => [
                'required',
                'boolean',
                'accepted',
            ],

            // Reason for deletion
            'deletion_reason' => [
                'required',
                'string',
                'min:5',
                'max:500',
                Rule::in([
                    'outdated_information',
                    'duplicate_content',
                    'policy_change',
                    'content_restructure',
                    'user_feedback',
                    'compliance_issue',
                    'accuracy_concern',
                    'redundant_content',
                    'other'
                ]),
            ],

            // Replacement FAQ (if applicable)
            'replacement_faq_id' => [
                'sometimes',
                'integer',
                'min:1',
                'exists:faqs,id',
                'different:id',
            ],

            // Content archival options
            'archive_content' => [
                'sometimes',
                'boolean',
            ],

            'backup_before_delete' => [
                'sometimes',
                'boolean',
            ],

            // Notification settings
            'notify_users' => [
                'sometimes',
                'boolean',
            ],

            'notification_message' => [
                'required_if:notify_users,true',
                'string',
                'min:10',
                'max:500',
            ],

            // Content migration
            'migrate_to_category' => [
                'sometimes',
                'integer',
                'exists:faq_categories,id',
            ],

            'update_related_content' => [
                'sometimes',
                'boolean',
            ],

            // SEO and URL handling
            'redirect_url' => [
                'sometimes',
                'url',
                'max:255',
            ],

            'update_sitemap' => [
                'sometimes',
                'boolean',
            ],

            // Admin notes
            'admin_notes' => [
                'sometimes',
                'string',
                'max:1000',
            ],

            // Approval workflow
            'requires_approval' => [
                'sometimes',
                'boolean',
            ],

            'approved_by' => [
                'required_if:requires_approval,false',
                'string',
                'max:100',
            ],

            // Impact assessment
            'view_count' => [
                'sometimes',
                'integer',
                'min:0',
            ],

            'helpfulness_rating' => [
                'sometimes',
                'numeric',
                'between:0,5',
            ],

            // Content analysis
            'content_category' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'tags' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'tags.*' => [
                'string',
                'max:50',
            ],

            // Effective deletion date
            'effective_date' => [
                'sometimes',
                'date',
                'after:now',
                'before:' . now()->addMonths(6)->toDateString(),
            ],

            // Content preservation
            'preserve_analytics' => [
                'sometimes',
                'boolean',
            ],

            'export_content' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     * Universal Pattern: Delete operation messages.
     */
    public function messages(): array
    {
        return [
            'id.required' => __('validation.required_field', ['field' => __('validation.attributes.faq_id')]),
            'id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.faq')]),
            
            'confirm_deletion.required' => __('validation.required_field', ['field' => __('validation.attributes.confirm_deletion')]),
            'confirm_deletion.accepted' => __('validation.must_confirm_deletion'),
            
            'deletion_reason.required' => __('validation.required_field', ['field' => __('validation.attributes.deletion_reason')]),
            'deletion_reason.min' => __('validation.min_chars', ['attribute' => __('validation.attributes.deletion_reason'), 'min' => 5]),
            'deletion_reason.in' => __('validation.invalid_deletion_reason'),
            
            'replacement_faq_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.replacement_faq')]),
            'replacement_faq_id.different' => __('validation.replacement_faq_different'),
            
            'notification_message.required_if' => __('validation.required_when_notify'),
            'notification_message.min' => __('validation.min_chars', ['attribute' => __('validation.attributes.notification_message'), 'min' => 10]),
            
            'migrate_to_category.exists' => __('validation.exists', ['attribute' => __('validation.attributes.faq_category')]),
            
            'redirect_url.url' => __('validation.valid_url', ['attribute' => __('validation.attributes.redirect_url')]),
            
            'effective_date.after' => __('validation.future_date', ['attribute' => __('validation.attributes.effective_date')]),
            'effective_date.before' => __('validation.within_six_months', ['attribute' => __('validation.attributes.effective_date')]),
            
            'approved_by.required_if' => __('validation.approval_required'),
            
            'helpfulness_rating.between' => __('validation.rating_range', ['attribute' => __('validation.attributes.helpfulness_rating')]),
            
            'tags.max' => __('validation.max_items', ['attribute' => __('validation.attributes.tags'), 'max' => 10]),
            'tags.*.max' => __('validation.max_chars', ['attribute' => __('validation.attributes.tag'), 'max' => 50]),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * Universal Pattern: User-friendly field names.
     */
    public function attributes(): array
    {
        return [
            'id' => __('validation.attributes.faq_id'),
            'confirm_deletion' => __('validation.attributes.confirm_deletion'),
            'deletion_reason' => __('validation.attributes.deletion_reason'),
            'replacement_faq_id' => __('validation.attributes.replacement_faq'),
            'archive_content' => __('validation.attributes.archive_content'),
            'backup_before_delete' => __('validation.attributes.backup_before_delete'),
            'notify_users' => __('validation.attributes.notify_users'),
            'notification_message' => __('validation.attributes.notification_message'),
            'migrate_to_category' => __('validation.attributes.migrate_to_category'),
            'update_related_content' => __('validation.attributes.update_related_content'),
            'redirect_url' => __('validation.attributes.redirect_url'),
            'update_sitemap' => __('validation.attributes.update_sitemap'),
            'admin_notes' => __('validation.attributes.admin_notes'),
            'requires_approval' => __('validation.attributes.requires_approval'),
            'approved_by' => __('validation.attributes.approved_by'),
            'view_count' => __('validation.attributes.view_count'),
            'helpfulness_rating' => __('validation.attributes.helpfulness_rating'),
            'content_category' => __('validation.attributes.content_category'),
            'tags' => __('validation.attributes.tags'),
            'effective_date' => __('validation.attributes.effective_date'),
            'preserve_analytics' => __('validation.attributes.preserve_analytics'),
            'export_content' => __('validation.attributes.export_content'),
        ];
    }

    /**
     * Configure the validator instance.
     * Universal Pattern: Delete validation enhancements.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Universal Pattern: Check for dependencies before delete
            if ($this->hasActiveDependencies()) {
                $validator->errors()->add('dependencies', __('validation.has_active_dependencies'));
            }

            // Universal Pattern: Check for protected resources
            if ($this->isProtectedResource()) {
                $validator->errors()->add('protected', __('validation.protected_resource'));
            }
        });
    }

    /**
     * Prepare the data for validation.
     * Universal Pattern: Data normalization for delete.
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        $this->merge([
            'archive_content' => $this->boolean('archive_content', true),
            'backup_before_delete' => $this->boolean('backup_before_delete', true),
            'notify_users' => $this->boolean('notify_users', false),
            'update_related_content' => $this->boolean('update_related_content', true),
            'update_sitemap' => $this->boolean('update_sitemap', true),
            'requires_approval' => $this->boolean('requires_approval', true),
            'preserve_analytics' => $this->boolean('preserve_analytics', true),
            'export_content' => $this->boolean('export_content', false),
            'effective_date' => $this->effective_date ?? now()->addDays(7)->toDateString(),
        ]);

        // Get FAQ statistics
        if ($this->has('id')) {
            $faqStats = $this->getFAQStatistics($this->id);
            $this->merge([
                'view_count' => $faqStats['view_count'] ?? 0,
                'helpfulness_rating' => $faqStats['helpfulness_rating'] ?? 0,
                'content_category' => $faqStats['category'] ?? null,
            ]);
        }

        // Process tags
        if ($this->has('tags') && is_array($this->tags)) {
            $this->merge([
                'tags' => array_map('trim', array_filter($this->tags)),
            ]);
        }

        // Log deletion attempt
        Log::warning('FAQ deletion attempt', [
            'faq_id' => $this->id,
            'deletion_reason' => $this->deletion_reason ?? null,
            'ip' => $this->ip(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Handle a failed validation attempt.
     * Universal Pattern: Enhanced error handling for delete operations.
     */
    protected function failedValidation(Validator $validator): void
    {
        logger()->warning('Delete validation failed for DeleteFAQRequest', [
            'errors' => $validator->errors()->toArray(),
            'resource_id' => $this->route('id'),
            'user_id' => $this->user()?->id,
            'ip' => $this->ip(),
            'force_delete' => $this->force_delete,
        ]);

        parent::failedValidation($validator);
    }

    /**
     * Universal Pattern: Check for active dependencies.
     */
    private function hasActiveDependencies(): bool
    {
        $resource = $this->route(strtolower('FAQ'));

        // Add specific dependency checks here
        // Example: return $resource->relatedItems()->exists();

        return false;
    }

    /**
     * Universal Pattern: Check if resource is protected from deletion.
     */
    private function isProtectedResource(): bool
    {
        $resource = $this->route(strtolower('FAQ'));

        // Add protection logic here
        // Example: return $resource->is_system_default;

        return false;
    }

    /**
     * Validate if FAQ can be deleted.
     */
    private function validateFAQDeletable($faqId): bool
    {
        // Check if FAQ exists and is not protected
        $faq = \DB::table('faqs')
            ->where('id', $faqId)
            ->first();

        if (!$faq) {
            return false;
        }

        // Check if FAQ is marked as protected/system FAQ
        if ($faq->is_protected ?? false) {
            return false;
        }

        // Check if FAQ has high importance or critical status
        if (($faq->importance_level ?? 'normal') === 'critical') {
            return $this->has('requires_approval') && $this->requires_approval === false;
        }

        // Allow deletion for normal FAQs
        return true;
    }

    /**
     * Get FAQ statistics for impact assessment.
     */
    private function getFAQStatistics($faqId): array
    {
        $faq = \DB::table('faqs')
            ->where('id', $faqId)
            ->first();

        if (!$faq) {
            return [];
        }

        // Get view count from analytics if available
        $viewCount = \DB::table('faq_views')
            ->where('faq_id', $faqId)
            ->count();

        // Get helpfulness rating
        $helpfulnessRating = \DB::table('faq_ratings')
            ->where('faq_id', $faqId)
            ->avg('rating') ?? 0;

        return [
            'view_count' => $viewCount,
            'helpfulness_rating' => round($helpfulnessRating, 2),
            'category' => $faq->category ?? null,
        ];
    }
}
