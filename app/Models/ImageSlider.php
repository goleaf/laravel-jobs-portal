<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * App\Models\ImageSlider
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ImageSlider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ImageSlider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ImageSlider query()
 *
 * @mixin \Eloquent
 *
 * @property int $id
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $image_slider_url
 * @property-read \Illuminate\Database\Eloquent\Collection|Media[] $media
 * @property-read int|null $media_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ImageSlider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImageSlider whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImageSlider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImageSlider whereUpdatedAt($value)
 *
 * @property int $is_active
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ImageSlider whereIsActive($value)
 */
class ImageSlider extends Model implements HasMedia
{
    use InteractsWithMedia;

    const ALL = 2;
    const ACTIVE = 1;
    const DEACTIVE = 0;
    const STATUS = [
        self::ALL => 'Select Status',
        self::ACTIVE => 'Active',
        self::DEACTIVE => 'Deactive',
    ];

    public const PATH = 'image-sliders';

    public $table = 'image_sliders';

    public $fillable = [
        'description',
        'is_active',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'image_slider' => 'required|mimes:jpeg,jpg,png',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
        protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',

        'id' => 'integer',
        'description' => 'string',
        'is_active' => 'boolean',
    
        ];
    }


    /**
     * @var array
     */
    protected $appends = ['image_slider_url'];

    /**
     * @return mixed
     */
    public function getImageSliderUrlAttribute()
    {
        /** @var Media $media */
        $media = $this->media->first();
        if (! empty($media)) {
            return $media->getFullUrl();
        }

        return asset('assets/img/infyom-logo.png');
    }




    /**
     * Scope for active image sliders.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive image sliders.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for featured image sliders.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for non-featured image sliders.
     */
    public function scopeNotFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope for searching image sliders.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('title', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
    }

    /**
     * Scope for recent image sliders.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old image sliders.
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
     * Scope for gallery sliders.
     */
    public function scopeGallery($query)
    {
        return $query->active()->withImages()->ordered();
    }

    /**
     * Scope for promotional sliders.
     */
    public function scopePromotional($query)
    {
        return $query->active()->featured()->withLinks();
    }
}
