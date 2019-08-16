<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Spatie\SchemalessAttributes\SchemalessAttributes;

class Related extends BaseModel
{
    public $casts = [
        'match' => 'array',
    ];

    public function getMatchAttribute(): SchemalessAttributes
    {
        return SchemalessAttributes::createForModel($this, 'match');
    }

    public function scopeWithMatch(): Builder
    {
        return SchemalessAttributes::scopeWithSchemalessAttributes('match');
    }
}
