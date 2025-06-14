<?php namespace App\Models; use Illuminate\Database\Eloquent\Factories\HasFactory; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsToMany; use Illuminate\Database\Eloquent\SoftDeletes; use Illuminate\Support\Facades\Cache; use Spatie\Activitylog\Traits\LogsActivity; use Spatie\Activitylog\LogOptions;

/**
 * PostCategory Model - Enhanced with Enhanced patterns
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property bool $is_default
 * @property bool $is_active
 * @property bool $is_featured
 * @property int|null $sort_order
 * @property string|null $color
 * @property string|null $icon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Post[] $posts
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Post[] $activePosts
 * @property-read string $display_name
 * @property-read string $slug
 * @property-read string $badge_html
 * @property-read string|null $icon_html
 * @property-read int $posts_count
 * @property-read int $active_posts_count
 * @property-read array $stats
 *
 * Enhanced Enhanced Scopes:
 * @method static \Illuminate\Database\Eloquent\Builder active()
 * @method static \Illuminate\Database\Eloquent\Builder inactive()
 * @method static \Illuminate\Database\Eloquent\Builder featured()
 * @method static \Illuminate\Database\Eloquent\Builder nonFeatured()
 * @method static \Illuminate\Database\Eloquent\Builder default()
 * @method static \Illuminate\Database\Eloquent\Builder custom()
 * @method static \Illuminate\Database\Eloquent\Builder search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder old(int $days = 365)
 * @method static \Illuminate\Database\Eloquent\Builder popular(int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Builder alphabetical()
 * @method static \Illuminate\Database\Eloquent\Builder ordered()
 * @method static \Illuminate\Database\Eloquent\Builder trending(int $days = 30, int $limit = 10)
 * @method static \Illuminate\Database\Eloquent\Builder withPosts()
 * @method static \Illuminate\Database\Eloquent\Builder withoutPosts()
 * @method static \Illuminate\Database\Eloquent\Builder withActivePosts()
 * @method static \Illuminate\Database\Eloquent\Builder withPostCounts()
 * @method static \Illuminate\Database\Eloquent\Builder byColor(string $color)
 * @method static \Illuminate\Database\Eloquent\Builder withIcons()
 * @method static \Illuminate\Database\Eloquent\Builder withoutIcons()
 * @method static \Illuminate\Database\Eloquent\Builder empty()
 * @method static \Illuminate\Database\Eloquent\Builder nameLike(string $name)
 *
 * @mixin \Eloquent
 */
class PostCategory extends Model { use HasFactory, SoftDeletes, LogsActivity; protected $fillable = ["name", "description", "is_default", "is_active", "is_featured", "sort_order", "color", "icon"]; protected $hidden = ["deleted_at"]; protected function casts(): array { return ["id" => "integer", "name" => "string", "description" => "string", "is_default" => "boolean", "is_active" => "boolean", "is_featured" => "boolean", "sort_order" => "integer", "color" => "string", "icon" => "string", "created_at" => "datetime", "updated_at" => "datetime", "deleted_at" => "datetime"]; } public function getActivitylogOptions(): LogOptions { return LogOptions::defaults()->logOnly(["name", "description", "is_default", "is_active", "is_featured"])->logOnlyDirty()->dontSubmitEmptyLogs(); } public static array $rules = ["name" => "required|string|max:255|unique:post_categories,name", "description" => "nullable|string|max:1000", "is_default" => "boolean", "is_active" => "boolean", "is_featured" => "boolean", "sort_order" => "nullable|integer|min:0", "color" => "nullable|string|max:7|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/", "icon" => "nullable|string|max:50"]; public function posts(): BelongsToMany { return $this->belongsToMany(Post::class, "post_assigned_categories", "post_categories_id", "post_id"); } public function activePosts(): BelongsToMany { return $this->posts()->where("posts.is_active", true); } public function featuredPosts(): BelongsToMany { return $this->posts()->where("posts.is_featured", true); } public function recentPosts(): BelongsToMany { return $this->posts()->where("posts.created_at", ">=", now()->subDays(30)); } public function scopeActive($query) { return $query->where("is_active", true); } public function scopeInactive($query) { return $query->where("is_active", false); } public function scopeFeatured($query) { return $query->where("is_featured", true); } public function scopeNonFeatured($query) { return $query->where("is_featured", false); } public function scopeDefault($query) { return $query->where("is_default", true); } public function scopeCustom($query) { return $query->where("is_default", false); } public function scopeSearch($query, string $term) { return $query->where(function ($q) use ($term) { $q->where("name", "like", "%" . $term . "%")->orWhere("description", "like", "%" . $term . "%"); }); } public function scopeRecent($query, int $days = 30) { return $query->where("created_at", ">=", now()->subDays($days)); } public function scopeOld($query, int $days = 365) { return $query->where("created_at", "<=", now()->subDays($days)); } public function scopePopular($query, int $limit = 10) { return $query->withCount(["posts" => function ($q) { $q->where("posts.is_active", true); }])->orderBy("posts_count", "desc")->limit($limit); } public function scopeAlphabetical($query) { return $query->orderBy("name", "asc"); } public function scopeOrdered($query) { return $query->orderBy("sort_order", "asc")->orderBy("name", "asc"); } public function scopeWithPosts($query) { return $query->has("posts"); } public function scopeWithoutPosts($query) { return $query->doesntHave("posts"); } public function scopeWithActivePosts($query) { return $query->whereHas("posts", function ($q) { $q->where("posts.is_active", true); }); } public function scopeWithFeaturedPosts($query) { return $query->whereHas("posts", function ($q) { $q->where("posts.is_featured", true); }); } public function scopeWithPostCounts($query) { return $query->withCount(["posts", "posts as active_posts_count" => function ($q) { $q->where("posts.is_active", true); }, "posts as featured_posts_count" => function ($q) { $q->where("posts.is_featured", true); }]); } public function scopeByColor($query, string $color) { return $query->where("color", $color); } public function scopeWithIcons($query) { return $query->whereNotNull("icon"); } public function scopeWithoutIcons($query) { return $query->whereNull("icon"); } public function scopeEmpty($query) { return $query->withCount("posts")->having("posts_count", "=", 0); } public function scopeNameLike($query, string $name) { return $query->where("name", "like", "%" . $name . "%"); } public static function getCachedActive(): \Illuminate\Database\Eloquent\Collection { return Cache::remember("post_categories.active", now()->addHours(6), function () { return static::active()->ordered()->get(); }); } public static function getCachedDefault(): \Illuminate\Database\Eloquent\Collection { return Cache::remember("post_categories.default", now()->addHours(12), function () { return static::default()->active()->ordered()->get(); }); } public static function getCachedFeatured(): \Illuminate\Database\Eloquent\Collection { return Cache::remember("post_categories.featured", now()->addHours(6), function () { return static::featured()->active()->ordered()->get(); }); } public static function getCachedPopular(int $limit = 10): \Illuminate\Database\Eloquent\Collection { return Cache::remember("post_categories.popular.{$limit}", now()->addHours(6), function () use ($limit) { return static::popular($limit)->active()->get(); }); } public static function getCachedTrending(int $days = 30, int $limit = 10): \Illuminate\Database\Eloquent\Collection { return Cache::remember("post_categories.trending.{$days}.{$limit}", now()->addHours(3), function () use ($days, $limit) { return static::trending($days, $limit)->active()->get(); }); } public function isDefault(): bool { return $this->is_default; } public function isCustom(): bool { return !$this->is_default; } public function isActive(): bool { return $this->is_active; } public function isFeatured(): bool { return $this->is_featured; } public function getDisplayNameAttribute(): string { $name = $this->name; if ($this->posts_count !== null) { $name .= " ({$this->posts_count})"; } return $name; } public function getPostsCountAttribute(): int { return $this->posts()->count(); } public function getActivePostsCountAttribute(): int { return $this->activePosts()->count(); } public function getFeaturedPostsCountAttribute(): int { return $this->featuredPosts()->count(); } public function getRecentPostsCountAttribute(): int { return $this->recentPosts()->count(); } public function getBadgeHtmlAttribute(): string { $color = $this->color ?: "#6c757d"; $icon = $this->icon ? "<i class=\"{$this->icon} me-1\"></i>" : ""; return "<span class=\"badge\" style=\"background-color: {$color};\">{$icon}{$this->name}</span>"; } public function getIconHtmlAttribute(): ?string { return $this->icon ? "<i class=\"{$this->icon}\"></i>" : null; } public function getSlugAttribute(): string { return \Str::slug($this->name); } public function hasPosts(): bool { return $this->posts()->count() > 0; } public function hasActivePosts(): bool { return $this->activePosts()->count() > 0; } public function hasFeaturedPosts(): bool { return $this->featuredPosts()->count() > 0; } public function hasIcon(): bool { return !empty($this->icon); } public function hasColor(): bool { return !empty($this->color); } public function getStatsAttribute(): array { return ["total_posts" => $this->posts()->count(), "active_posts" => $this->activePosts()->count(), "featured_posts" => $this->featuredPosts()->count(), "recent_posts" => $this->recentPosts()->count(), "is_popular" => $this->posts()->count() >= 5, "is_trending" => $this->recentPosts()->count() >= 3, "created_days_ago" => $this->created_at?->diffInDays(now())]; } public function clearCaches(): void { $cacheKeys = ["post_categories.active", "post_categories.default", "post_categories.featured"]; for ($i = 5; $i <= 20; $i += 5) { $cacheKeys[] = "post_categories.popular.{$i}"; } foreach ($cacheKeys as $key) { Cache::forget($key); } } protected static function boot() { parent::boot(); static::saved(function ($model) { $model->clearCaches(); }); static::deleted(function ($model) { $model->clearCaches(); }); static::restored(function ($model) { $model->clearCaches(); }); } }
