<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SallerCode extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function prices()
    {
        return $this->morphMany(Price::class, 'priceable');
    }

    public function saller()
    {
        return $this->belongsTo(Saller::class, 'saller_id', 'id');
    }

    public function tire()
    {
        return $this->belongsTo(Tire::class, 'tire_id', 'id');
    }
}
