<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tire extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function modelPosition()
    {
        return $this->belongsTo(ModelPosition::class, 'model_id', 'id');
    }

    public function vendor()
    {
        return $this->modelPosition()->vendor();
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function vendorsCode()
    {
        return $this->hasMany(VendorCode::class, 'tire_id', 'id');
    }

    public function sallersCode()
    {
        return $this->hasMany(SallerCode::class, 'tire_id', 'id');
    }


}
