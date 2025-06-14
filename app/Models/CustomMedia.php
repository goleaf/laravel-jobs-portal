<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * CustomMedia Model - Enhanced with Enhanced patterns
 *
 * @property int $id
 * @property string $name
 * @property string $file_name
 * @property string|null $title
 * @property string|null $description
 * @property string $mime_type
 * @property string|null $disk
 * @property string $collection_name
 * @property int $size
 * @property array|null $manipulations
 * @property array|null $custom_properties
 * @property array|null $generated_conversions
 * @property int|null $responsive_images
 * @property int|null $order_column
 * @property string|null $uuid
 * @property string|null $conversions_disk
 * @property bool $is_active
 * @property bool $is_featured
 * @property bool $is_processed
 * @property string|null $alt_text
 * @property string|null $caption
 * @property string|null $copyright
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read string $url
 * @property-read string $full_url
 * @property-read string $path
 * @property-read string $extension
 * @property-read string $human_readable_size
 * @property-read bool $is_image
 * @property-read bool $is_video
 * @property-read bool $is_audio
 * @property-read bool $is_document
 * @property-read array $meta_data
 *
 * Enhanced Enhanced Scopes:
 * @method static \Illuminate\Database\Eloquent\Builder active()
 * @method static \Illuminate\Database\Eloquent\Builder inactive()
 * @method static \Illuminate\Database\Eloquent\Builder featured()
 * @method static \Illuminate\Database\Eloquent\Builder nonFeatured()
 * @method static \Illuminate\Database\Eloquent\Builder processed()
 * @method static \Illuminate\Database\Eloquent\Builder unprocessed()
 * @method static \Illuminate\Database\Eloquent\Builder byCollection(string $collection)
 * @method static \Illuminate\Database\Eloquent\Builder byMimeType(string $mimeType)
 * @method static \Illuminate\Database\Eloquent\Builder images()
 * @method static \Illuminate\Database\Eloquent\Builder videos()
 * @method static \Illuminate\Database\Eloquent\Builder audio()
 * @method static \Illuminate\Database\Eloquent\Builder documents()
 * @method static \Illuminate\Database\Eloquent\Builder search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder old(int $days = 365)
 * @method static \Illuminate\Database\Eloquent\Builder large(int $bytes = 1048576)
 * @method static \Illuminate\Database\Eloquent\Builder small(int $bytes = 102400)
 * @method static \Illuminate\Database\Eloquent\Builder byDisk(string $disk)
 * @method static \Illuminate\Database\Eloquent\Builder withConversions()
 * @method static \Illuminate\Database\Eloquent\Builder withoutConversions()
 * @method static \Illuminate\Database\Eloquent\Builder alphabetical()
 * @method static \Illuminate\Database\Eloquent\Builder ordered()
 *
 * @mixin \Eloquent
 */
class CustomMedia extends Media
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'media';

    /**
     * Media type constants
     */
    public const TYPE_IMAGE = 'image';
    public const TYPE_VIDEO = 'video';
    public const TYPE_AUDIO = 'audio';
    public const TYPE_DOCUMENT = 'document';
    public const TYPE_ARCHIVE = 'archive';
    public const TYPE_OTHER = 'other';

    /**
     * Collection constants
     */
    public const COLLECTION_AVATARS = 'avatars';
    public const COLLECTION_BANNERS = 'banners';
    public const COLLECTION_GALLERY = 'gallery';
    public const COLLECTION_DOCUMENTS = 'documents';
    public const COLLECTION_ATTACHMENTS = 'attachments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'file_name',
        'title',
        'description',
        'mime_type',
        'disk',
        'collection_name',
        'size',
        'manipulations',
        'custom_properties',
        'generated_conversions',
        'responsive_images',
        'order_column',
        'uuid',
        'conversions_disk',
        'is_active',
        'is_featured',
        'is_processed',
        'alt_text',
        'caption',
        'copyright',
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
     * The accessors to append to the model's array form.
     *
     * @var array<string>
     */
    protected $appends = [
        'url',
        'full_url',
        'human_readable_size',
        'is_image',
        'is_video',
        'is_audio',
        'is_document',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'size' => 'integer',
            'manipulations' => 'array',
            'custom_properties' => 'array',
            'generated_conversions' => 'array',
            'responsive_images' => 'array',
            'order_column' => 'integer',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_processed' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'file_name', 'title', 'description', 'is_active', 'is_featured'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Validation rules for creating custom media.
     *
     * @var array<string, string>
     */
    public static array $rules = [
        'name' => 'required|string|max:255',
        'file_name' => 'required|string|max:255',
        'title' => 'nullable|string|max:255',
        'description' => 'nullable|string|max:1000',
        'mime_type' => 'required|string|max:255',
        'disk' => 'nullable|string|max:255',
        'collection_name' => 'required|string|max:255',
        'size' => 'required|integer|min:0',
        'manipulations' => 'nullable|array',
        'custom_properties' => 'nullable|array',
        'generated_conversions' => 'nullable|array',
        'responsive_images' => 'nullable|array',
        'order_column' => 'nullable|integer|min:0',
        'uuid' => 'nullable|string|max:36',
        'conversions_disk' => 'nullable|string|max:255',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_processed' => 'boolean',
        'alt_text' => 'nullable|string|max:255',
        'caption' => 'nullable|string|max:500',
        'copyright' => 'nullable|string|max:255',
    ];

    /**
     * Update validation rules for custom media.
     *
     * @param int $id
     * @return array<string, string>
     */
    public static function updateRules(int $id): array
    {
        return [
            'name' => 'required|string|max:255',
            'file_name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'mime_type' => 'required|string|max:255',
            'disk' => 'nullable|string|max:255',
            'collection_name' => 'required|string|max:255',
            'size' => 'required|integer|min:0',
            'manipulations' => 'nullable|array',
            'custom_properties' => 'nullable|array',
            'generated_conversions' => 'nullable|array',
            'responsive_images' => 'nullable|array',
            'order_column' => 'nullable|integer|min:0',
            'uuid' => 'nullable|string|max:36',
            'conversions_disk' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_processed' => 'boolean',
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:500',
            'copyright' => 'nullable|string|max:255',
        ];
    }

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope to only include active media.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to only include inactive media.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope to only include featured media.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to only include non-featured media.
     */
    public function scopeNonFeatured($query)
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope to only include processed media.
     */
    public function scopeProcessed($query)
    {
        return $query->where('is_processed', true);
    }

    /**
     * Scope to only include unprocessed media.
     */
    public function scopeUnprocessed($query)
    {
        return $query->where('is_processed', false);
    }

    // =============================================
    // SCOPES - Collection & Type
    // =============================================

    /**
     * Scope to get media by collection.
     */
    public function scopeByCollection($query, string $collection)
    {
        return $query->where('collection_name', $collection);
    }

    /**
     * Scope to get media by MIME type.
     */
    public function scopeByMimeType($query, string $mimeType)
    {
        return $query->where('mime_type', $mimeType);
    }

    /**
     * Scope to get only image files.
     */
    public function scopeImages($query)
    {
        return $query->where('mime_type', 'like', 'image/%');
    }

    /**
     * Scope to get only video files.
     */
    public function scopeVideos($query)
    {
        return $query->where('mime_type', 'like', 'video/%');
    }

    /**
     * Scope to get only audio files.
     */
    public function scopeAudio($query)
    {
        return $query->where('mime_type', 'like', 'audio/%');
    }

    /**
     * Scope to get only document files.
     */
    public function scopeDocuments($query)
    {
        return $query->whereIn('mime_type', [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'text/csv',
        ]);
    }

    /**
     * Scope to get archive files.
     */
    public function scopeArchives($query)
    {
        return $query->whereIn('mime_type', [
            'application/zip',
            'application/x-rar-compressed',
            'application/x-tar',
            'application/gzip',
        ]);
    }

    // =============================================
    // SCOPES - Search & Filtering
    // =============================================

    /**
     * Scope to search media by name, title, or description.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', '%' . $term . '%')
              ->orWhere('file_name', 'like', '%' . $term . '%')
              ->orWhere('title', 'like', '%' . $term . '%')
              ->orWhere('description', 'like', '%' . $term . '%')
              ->orWhere('alt_text', 'like', '%' . $term . '%')
              ->orWhere('caption', 'like', '%' . $term . '%');
        });
    }

    /**
     * Scope to get media created within specified days.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope to get old media created before specified days.
     */
    public function scopeOld($query, int $days = 365)
    {
        return $query->where('created_at', '<=', now()->subDays($days));
    }

    // =============================================
    // SCOPES - Size & Storage
    // =============================================

    /**
     * Scope to get large files above specified size.
     */
    public function scopeLarge($query, int $bytes = 1048576) // 1MB default
    {
        return $query->where('size', '>', $bytes);
    }

    /**
     * Scope to get small files below specified size.
     */
    public function scopeSmall($query, int $bytes = 102400) // 100KB default
    {
        return $query->where('size', '<', $bytes);
    }

    /**
     * Scope to get media by storage disk.
     */
    public function scopeByDisk($query, string $disk)
    {
        return $query->where('disk', $disk);
    }

    /**
     * Scope to get media with conversions.
     */
    public function scopeWithConversions($query)
    {
        return $query->whereNotNull('generated_conversions');
    }

    /**
     * Scope to get media without conversions.
     */
    public function scopeWithoutConversions($query)
    {
        return $query->whereNull('generated_conversions');
    }

    // =============================================
    // SCOPES - Ordering & Sorting
    // =============================================

    /**
     * Scope to order media alphabetically by name.
     */
    public function scopeAlphabetical($query)
    {
        return $query->orderBy('name', 'asc');
    }

    /**
     * Scope to order media by order column and creation date.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_column', 'asc')
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Scope to order media by size (largest first).
     */
    public function scopeBySize($query, string $direction = 'desc')
    {
        return $query->orderBy('size', $direction);
    }

    // =============================================
    // CACHE METHODS - Enhanced Caching Strategy
    // =============================================

    /**
     * Get cached active media.
     */
    public static function getCachedActive(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('custom_media.active', now()->addHours(1), function () {
            return static::active()->ordered()->get();
        });
    }

    /**
     * Get cached media by collection.
     */
    public static function getCachedByCollection(string $collection): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember("custom_media.collection.{$collection}", now()->addHours(1), function () use ($collection) {
            return static::byCollection($collection)->active()->ordered()->get();
        });
    }

    /**
     * Get cached featured media.
     */
    public static function getCachedFeatured(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember('custom_media.featured', now()->addMinutes(30), function () {
            return static::featured()->active()->ordered()->get();
        });
    }

    // =============================================
    // ACCESSOR METHODS
    // =============================================

    /**
     * Get the URL attribute.
     */
    public function getUrlAttribute(): string
    {
        return $this->getUrl();
    }

    /**
     * Get the full URL attribute.
     */
    public function getFullUrlAttribute(): string
    {
        return $this->getFullUrl();
    }

    /**
     * Get the path attribute.
     */
    public function getPathAttribute(): string
    {
        return $this->getPath();
    }

    /**
     * Get the extension attribute.
     */
    public function getExtensionAttribute(): string
    {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }

    /**
     * Get human readable size attribute.
     */
    public function getHumanReadableSizeAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        return number_format($bytes / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }

    /**
     * Check if media is an image.
     */
    public function getIsImageAttribute(): bool
    {
        return strpos($this->mime_type, 'image/') === 0;
    }

    /**
     * Check if media is a video.
     */
    public function getIsVideoAttribute(): bool
    {
        return strpos($this->mime_type, 'video/') === 0;
    }

    /**
     * Check if media is audio.
     */
    public function getIsAudioAttribute(): bool
    {
        return strpos($this->mime_type, 'audio/') === 0;
    }

    /**
     * Check if media is a document.
     */
    public function getIsDocumentAttribute(): bool
    {
        $documentMimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'text/csv',
        ];
        return in_array($this->mime_type, $documentMimes);
    }

    /**
     * Get meta data attribute.
     */
    public function getMetaDataAttribute(): array
    {
        return [
            'name' => $this->name,
            'file_name' => $this->file_name,
            'size' => $this->size,
            'human_size' => $this->human_readable_size,
            'mime_type' => $this->mime_type,
            'extension' => $this->extension,
            'collection' => $this->collection_name,
            'disk' => $this->disk,
            'is_image' => $this->is_image,
            'is_video' => $this->is_video,
            'is_audio' => $this->is_audio,
            'is_document' => $this->is_document,
        ];
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Check if media is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if media is featured.
     */
    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    /**
     * Check if media is processed.
     */
    public function isProcessed(): bool
    {
        return $this->is_processed;
    }

    /**
     * Get media type based on MIME type.
     */
    public function getType(): string
    {
        if ($this->is_image) return self::TYPE_IMAGE;
        if ($this->is_video) return self::TYPE_VIDEO;
        if ($this->is_audio) return self::TYPE_AUDIO;
        if ($this->is_document) return self::TYPE_DOCUMENT;
        
        $archiveMimes = [
            'application/zip',
            'application/x-rar-compressed',
            'application/x-tar',
            'application/gzip',
        ];
        
        if (in_array($this->mime_type, $archiveMimes)) {
            return self::TYPE_ARCHIVE;
        }
        
        return self::TYPE_OTHER;
    }

    /**
     * Check if file exists on disk.
     */
    public function exists(): bool
    {
        return Storage::disk($this->disk ?? config('filesystems.default'))->exists($this->getPath());
    }

    /**
     * Get file icon based on type.
     */
    public function getIcon(): string
    {
        return match($this->getType()) {
            self::TYPE_IMAGE => 'fa-image',
            self::TYPE_VIDEO => 'fa-video',
            self::TYPE_AUDIO => 'fa-music',
            self::TYPE_DOCUMENT => 'fa-file-text',
            self::TYPE_ARCHIVE => 'fa-file-archive',
            default => 'fa-file',
        };
    }

    /**
     * Mark media as processed.
     */
    public function markAsProcessed(): bool
    {
        return $this->update(['is_processed' => true]);
    }

    /**
     * Update media metadata.
     */
    public function updateMetadata(array $metadata): bool
    {
        $customProperties = array_merge($this->custom_properties ?? [], $metadata);
        return $this->update(['custom_properties' => $customProperties]);
    }

    // =============================================
    // CACHE MANAGEMENT
    // =============================================

    /**
     * Clear all related caches.
     */
    public function clearCaches(): void
    {
        $cacheKeys = [
            'custom_media.active',
            'custom_media.featured',
            "custom_media.collection.{$this->collection_name}",
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    // =============================================
    // BOOT METHOD
    // =============================================

    /**
     * Boot the model and register model events.
     */
    protected static function boot()
    {
        parent::boot();

        // Clear caches when model is modified
        static::saved(function ($model) {
            $model->clearCaches();
        });

        static::deleted(function ($model) {
            $model->clearCaches();
        });

        static::restored(function ($model) {
            $model->clearCaches();
        });
    }
}
