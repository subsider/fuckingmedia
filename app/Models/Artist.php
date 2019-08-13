<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\SchemalessAttributes\SchemalessAttributes;

class Artist extends BaseModel
{
    use Sluggable;

    public $casts = [
        'listeners' => 'array',
        'playcount' => 'array',
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

    public function sluggable(): array
    {
        return ['slug' => ['source' => 'name']];
    }

    public function services(): MorphMany
    {
        return $this->morphMany(Service::class, 'model');
    }
}
