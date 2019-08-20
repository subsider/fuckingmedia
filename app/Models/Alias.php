<?php

namespace App\Models;

class Alias extends BaseModel
{
    public function model()
    {
        return $this->morphTo();
    }
}
