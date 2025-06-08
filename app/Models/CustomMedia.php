<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CustomMedia extends Media
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'file_name',
        'mime_type',
        'size',
        'collection_name',
        'disk',
    ];
    
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'model_id');
    }
}
