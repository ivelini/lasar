<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    public function apiUrlSaller()
    {
        return $this->belongsTo(ApiUrlSaller::class, 'api_url_saller_id', 'id');
    }
}
