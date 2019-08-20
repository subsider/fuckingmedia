<?php

namespace App\Models;

class NameVariation extends BaseModel
{
    public function model()
    {
        return $this->morphTo();
    }
}
