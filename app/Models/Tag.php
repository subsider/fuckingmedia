<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\SchemalessAttributes\SchemalessAttributes;

class Tag extends BaseModel
{
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

    public function artists()
    {
        return $this->morphedByMany(Tag::class, 'taggable');
    }

    public function bios(): MorphMany
    {
        return $this->morphMany(Bio::class, 'model');
    }
}
