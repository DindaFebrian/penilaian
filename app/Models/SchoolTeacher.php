<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolTeacher extends Model {
    protected $fillable = ['school_id','nama','nip_nik','pangkat_golongan','jabatan','sertifikasi'];
    protected $casts = ['sertifikasi'=>'bool'];
    public function school(){ return $this->belongsTo(School::class); }
}

