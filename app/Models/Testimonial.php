<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * App\Models\Testimonial
 *
 * @property int $id
 * @property string $customer_name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $customer_image_url
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\Models\Media[] $media
 * @property-read int|null $media_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Testimonial newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Testimonial newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Testimonial query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Testimonial whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Testimonial whereCustomerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Testimonial whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Testimonial whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Testimonial whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Testimonial extends Model implements HasMedia
{
    use InteractsWithMedia, HasFactory;

    public const PATH = 'testimonials';

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'customer_name' => 'required',
        'customer_image' => 'required|mimes:jpeg,jpg,png',
    ];

    public $table = 'testimonials';

    public $fillable = [
        'customer_name',
        'description',
    ];

    /**
     * @var array
     */
    protected $appends = ['customer_image_url'];

    /**
     * @return mixed
     */
    public function getCustomerImageUrlAttribute()
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
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'rating' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Scope for active testimonials.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured testimonials.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for testimonials by rating.
     */
    public function scopeByRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope for high rated testimonials (4+ stars).
     */
    public function scopeHighRated($query)
    {
        return $query->where('rating', '>=', 4);
    }

    /**
     * Scope for searching testimonials.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where('customer_name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
    }

    /**
     * Scope for recent testimonials.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for testimonials with images.
     */
    public function scopeWithImage($query)
    {
        return $query->whereHas('media');
    }

    /**
     * Scope for random testimonials.
     */
    public function scopeRandom($query, int $limit = 5)
    {
        return $query->inRandomOrder()->limit($limit);
    }

    /**
     * Scope for alphabetically ordered testimonials.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('customer_name', 'asc');
    }
}
