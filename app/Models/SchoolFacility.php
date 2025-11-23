<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolFacility extends Model
{
    protected $fillable = [
        'school_id',
        'item',
        'jumlah',
        'kondisi',
        'keterangan',
        'file_path',
    ];

    protected $casts = [
        'jumlah' => 'integer',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
