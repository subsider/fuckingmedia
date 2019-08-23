<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Company extends BaseModel
{
    use Sluggable;

    public function sluggable(): array
    {
        return ['slug' => ['source' => ['name']]];
    }

    public function services(): MorphMany
    {
        return $this->morphMany(Service::class, 'model');
    }

    public function images(): MorphToMany
    {
        return $this->morphToMany(Image::class, 'imageable');
    }
}
