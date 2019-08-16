<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\SchemalessAttributes\SchemalessAttributes;

class Album extends BaseModel
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
        return ['slug' => ['source' => ['artist_name', 'name']]];
    }

    public function artists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class);
    }

    public function services(): MorphMany
    {
        return $this->morphMany(Service::class, 'model');
    }

    public function bios(): MorphMany
    {
        return $this->morphMany(Bio::class, 'model');
    }

    public function images(): MorphToMany
    {
        return $this->morphToMany(Image::class, 'imageable');
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable')
            ->withPivot('url');
    }
}
