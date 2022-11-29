<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saller extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function apiUrlsSaller()
    {
        return $this->hasMany(ApiUrlSaller::class, 'saller_id', 'id');
    }
}
