<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelPosition extends Model
{
    use HasFactory;

    protected $table = "models";
    protected $guarded = [];

    public function tires()
    {
        return $this->hasMany(Tire::class, 'model_id', 'id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id', 'id');
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
