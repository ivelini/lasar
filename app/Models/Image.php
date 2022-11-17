<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * Получить родительскую модель (пользователя или поста), к которой относится изображение.
     */
    public function imageable()
    {
        return $this->morphTo();
    }
}
