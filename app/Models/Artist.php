<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Spatie\SchemalessAttributes\SchemalessAttributes;

class Artist extends BaseModel
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
}
