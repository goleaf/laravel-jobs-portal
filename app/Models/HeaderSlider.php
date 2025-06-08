<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * App\Models\HeaderSlider
 *
 * @property int $id
 * @property string|null $description
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $header_slider_url
 * @property-read \Illuminate\Database\Eloquent\Collection|Media[] $media
 * @property-read int|null $media_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|HeaderSlider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HeaderSlider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HeaderSlider query()
 * @method static \Illuminate\Database\Eloquent\Builder|HeaderSlider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HeaderSlider whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HeaderSlider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HeaderSlider whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HeaderSlider whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class HeaderSlider extends Model implements HasMedia
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

    public const PATH = 'header-sliders';

    /**
     * @var string
     */
    public $table = 'header_sliders';

    /**
     * @var string[]
     */
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
        'header_slider' => 'required|mimes:jpeg,jpg,png',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'description' => 'string',
        'is_active' => 'boolean',
    ];

    /**
     * @var array
     */
    protected $appends = ['header_slider_url'];

    /**
     * @return mixed
     */
    public function getHeaderSliderUrlAttribute()
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
            'sub_title' => 'string',
            'description' => 'string',
            'image_url' => 'string',
            'button_text' => 'string',
            'button_url' => 'string',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Scope for active header sliders.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive header sliders.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for featured header sliders.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for non-featured header sliders.
     */
    public function scopeNotFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope for searching header sliders.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('title', 'like', "%{$term}%")
                    ->orWhere('sub_title', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
    }

    /**
     * Scope for recent header sliders.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old header sliders.
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
     * Scope for sliders with buttons.
     */
    public function scopeWithButtons($query)
    {
        return $query->whereNotNull('button_text')
                    ->where('button_text', '!=', '')
                    ->whereNotNull('button_url')
                    ->where('button_url', '!=', '');
    }

    /**
     * Scope for sliders without buttons.
     */
    public function scopeWithoutButtons($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('button_text')
                  ->orWhere('button_text', '')
                  ->orWhereNull('button_url')
                  ->orWhere('button_url', '');
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
     * Scope for sliders with subtitles.
     */
    public function scopeWithSubtitles($query)
    {
        return $query->whereNotNull('sub_title')->where('sub_title', '!=', '');
    }
}
