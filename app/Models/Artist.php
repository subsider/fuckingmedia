<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\SchemalessAttributes\SchemalessAttributes;

/**
 * App\Models\Artist
 *
 * @property int $id
 * @property string|null $mbid
 * @property string $name
 * @property string|null $slug
 * @property array|null $listeners
 * @property array|null $playcount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Service[] $services
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Artist findSimilarSlugs($attribute, $config, $slug)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Artist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Artist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Artist query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Artist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Artist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Artist whereListeners($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Artist whereMbid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Artist whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Artist wherePlaycount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Artist whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Artist whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Artist withListeners()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Artist withPlaycount()
 * @mixin \Eloquent
 */
class Artist extends BaseModel
{
    use Sluggable;

    public $casts = [
        'listeners' => 'array',
        'playcount' => 'array',
        'streamable' => 'array',
    ];

    public function getListenersAttribute(): SchemalessAttributes
    {
        return SchemalessAttributes::createForModel($this, 'listeners');
    }

    public function scopeWithListeners(): Builder
    {
        return SchemalessAttributes::scopeWithSchemalessAttributes('listeners');
    }

    public function getPlaycountAttribute(): SchemalessAttributes
    {
        return SchemalessAttributes::createForModel($this, 'playcount');
    }

    public function scopeWithPlaycount(): Builder
    {
        return SchemalessAttributes::scopeWithSchemalessAttributes('playcount');
    }

    public function getStreamableAttribute(): SchemalessAttributes
    {
        return SchemalessAttributes::createForModel($this, 'streamable');
    }

    public function scopeWithStreamable(): Builder
    {
        return SchemalessAttributes::scopeWithSchemalessAttributes('streamable');
    }

    public function sluggable(): array
    {
        return ['slug' => ['source' => 'name']];
    }

    public function services(): MorphMany
    {
        return $this->morphMany(Service::class, 'model');
    }

    public function images(): MorphToMany
    {
        return $this->morphToMany(Image::class, 'imageable');
    }
}
