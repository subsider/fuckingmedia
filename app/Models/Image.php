<?php

namespace App\Models;

class Image extends BaseModel
{
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function artists()
    {
        return $this->morphedByMany(Artist::class, 'imageable');
    }
}
