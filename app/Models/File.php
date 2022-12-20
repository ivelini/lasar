<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * Получить родительскую модель, к которой относится файл.
     */
    public function fileable()
    {
        return $this->morphTo();
    }
}
