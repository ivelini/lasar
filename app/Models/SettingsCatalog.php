<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingsCatalog extends Model
{
    use HasFactory;
    protected $table = 'settings_catalog';
    protected $guarded = [];
}
