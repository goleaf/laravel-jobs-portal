<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Class JobCategory
 *
 * @version June 19, 2020, 6:50 am UTC
 *
 * @property string $name
 * @property string $description
 * @property bool|null $is_featured
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JobCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JobCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JobCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JobCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JobCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JobCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JobCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JobCategory whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\JobCategory whereIsFeatured($value)
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Job[] $jobs
 * @property-read int|null $jobs_count
 */
class JobCategory extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    public const PATH = 'job_category';

    public $table = 'job_categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'image',
        'is_featured',
    ];

    const ALL = 2;
    const IS_FEATURED = 1;
    const NOT_FEATURED = 0;
    const FEATURED = [
        self::ALL => 'All',
        self::IS_FEATURED => 'Featured',
        self::NOT_FEATURED => 'Not featured',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|max:160|unique:job_categories,name',
        'customer_image' => 'nullable|mimes:jpeg,jpg,png',
    ];

    protected $appends = ['image_url', 'is_featured_label'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'is_featured' => 'boolean',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the URL for the job category image.
     *
     * @return string|null
     */
    public function getImageUrlAttribute()
    {
        if (! $this->image) {
            return null;
        }

        return Storage::url($this->image);
    }

    public function getIsFeaturedLabelAttribute(): string
    {
        return self::FEATURED[$this->is_featured];
    }

    /**
     * Get the jobs for the job category.
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'job_category_id');
    }

    /**
     * Scope for active job categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured job categories.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for non-featured job categories.
     */
    public function scopeNotFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope for default job categories.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope for custom job categories.
     */
    public function scopeCustom($query)
    {
        return $query->where('is_default', false);
    }

    /**
     * Scope for job categories with jobs.
     */
    public function scopeWithJobs($query)
    {
        return $query->whereHas('jobs');
    }

    /**
     * Scope for job categories with active jobs.
     */
    public function scopeWithActiveJobs($query)
    {
        return $query->whereHas('jobs', function ($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope for searching job categories by name.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
    }

    /**
     * Scope for popular job categories (with most jobs).
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount('jobs')
                    ->orderByDesc('jobs_count')
                    ->limit($limit);
    }

    /**
     * Scope for alphabetically ordered job categories.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Scope for recently created job categories.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
