<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SallerSyncApiFile extends Model
{
    use HasFactory;
    protected $guarded= [];
    protected $table='sallers_sync_api_file';

    public function storage()
    {
        return $this->belongsTo(Storage::class, 'storage_id', 'id');
    }
}
