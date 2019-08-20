<?php

namespace App\Models;

class Url extends BaseModel
{
    public function model()
    {
        return $this->morphTo();
    }
}
