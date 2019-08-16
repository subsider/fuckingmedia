<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Spatie\SchemalessAttributes\SchemalessAttributes;

class Taggable extends BaseModel
{
    protected $table = 'taggables';

    protected $guarded = [];

    public $casts = [
        'url' => 'array',
        'match' => 'array',
    ];

    public function getUrlAttribute(): SchemalessAttributes
    {
        return SchemalessAttributes::createForModel($this, 'url');
    }

    public function scopeWithUrl(): Builder
    {
        return SchemalessAttributes::scopeWithSchemalessAttributes('url');
    }

    public function getMatchAttribute(): SchemalessAttributes
    {
        return SchemalessAttributes::createForModel($this, 'match');
    }

    public function scopeWithMatch(): Builder
    {
        return SchemalessAttributes::scopeWithSchemalessAttributes('match');
    }
}
