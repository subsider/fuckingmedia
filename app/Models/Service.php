<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends BaseModel
{
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function model()
    {
        return $this->morphTo();
    }
}
