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
    ];

    public function getUrlAttribute(): SchemalessAttributes
    {
        return SchemalessAttributes::createForModel($this, 'url');
    }

    public function scopeWithUrl(): Builder
    {
        return SchemalessAttributes::scopeWithSchemalessAttributes('url');
    }
}
