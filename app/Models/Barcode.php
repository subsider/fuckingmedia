<?php

namespace App\Models;

class Barcode extends BaseModel
{
    public function model()
    {
        return $this->morphTo();
    }
}
