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
            'parent_id' => 'integer',
            'name' => 'string',
            'description' => 'string',
            'slug' => 'string',
            'icon' => 'string',
            'color' => 'string',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_default' => 'boolean',
            'sort_order' => 'integer',
            'jobs_count' => 'integer',
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
     * Scope for inactive job categories.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
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
     * Scope for parent categories (top-level).
     */
    public function scopeParent($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope for child categories (subcategories).
     */
    public function scopeChild($query)
    {
        return $query->whereNotNull('parent_id');
    }

    /**
     * Scope for categories by parent.
     */
    public function scopeByParent($query, int $parentId)
    {
        return $query->where('parent_id', $parentId);
    }

    /**
     * Scope for categories with jobs.
     */
    public function scopeWithJobs($query)
    {
        return $query->has('jobs');
    }

    /**
     * Scope for categories without jobs.
     */
    public function scopeWithoutJobs($query)
    {
        return $query->doesntHave('jobs');
    }

    /**
     * Scope for categories with active jobs.
     */
    public function scopeWithActiveJobs($query)
    {
        return $query->whereHas('jobs', function ($q) {
            $q->where('status', 1);
        });
    }

    /**
     * Scope for categories with children.
     */
    public function scopeWithChildren($query)
    {
        return $query->has('children');
    }

    /**
     * Scope for categories without children.
     */
    public function scopeWithoutChildren($query)
    {
        return $query->doesntHave('children');
    }

    /**
     * Scope for searching job categories.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhere('slug', 'like', "%{$term}%");
    }

    /**
     * Scope for recent job categories.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old job categories.
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope for popular job categories (with most jobs).
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->withCount('jobs')
                    ->orderBy('jobs_count', 'desc')
                    ->limit($limit);
    }

    /**
     * Scope for trending job categories.
     */
    public function scopeTrending($query, int $limit = 10)
    {
        return $query->withCount(['jobs' => function ($q) {
            $q->where('created_at', '>=', now()->subDays(30))
              ->where('status', 1);
        }])
        ->orderBy('jobs_count', 'desc')
        ->limit($limit);
    }

    /**
     * Scope for ordered job categories.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    /**
     * Scope for alphabetical ordering.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Scope for categories by slug.
     */
    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }

    /**
     * Scope for technology categories.
     */
    public function scopeTechnology($query)
    {
        return $query->where('name', 'like', '%technology%')
                    ->orWhere('name', 'like', '%IT%')
                    ->orWhere('name', 'like', '%software%')
                    ->orWhere('name', 'like', '%development%');
    }

    /**
     * Scope for business categories.
     */
    public function scopeBusiness($query)
    {
        return $query->where('name', 'like', '%business%')
                    ->orWhere('name', 'like', '%management%')
                    ->orWhere('name', 'like', '%finance%')
                    ->orWhere('name', 'like', '%accounting%');
    }

    /**
     * Scope for creative categories.
     */
    public function scopeCreative($query)
    {
        return $query->where('name', 'like', '%creative%')
                    ->orWhere('name', 'like', '%design%')
                    ->orWhere('name', 'like', '%art%')
                    ->orWhere('name', 'like', '%media%');
    }

    /**
     * Scope for healthcare categories.
     */
    public function scopeHealthcare($query)
    {
        return $query->where('name', 'like', '%healthcare%')
                    ->orWhere('name', 'like', '%medical%')
                    ->orWhere('name', 'like', '%nursing%')
                    ->orWhere('name', 'like', '%doctor%');
    }

    /**
     * Scope for education categories.
     */
    public function scopeEducation($query)
    {
        return $query->where('name', 'like', '%education%')
                    ->orWhere('name', 'like', '%teaching%')
                    ->orWhere('name', 'like', '%academic%')
                    ->orWhere('name', 'like', '%training%');
    }

    /**
     * Scope for sales categories.
     */
    public function scopeSales($query)
    {
        return $query->where('name', 'like', '%sales%')
                    ->orWhere('name', 'like', '%marketing%')
                    ->orWhere('name', 'like', '%retail%')
                    ->orWhere('name', 'like', '%customer%');
    }

    /**
     * Scope for engineering categories.
     */
    public function scopeEngineering($query)
    {
        return $query->where('name', 'like', '%engineering%')
                    ->orWhere('name', 'like', '%engineer%')
                    ->orWhere('name', 'like', '%technical%')
                    ->orWhere('name', 'like', '%mechanical%');
    }

    /**
     * Scope for categories with high job count.
     */
    public function scopeHighJobCount($query, int $threshold = 100)
    {
        return $query->where('jobs_count', '>=', $threshold);
    }

    /**
     * Scope for categories with low job count.
     */
    public function scopeLowJobCount($query, int $threshold = 10)
    {
        return $query->where('jobs_count', '<=', $threshold);
    }

    /**
     * Scope for categories with icon.
     */
    public function scopeWithIcon($query)
    {
        return $query->whereNotNull('icon')
                    ->where('icon', '!=', '');
    }

    /**
     * Scope for categories with color.
     */
    public function scopeWithColor($query)
    {
        return $query->whereNotNull('color')
                    ->where('color', '!=', '');
    }
}
