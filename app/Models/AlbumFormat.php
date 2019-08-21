<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlbumFormat extends BaseModel
{
    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }
}
