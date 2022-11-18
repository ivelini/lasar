<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function modelsPosition()
    {
        return $this->hasMany(ModelPosition::class);
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
