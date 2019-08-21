<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;

class Company extends BaseModel
{
    use Sluggable;

    public function sluggable(): array
    {
        return ['slug' => ['source' => ['name']]];
    }
}
