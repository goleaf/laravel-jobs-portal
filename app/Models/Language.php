<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * Class Language
 *
 * @version July 3, 2020, 9:12 am UTC
 *
 * @property int $id
 * @property string $language
 * @property string $iso_code
 * @property bool $is_default
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $candidates
 *
 * @method static Builder|Language newModelQuery()
 * @method static Builder|Language newQuery()
 * @method static Builder|Language query()
 * @method static Builder|Language whereCreatedAt($value)
 * @method static Builder|Language whereId($value)
 * @method static Builder|Language whereIsoCode($value)
 * @method static Builder|Language whereLanguage($value)
 * @method static Builder|Language whereIsDefault($value)
 * @method static Builder|Language whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Language extends Model
{
    public $table = 'languages';

    protected $fillable = [
        'language',
        'iso_code',
        'is_default',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'language' => 'string',
        'iso_code' => 'string',
        'is_default' => 'boolean',
    ];

    /**
     * Validation rules
     *
     * @var array<string, string>
     */
    public static $rules = [
        'language' => 'required|unique:languages,language|max:150',
        'iso_code' => 'required|unique:languages,iso_code|max:150',
    ];

    /**
     * Get all candidates that use this language
     *
     * @return BelongsToMany
     */
    public function candidates(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'candidate_language');
    }
}
