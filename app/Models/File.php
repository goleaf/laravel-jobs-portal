<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * File Model - Enhanced with Enhanced patterns.
 *
 * @property int         $id
 * @property string      $name
 * @property string      $original_name
 * @property string      $path
 * @property null|string $disk
 * @property null|string $mime_type
 * @property null|int    $size
 * @property null|string $extension
 * @property bool        $is_active
 * @property bool        $is_public
 * @property bool        $is_temporary
 * @property null|string $model_type
 * @property null|int    $model_id
 * @property null|int    $user_id
 * @property null|array  $metadata
 * @property null|Carbon $created_at
 * @property null|Carbon $updated_at
 * @property null|Carbon $deleted_at
 * @property null|Model  $model
 * @property null|User   $user
 * @property string      $url
 * @property string      $human_readable_size
 * @property bool        $is_image
 * @property bool        $is_document
 * @property bool        $is_video
 * @property bool        $is_audio
 * @property bool        $exists
 *
 * Enhanced Enhanced Scopes:
 *
 * @method static Builder active()
 * @method static Builder inactive()
 * @method static Builder public()
 * @method static Builder private()
 * @method static Builder temporary()
 * @method static Builder permanent()
 * @method static Builder recent(int $days = 30)
 * @method static Builder old(int $days = 365)
 * @method static Builder byUser(int $userId)
 * @method static Builder byModel(string $modelType, int $modelId)
 * @method static Builder byMimeType(string $mimeType)
 * @method static Builder byExtension(string $extension)
 * @method static Builder images()
 * @method static Builder documents()
 * @method static Builder videos()
 * @method static Builder audio()
 * @method static Builder search(string $term)
 * @method static Builder large(int $sizeInBytes = 1048576)
 * @method static Builder small(int $sizeInBytes = 102400)
 * @method static Builder latest()
 * @method static Builder oldest()
 *
 * @mixin \Eloquent
 */
class File extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /**
     * File type constants.
     */
    public const TYPE_IMAGE = 'image';
    public const TYPE_DOCUMENT = 'document';
    public const TYPE_VIDEO = 'video';
    public const TYPE_AUDIO = 'audio';
    public const TYPE_OTHER = 'other';

    /**
     * Image extensions.
     */
    public const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'];

    /**
     * Document extensions.
     */
    public const DOCUMENT_EXTENSIONS = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf'];

    /**
     * Video extensions.
     */
    public const VIDEO_EXTENSIONS = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv'];

    /**
     * Audio extensions.
     */
    public const AUDIO_EXTENSIONS = ['mp3', 'wav', 'flac', 'aac', 'ogg', 'wma'];

    /**
     * Validation rules.
     */
    public static array $rules = [
        'name' => 'required|string|max:255',
        'original_name' => 'required|string|max:255',
        'path' => 'required|string|max:500',
        'disk' => 'nullable|string|max:50',
        'mime_type' => 'nullable|string|max:100',
        'size' => 'nullable|integer|min:0',
        'extension' => 'nullable|string|max:10',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'is_temporary' => 'boolean',
        'model_type' => 'nullable|string|max:255',
        'model_id' => 'nullable|integer',
        'user_id' => 'nullable|integer|exists:users,id',
        'metadata' => 'nullable|array',
    ];

    /**
     * The table associated with the model.
     */
    protected $table = 'files';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'original_name',
        'path',
        'disk',
        'mime_type',
        'size',
        'extension',
        'is_active',
        'is_public',
        'is_temporary',
        'model_type',
        'model_id',
        'user_id',
        'metadata',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Activity log configuration.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'original_name', 'path', 'size', 'is_active', 'is_public'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "File has been {$eventName}")
        ;
    }

    // =============================================
    // RELATIONSHIPS
    // =============================================

    /**
     * Get the parent model.
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who uploaded the file.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // =============================================
    // SCOPES - Basic Status
    // =============================================

    /**
     * Scope for active files.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive files.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for public files.
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for private files.
     */
    public function scopePrivate(Builder $query): Builder
    {
        return $query->where('is_public', false);
    }

    /**
     * Scope for temporary files.
     */
    public function scopeTemporary(Builder $query): Builder
    {
        return $query->where('is_temporary', true);
    }

    /**
     * Scope for permanent files.
     */
    public function scopePermanent(Builder $query): Builder
    {
        return $query->where('is_temporary', false);
    }

    // =============================================
    // SCOPES - Date-based
    // =============================================

    /**
     * Scope for recent files.
     */
    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for old files.
     */
    public function scopeOld(Builder $query, int $days = 365): Builder
    {
        return $query->where('created_at', '<=', now()->subDays($days));
    }

    // =============================================
    // SCOPES - Filtering
    // =============================================

    /**
     * Scope for files by user.
     */
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for files by model.
     */
    public function scopeByModel(Builder $query, string $modelType, int $modelId): Builder
    {
        return $query->where('model_type', $modelType)
            ->where('model_id', $modelId)
        ;
    }

    /**
     * Scope for files by mime type.
     */
    public function scopeByMimeType(Builder $query, string $mimeType): Builder
    {
        return $query->where('mime_type', $mimeType);
    }

    /**
     * Scope for files by extension.
     */
    public function scopeByExtension(Builder $query, string $extension): Builder
    {
        return $query->where('extension', strtolower($extension));
    }

    // =============================================
    // SCOPES - File Types
    // =============================================

    /**
     * Scope for image files.
     */
    public function scopeImages(Builder $query): Builder
    {
        return $query->whereIn('extension', self::IMAGE_EXTENSIONS);
    }

    /**
     * Scope for document files.
     */
    public function scopeDocuments(Builder $query): Builder
    {
        return $query->whereIn('extension', self::DOCUMENT_EXTENSIONS);
    }

    /**
     * Scope for video files.
     */
    public function scopeVideos(Builder $query): Builder
    {
        return $query->whereIn('extension', self::VIDEO_EXTENSIONS);
    }

    /**
     * Scope for audio files.
     */
    public function scopeAudio(Builder $query): Builder
    {
        return $query->whereIn('extension', self::AUDIO_EXTENSIONS);
    }

    // =============================================
    // SCOPES - Size-based
    // =============================================

    /**
     * Scope for large files.
     */
    public function scopeLarge(Builder $query, int $sizeInBytes = 1048576): Builder
    {
        return $query->where('size', '>=', $sizeInBytes);
    }

    /**
     * Scope for small files.
     */
    public function scopeSmall(Builder $query, int $sizeInBytes = 102400): Builder
    {
        return $query->where('size', '<=', $sizeInBytes);
    }

    // =============================================
    // SCOPES - Search & Ordering
    // =============================================

    /**
     * Scope for searching files.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('name', 'like', '%'.$term.'%')
            ->orWhere('original_name', 'like', '%'.$term.'%')
            ->orWhere('extension', 'like', '%'.$term.'%')
            ->orWhere('mime_type', 'like', '%'.$term.'%')
        ;
    }

    /**
     * Scope for latest files.
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope for oldest files.
     */
    public function scopeOldest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'asc');
    }

    // =============================================
    // ATTRIBUTE ACCESSORS
    // =============================================

    /**
     * Get the URL of the file.
     */
    public function getUrlAttribute(): string
    {
        if ($this->is_public) {
            return Storage::disk($this->disk ?? 'public')->url($this->path);
        }

        return asset('storage/'.$this->path);
    }

    /**
     * Get size in human readable format.
     */
    public function getHumanReadableSizeAttribute(): string
    {
        $bytes = $this->size ?? 0;
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; ++$i) {
            $bytes /= 1024;
        }

        return round($bytes, 2).' '.$units[$i];
    }

    /**
     * Check if file is an image.
     */
    public function getIsImageAttribute(): bool
    {
        return in_array(strtolower($this->extension), self::IMAGE_EXTENSIONS);
    }

    /**
     * Check if file is a document.
     */
    public function getIsDocumentAttribute(): bool
    {
        return in_array(strtolower($this->extension), self::DOCUMENT_EXTENSIONS);
    }

    /**
     * Check if file is a video.
     */
    public function getIsVideoAttribute(): bool
    {
        return in_array(strtolower($this->extension), self::VIDEO_EXTENSIONS);
    }

    /**
     * Check if file is audio.
     */
    public function getIsAudioAttribute(): bool
    {
        return in_array(strtolower($this->extension), self::AUDIO_EXTENSIONS);
    }

    /**
     * Check if file exists on disk.
     */
    public function getExistsAttribute(): bool
    {
        return Storage::disk($this->disk ?? 'public')->exists($this->path);
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Check if file is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if file is public.
     */
    public function isPublic(): bool
    {
        return $this->is_public;
    }

    /**
     * Check if file is temporary.
     */
    public function isTemporary(): bool
    {
        return $this->is_temporary;
    }

    /**
     * Get file type based on extension.
     */
    public function getFileType(): string
    {
        $extension = strtolower($this->extension);

        if (in_array($extension, self::IMAGE_EXTENSIONS)) {
            return self::TYPE_IMAGE;
        }

        if (in_array($extension, self::DOCUMENT_EXTENSIONS)) {
            return self::TYPE_DOCUMENT;
        }

        if (in_array($extension, self::VIDEO_EXTENSIONS)) {
            return self::TYPE_VIDEO;
        }

        if (in_array($extension, self::AUDIO_EXTENSIONS)) {
            return self::TYPE_AUDIO;
        }

        return self::TYPE_OTHER;
    }

    /**
     * Delete file from storage.
     */
    public function deleteFromStorage(): bool
    {
        if ($this->exists) {
            return Storage::disk($this->disk ?? 'public')->delete($this->path);
        }

        return true;
    }

    /**
     * Get download URL.
     */
    public function getDownloadUrl(): string
    {
        return route('files.download', $this->id);
    }

    /**
     * Mark file as permanent.
     */
    public function markAsPermanent(): bool
    {
        return $this->update(['is_temporary' => false]);
    }

    /**
     * Get files count by user.
     */
    public static function getUserFilesCount(int $userId): int
    {
        return Cache::remember("user.{$userId}.files_count", 3600, function () use ($userId) {
            return self::where('user_id', $userId)->active()->count();
        });
    }

    /**
     * Get total storage used by user.
     */
    public static function getUserStorageUsed(int $userId): int
    {
        return Cache::remember("user.{$userId}.storage_used", 3600, function () use ($userId) {
            return self::where('user_id', $userId)->active()->sum('size') ?? 0;
        });
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
            'files.active',
            'files.recent',
            'files.images',
            'files.documents',
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        // Clear user specific caches
        if ($this->user_id) {
            Cache::forget("user.{$this->user_id}.files_count");
            Cache::forget("user.{$this->user_id}.storage_used");
        }
    }

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'size' => 'integer',
            'is_active' => 'boolean',
            'is_public' => 'boolean',
            'is_temporary' => 'boolean',
            'model_id' => 'integer',
            'user_id' => 'integer',
            'metadata' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
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

        // Set default values
        static::creating(function ($model) {
            if (is_null($model->is_active)) {
                $model->is_active = true;
            }
            if (is_null($model->is_public)) {
                $model->is_public = false;
            }
            if (is_null($model->is_temporary)) {
                $model->is_temporary = false;
            }
            if (is_null($model->disk)) {
                $model->disk = 'public';
            }
        });

        // Clear caches when model is modified
        static::saved(function ($model) {
            $model->clearCaches();
        });

        static::deleted(function ($model) {
            $model->clearCaches();
            // Optionally delete file from storage
            // $model->deleteFromStorage();
        });

        static::restored(function ($model) {
            $model->clearCaches();
        });
    }
}
