<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;

/**
 * App\Models\Provider
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Provider findSimilarSlugs($attribute, $config, $slug)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Provider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Provider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Provider query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Provider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Provider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Provider whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Provider whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Provider whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Provider extends BaseModel
{
    use Sluggable;

    public function sluggable(): array
    {
        return ['slug' => ['source' => 'name']];
    }
}
