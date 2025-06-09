<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\EmailTemplate
 *
 * @property int $id
 * @property string $template_name
 * @property string $subject
 * @property string $body
 * @property string $variables
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|EmailTemplate newModelQuery()
 * @method static Builder|EmailTemplate newQuery()
 * @method static Builder|EmailTemplate query()
 * @method static Builder|EmailTemplate whereBody($value)
 * @method static Builder|EmailTemplate whereCreatedAt($value)
 * @method static Builder|EmailTemplate whereId($value)
 * @method static Builder|EmailTemplate whereSubject($value)
 * @method static Builder|EmailTemplate whereTemplateName($value)
 * @method static Builder|EmailTemplate whereUpdatedAt($value)
 * @method static Builder|EmailTemplate whereVariables($value)
 *
 * @mixin \Eloquent
 */

    use Illuminate\Database\Eloquent\Factories\HasFactory;

    /**
     * @var string
     */
    public $table = 'email_templates';

    /**
     * @var array
     */
    public $fillable = [
        'template_name',
        'subject',
        'body',
        'variables',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
        protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',

            'id' => 'integer',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'variables' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        
        ];
    }


    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'subject' => 'required|max:150',
        'body' => 'required',
    ];

    /**
     * Scope for active email templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for default email templates.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope for custom email templates.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_default', false);
    }

    /**
     * Scope for searching templates by name or subject.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('template_name', 'like', "%{$term}%")
                    ->orWhere('subject', 'like', "%{$term}%")
                    ->orWhere('body', 'like', "%{$term}%");
    }

    /**
     * Scope for templates by name.
     */
    public function scopeByName($query, string $name)
    {
        return $query->where('template_name', $name);
    }

    /**
     * Scope for recently created templates.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for alphabetically ordered templates.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('template_name', 'asc');
    }

    /**
     * Scope for templates with variables.
     */
    public function scopeWithVariables($query)
    {
        return $query->whereNotNull('variables');
    }

    /**
     * Scope for system notification templates.
     */
    public function scopeNotification($query)
    {
        return $query->where('template_name', 'like', '%notification%')
                    ->orWhere('template_name', 'like', '%alert%');
    }

    /**
     * Scope for welcome/registration templates.
     */
    public function scopeWelcome($query)
    {
        return $query->where('template_name', 'like', '%welcome%')
                    ->orWhere('template_name', 'like', '%registration%');
    }
}

    /**
     * Scope a query to only include inactive records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
