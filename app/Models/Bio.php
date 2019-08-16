<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Translatable\HasTranslations;

class Bio extends BaseModel
{
    use HasTranslations;

    public $translatable = ['summary', 'content', 'published_at'];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
