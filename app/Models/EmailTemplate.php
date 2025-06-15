<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * EmailTemplate Model - Enhanced with Enhanced patterns.
 *
 * @property int         $id
 * @property string      $template_name
 * @property string      $subject
 * @property string      $body
 * @property null|string $variables
 * @property null|string $description
 * @property null|string $category
 * @property bool        $is_active
 * @property bool        $is_default
 * @property bool        $is_system
 * @property null|array  $placeholders
 * @property null|string $preview_text
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property null|Carbon $deleted_at
 * @property string      $display_name
 * @property array       $available_variables
 * @property string      $category_label
 * @property string      $preview_body
 * @property int         $usage_count
 *
 * Enhanced Enhanced Scopes:
 *
 * @method static Builder active()
 * @method static Builder inactive()
 * @method static Builder default()
 * @method static Builder custom()
 * @method static Builder system()
 * @method static Builder userDefined()
 * @method static Builder byCategory(string $category)
 * @method static Builder search(string $term)
 * @method static Builder recent(int $days = 30)
 * @method static Builder old(int $days = 365)
 * @method static Builder popular()
 * @method static Builder alphabetical()
 * @method static Builder byUsage()
 * @method static Builder withVariables()
 * @method static Builder withoutVariables()
 * @method static Builder notification()
 * @method static Builder transactional()
 * @method static Builder marketing()
 * @method static Builder welcome()
 * @method static Builder password()
 * @method static Builder job()
 * @method static Builder company()
 * @method static Builder candidate()
 *
 * @mixin \Eloquent
 */
class EmailTemplate extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    protected $table = 'email_templates';

    // =============================================
    // CONSTANTS
    // =============================================

    public const CATEGORIES = [
        'notification' => 'Notification Emails',
        'transactional' => 'Transactional Emails',
        'marketing' => 'Marketing Emails',
        'welcome' => 'Welcome Emails',
        'password' => 'Password Reset Emails',
        'job' => 'Job Related Emails',
        'company' => 'Company Emails',
        'candidate' => 'Candidate Emails',
        'system' => 'System Emails',
        'other' => 'Other Emails',
    ];

    public const COMMON_VARIABLES = [
        'user_name' => 'User Name',
        'user_email' => 'User Email',
        'company_name' => 'Company Name',
        'job_title' => 'Job Title',
        'candidate_name' => 'Candidate Name',
        'application_date' => 'Application Date',
        'site_name' => 'Site Name',
        'site_url' => 'Site URL',
        'current_date' => 'Current Date',
        'current_year' => 'Current Year',
    ];

    /**
     * Validation rules for creating email templates.
     *
     * @var array<string, string>
     */
    public static array $rules = [
        'template_name' => 'required|string|max:255|unique:email_templates,template_name',
        'subject' => 'required|string|max:255',
        'body' => 'required|string',
        'variables' => 'nullable|string',
        'description' => 'nullable|string|max:500',
        'category' => 'nullable|string|max:100',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'is_system' => 'boolean',
        'placeholders' => 'nullable|array',
        'preview_text' => 'nullable|string|max:255',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'template_name',
        'subject',
        'body',
        'variables',
        'description',
        'category',
        'is_active',
        'is_default',
        'is_system',
        'placeholders',
        'preview_text',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Configure activity logging.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'template_name',
                'subject',
                'body',
                'variables',
                'description',
                'category',
                'is_active',
                'is_default',
                'is_system',
                'placeholders',
                'preview_text',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
        ;
    }

    /**
     * Update validation rules for email templates.
     *
     * @return array<string, string>
     */
    public static function updateRules(int $id): array
    {
        return [
            'template_name' => 'required|string|max:255|unique:email_templates,template_name,'.$id,
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'variables' => 'nullable|string',
            'description' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'is_system' => 'boolean',
            'placeholders' => 'nullable|array',
            'preview_text' => 'nullable|string|max:255',
        ];
    }

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope a query to only include active templates.
     *
     * @param mixed $query
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Scope a query to only include inactive templates.
     *
     * @param mixed $query
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include default templates.
     *
     * @param mixed $query
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope a query to only include custom templates.
     *
     * @param mixed $query
     */
    public function scopeCustom($query)
    {
        return $query->where('is_default', false);
    }

    /**
     * Scope a query to only include system templates.
     *
     * @param mixed $query
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * Scope a query to only include user-defined templates.
     *
     * @param mixed $query
     */
    public function scopeUserDefined($query)
    {
        return $query->where('is_system', false);
    }

    // =============================================
    // SCOPES - Filtering & Search
    // =============================================

    /**
     * Scope for templates by category.
     *
     * @param mixed $query
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for searching templates.
     *
     * @param mixed $query
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('template_name', 'like', "%{$term}%")
                ->orWhere('subject', 'like', "%{$term}%")
                ->orWhere('body', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
            ;
        });
    }

    /**
     * Scope for recent templates.
     *
     * @param mixed $query
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('updated_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old templates.
     *
     * @param mixed $query
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('updated_at', '<', now()->subDays($days));
    }

    /**
     * Scope for popular templates (most recently used).
     *
     * @param mixed $query
     */
    public function scopePopular($query)
    {
        return $query->orderBy('updated_at', 'desc');
    }

    /**
     * Scope for alphabetical ordering.
     *
     * @param mixed $query
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('template_name', 'asc');
    }

    /**
     * Scope for ordering by usage.
     *
     * @param mixed $query
     */
    public function scopeByUsage($query)
    {
        return $query->orderBy('updated_at', 'desc');
    }

    // =============================================
    // SCOPES - Content Based
    // =============================================

    /**
     * Scope for templates with variables.
     *
     * @param mixed $query
     */
    public function scopeWithVariables($query)
    {
        return $query->whereNotNull('variables')
            ->where('variables', '!=', '')
        ;
    }

    /**
     * Scope for templates without variables.
     *
     * @param mixed $query
     */
    public function scopeWithoutVariables($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('variables')
                ->orWhere('variables', '')
            ;
        });
    }

    // =============================================
    // SCOPES - Category Specific
    // =============================================

    /**
     * Scope for notification templates.
     *
     * @param mixed $query
     */
    public function scopeNotification($query)
    {
        return $query->where('category', 'notification');
    }

    /**
     * Scope for transactional templates.
     *
     * @param mixed $query
     */
    public function scopeTransactional($query)
    {
        return $query->where('category', 'transactional');
    }

    /**
     * Scope for marketing templates.
     *
     * @param mixed $query
     */
    public function scopeMarketing($query)
    {
        return $query->where('category', 'marketing');
    }

    /**
     * Scope for welcome templates.
     *
     * @param mixed $query
     */
    public function scopeWelcome($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'welcome')
                ->orWhere('template_name', 'like', '%welcome%')
            ;
        });
    }

    /**
     * Scope for password reset templates.
     *
     * @param mixed $query
     */
    public function scopePassword($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'password')
                ->orWhere('template_name', 'like', '%password%')
                ->orWhere('template_name', 'like', '%reset%')
            ;
        });
    }

    /**
     * Scope for job-related templates.
     *
     * @param mixed $query
     */
    public function scopeJob($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'job')
                ->orWhere('template_name', 'like', '%job%')
                ->orWhere('template_name', 'like', '%application%')
            ;
        });
    }

    /**
     * Scope for company-related templates.
     *
     * @param mixed $query
     */
    public function scopeCompany($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'company')
                ->orWhere('template_name', 'like', '%company%')
                ->orWhere('template_name', 'like', '%employer%')
            ;
        });
    }

    /**
     * Scope for candidate-related templates.
     *
     * @param mixed $query
     */
    public function scopeCandidate($query)
    {
        return $query->where(function ($q) {
            $q->where('category', 'candidate')
                ->orWhere('template_name', 'like', '%candidate%')
                ->orWhere('template_name', 'like', '%applicant%')
            ;
        });
    }

    // =============================================
    // CACHED METHODS
    // =============================================

    /**
     * Get cached templates by category.
     */
    public static function getCachedByCategory(string $category): Collection
    {
        return Cache::remember(
            "email_templates_category_{$category}",
            now()->addHours(12),
            fn () => static::active()
                ->byCategory($category)
                ->alphabetical()
                ->get()
        );
    }

    /**
     * Get cached active templates.
     */
    public static function getCachedActive(): Collection
    {
        return Cache::remember(
            'email_templates_active',
            now()->addHours(6),
            fn () => static::active()
                ->alphabetical()
                ->get()
        );
    }

    /**
     * Get cached system templates.
     */
    public static function getCachedSystem(): Collection
    {
        return Cache::remember(
            'email_templates_system',
            now()->addHours(24),
            fn () => static::active()
                ->system()
                ->alphabetical()
                ->get()
        );
    }

    /**
     * Get cached template by name.
     */
    public static function getCachedByName(string $name): ?self
    {
        return Cache::remember(
            "email_template_name_{$name}",
            now()->addHours(12),
            fn () => static::active()
                ->where('template_name', $name)
                ->first()
        );
    }

    // =============================================
    // HELPER METHODS & ATTRIBUTES
    // =============================================

    /**
     * Get display name attribute.
     */
    public function getDisplayNameAttribute(): string
    {
        return ucwords(str_replace(['_', '-'], ' ', $this->template_name));
    }

    /**
     * Get available variables attribute.
     */
    public function getAvailableVariablesAttribute(): array
    {
        if (!$this->variables) {
            return [];
        }

        return array_map('trim', explode(',', $this->variables));
    }

    /**
     * Get category label attribute.
     */
    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? ucwords($this->category ?? 'Other');
    }

    /**
     * Get preview body attribute (truncated).
     */
    public function getPreviewBodyAttribute(): string
    {
        return strlen($this->body) > 200
            ? substr(strip_tags($this->body), 0, 200).'...'
            : strip_tags($this->body);
    }

    /**
     * Get usage count attribute (placeholder for future implementation).
     */
    public function getUsageCountAttribute(): int
    {
        // This would be implemented with actual usage tracking
        return 0;
    }

    /**
     * Replace variables in template content.
     */
    public function replaceVariables(array $variables): array
    {
        $subject = $this->subject;
        $body = $this->body;

        foreach ($variables as $key => $value) {
            $placeholder = '{'.$key.'}';
            $subject = str_replace($placeholder, $value, $subject);
            $body = str_replace($placeholder, $value, $body);
        }

        return [
            'subject' => $subject,
            'body' => $body,
        ];
    }

    /**
     * Get all placeholders in template.
     */
    public function getPlaceholders(): array
    {
        $content = $this->subject.' '.$this->body;
        preg_match_all('/\{([^}]+)\}/', $content, $matches);

        return array_unique($matches[1] ?? []);
    }

    /**
     * Check if template has required variables.
     */
    public function hasRequiredVariables(array $variables): bool
    {
        $placeholders = $this->getPlaceholders();
        $requiredVariables = $this->available_variables;

        foreach ($requiredVariables as $required) {
            if (!isset($variables[$required])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if template is editable.
     */
    public function isEditable(): bool
    {
        return !$this->is_system;
    }

    /**
     * Check if template is deletable.
     */
    public function isDeletable(): bool
    {
        return !$this->is_system && !$this->is_default;
    }

    /**
     * Clone template with new name.
     */
    public function cloneTemplate(string $newName): self
    {
        $clone = $this->replicate();
        $clone->template_name = $newName;
        $clone->is_default = false;
        $clone->is_system = false;
        $clone->save();

        return $clone;
    }

    /**
     * Clear template-related caches.
     */
    public function clearCaches(): void
    {
        $cacheKeys = [
            'email_templates_active',
            'email_templates_system',
            "email_template_name_{$this->template_name}",
        ];

        if ($this->category) {
            $cacheKeys[] = "email_templates_category_{$this->category}";
        }

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'is_system' => 'boolean',
            'placeholders' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($template) {
            $template->clearCaches();
        });

        static::deleted(function ($template) {
            $template->clearCaches();
        });

        static::restored(function ($template) {
            $template->clearCaches();
        });
    }
}
