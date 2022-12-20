<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(PageCategory::class, 'category_id', 'id');
    }

    public function seo()
    {
        return $this->morphOne(Seo::class, 'seoable');
    }
}
