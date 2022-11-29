<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiUrlSaller extends Model
{
    use HasFactory;
    protected $table = 'api_url_sallers';
    protected $guarded = [];

    public function storage()
    {
        return $this->hasOne(Storage::class, 'api_url_saller_id', 'id');
    }

    public function saller()
    {
        return $this->belongsTo(Saller::class, 'saller_id', 'id');
    }

    public function label()
    {
        return $this->belongsTo(LabelImportCatalogService::class, 'label_id', 'id');
    }
}
