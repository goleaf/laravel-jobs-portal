<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * App\Models\BrandingSliders
 *
 * @property int $id
 * @property string $title
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $branding_slider_url
 * @property-read \Illuminate\Database\Eloquent\Collection|Media[] $media
 * @property-read int|null $media_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|BrandingSliders newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BrandingSliders newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BrandingSliders query()
 * @method static \Illuminate\Database\Eloquent\Builder|BrandingSliders whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BrandingSliders whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BrandingSliders whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BrandingSliders whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BrandingSliders whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class BrandingSliders extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    const ALL = 2;
    const ACTIVE = 1;
    const DEACTIVE = 0;
    const STATUS = [
        self::ALL => 'Select Status',
        self::ACTIVE => 'Active',
        self::DEACTIVE => 'Deactive',
    ];

    public const PATH = 'branding-sliders';

    public $table = 'branding_sliders';

    public $fillable = [
        'title',
        'is_active',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'title' => 'required|max:150',
        'branding_slider' => 'required|mimes:jpeg,jpg,png',
    ];

    /**
     * @var array
     */
    protected $appends = ['branding_slider_url'];

    /**
     * @return mixed
     */
    public function getBrandingSliderUrlAttribute()
    {
        /** @var Media $media */
        $media = $this->media->first();
        if (! empty($media)) {
            return $media->getFullUrl();
        }

        return asset('assets/img/infyom-logo.png');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'title' => 'string',
            'description' => 'string',
            'image_url' => 'string',
            'link_url' => 'string',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Scope for active branding sliders.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive branding sliders.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for featured branding sliders.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for non-featured branding sliders.
     */
    public function scopeNotFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope for searching branding sliders.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('title', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
    }

    /**
     * Scope for recent branding sliders.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old branding sliders.
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<', now()->subDays($days));
    }

    /**
     * Scope for ordering by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');
    }

    /**
     * Scope for alphabetical ordering.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('title', 'asc');
    }

    /**
     * Scope for sliders with images.
     */
    public function scopeWithImages($query)
    {
        return $query->whereNotNull('image_url')->where('image_url', '!=', '');
    }

    /**
     * Scope for sliders with links.
     */
    public function scopeWithLinks($query)
    {
        return $query->whereNotNull('link_url')->where('link_url', '!=', '');
    }

    /**
     * Scope for sliders without links.
     */
    public function scopeWithoutLinks($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('link_url')->orWhere('link_url', '');
        });
    }

    /**
     * Scope for homepage sliders.
     */
    public function scopeHomepage($query)
    {
        return $query->active()->featured()->ordered();
    }

    /**
     * Scope for branding sliders with media.
     */
    public function scopeWithMedia($query)
    {
        return $query->whereHas('media');
    }

    /**
     * Scope for random branding sliders.
     */
    public function scopeRandom($query, int $limit = 5)
    {
        return $query->inRandomOrder()->limit($limit);
    }

    /**
     * Scope for popular branding sliders.
     */
    public function scopePopular($query, int $limit = 10)
    {
        return $query->limit($limit);
    }
}
