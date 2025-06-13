<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PostCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "description",
        "is_default",
    ];

    protected function casts(): array
    {
        return [
            "id" => "integer",
            "name" => "string",
            "description" => "string",
            "is_default" => "boolean",
            "created_at" => "datetime",
            "updated_at" => "datetime",
        ];
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, "post_assigned_categories", "post_categories_id", "post_id");
    }

    public function postAssignCategories(): BelongsToMany
    {
        return $this->posts();
    }

    public function scopeDefault($query)
    {
        return $query->where("is_default", true);
    }

    public function scopeCustom($query)
    {
        return $query->where("is_default", false);
    }

    public function isDefault(): bool
    {
        return $this->is_default;
    }
}
