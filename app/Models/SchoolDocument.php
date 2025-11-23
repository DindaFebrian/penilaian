<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolDocument extends Model
{
    protected $fillable = [
        'school_id',
        'jenis',           // RKS, RKAS, EDS, LAINNYA
        'nama',
        'file_path',
        'tanggal_upload',
    ];

    protected $casts = [
        'tanggal_upload' => 'date',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
