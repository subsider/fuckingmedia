<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\SchemalessAttributes\SchemalessAttributes;

class ArtistTrack extends Pivot
{
    protected $table = 'artist_track';

    public $casts = [
        'rank' => 'array',
    ];

    public function getRankAttribute(): SchemalessAttributes
    {
        return SchemalessAttributes::createForModel($this, 'rank');
    }

    public function scopeWithRank(): Builder
    {
        return SchemalessAttributes::scopeWithSchemalessAttributes('rank');
    }
}
