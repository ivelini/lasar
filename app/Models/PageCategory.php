<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageCategory extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function pages()
    {
        return $this->hasMany(Page::class, 'category_id', 'id');
    }

    public function seo()
    {
        return $this->morphOne(Seo::class, 'seoable');
    }
}
