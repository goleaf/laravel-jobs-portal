<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Inquiry
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $phone_no
 * @property string $subject
 * @property string $message
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inquiry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inquiry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inquiry query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inquiry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inquiry whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inquiry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inquiry whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inquiry whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inquiry wherePhoneNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inquiry whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Inquiry whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */

    /**
     * Scope a query to only include popular records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePopular(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->orderBy("views_count", "desc");
    }

$/i',
        'phone_no' => 'nullable',
        'subject' => 'required|max:190',
        'message' => 'required',
    ];

    public $table = 'inquiries';

    public $fillable = [
        'name', 'email', 'phone_no', 'subject', 'message',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
        protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_featured' => 'boolean',

            'id' => 'integer',
            'name' => 'string',
            'email' => 'string',
            'phone_no' => 'string',
            'subject' => 'string',
            'message' => 'string',
            'is_read' => 'boolean',
            'is_resolved' => 'boolean',
            'priority' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        
        ];
    }


    /**
     * Scope for read inquiries.
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope for unread inquiries.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for resolved inquiries.
     */
    public function scopeResolved($query)
    {
        return $query->where('is_resolved', true);
    }

    /**
     * Scope for unresolved inquiries.
     */
    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }

    /**
     * Scope for high priority inquiries.
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', '>=', 8);
    }

    /**
     * Scope for medium priority inquiries.
     */
    public function scopeMediumPriority($query)
    {
        return $query->whereBetween('priority', [4, 7]);
    }

    /**
     * Scope for low priority inquiries.
     */
    public function scopeLowPriority($query)
    {
        return $query->where('priority', '<=', 3);
    }

    /**
     * Scope for searching inquiries.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('subject', 'like', "%{$term}%")
                    ->orWhere('message', 'like', "%{$term}%");
    }

    /**
     * Scope for recent inquiries.
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old inquiries.
     */
    public function scopeOld($query, int $days = 30)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope for inquiries by email.
     */
    public function scopeByEmail($query, string $email)
    {
        return $query->where('email', $email);
    }

    /**
     * Scope for urgent inquiries.
     */
    public function scopeUrgent($query)
    {
        return $query->where('priority', 10);
    }

    /**
     * Scope for pending inquiries (unread and unresolved).
     */
    public function scopePending($query)
    {
        return $query->where('is_read', false)->where('is_resolved', false);
    }

    /**
     * Scope for ordering by priority.
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc')->orderBy('created_at', 'asc');
    }
}

    /**
     * Scope a query to only include active records.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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
