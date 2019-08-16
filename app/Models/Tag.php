<?php

namespace App\Models;

class Tag extends BaseModel
{
    public function artists()
    {
        return $this->morphedByMany(Tag::class, 'taggable');
    }
}
