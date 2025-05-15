<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class File extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'model_type',
        'model_id',
        'collection_name',
        'name',
        'file_name',
        'mime_type',
        'disk',
        'path',
        'size',
        'order_column',
        'custom_properties',
        'responsive_images',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'custom_properties' => 'array',
        'responsive_images' => 'array',
        'size' => 'integer',
        'order_column' => 'integer',
    ];

    /**
     * Get the URL of the file.
     */
    public function getUrl(): string
    {
        return asset('storage/'.$this->path);
    }

    /**
     * Get the parent model.
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get path attribute.
     */
    public function getPathAttribute(): string
    {
        return $this->attributes['path'] ?? '';
    }

    /**
     * Get size in human readable format.
     */
    public function getHumanReadableSize(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2).' '.$units[$i];
    }
}
