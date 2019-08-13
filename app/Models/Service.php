<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Service extends BaseModel
{
    protected $dates = [
        'crawled_at',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
