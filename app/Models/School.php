<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model {

    protected $fillable = [
        'nama','npsn','jenjang','status_kepemilikan','tanggal_sk_sekolah',
        'alamat','kepala_sekolah','email',
        'complete_profile','complete_guru','complete_siswa','complete_dokumen','complete_sarpras',
        'review_status','reviewed_at','review_notes'
    ];
    protected $casts = [
        'complete_profile'=>'bool','complete_guru'=>'bool','complete_siswa'=>'bool',
        'complete_dokumen'=>'bool','complete_sarpras'=>'bool',
        'tanggal_sk_sekolah'=>'date','reviewed_at'=>'datetime'
    ];

    public function teachers(){ return $this->hasMany(SchoolTeacher::class); }
    public function studentStats(){ return $this->hasMany(SchoolStudentStat::class); }
    public function documents(){ return $this->hasMany(SchoolDocument::class); }
    public function facilities(){ return $this->hasMany(SchoolFacility::class); }
    public function user() { return $this->belongsTo(\App\Models\User::class); }
    public function latestDataUpdatedAt(): ?\Illuminate\Support\Carbon
    {
        // Ambil waktu update terbaru dari sekolah dan relasi-relasi
        $times = [
            $this->updated_at,
            $this->teachers()->max('updated_at'),
            $this->studentStats()->max('updated_at'),
            $this->documents()->max('updated_at'),
            $this->facilities()->max('updated_at'),
        ];

        $times = array_filter($times); // buang null
        return !empty($times) ? collect($times)->max() : null;
    }

    public function updatedAfterReview(): bool
    {
        if (!$this->reviewed_at) {
            // Belum pernah direview → anggap perlu diverifikasi/approve boleh
            return true;
        }
        $latest = $this->latestDataUpdatedAt();
        return $latest ? $latest->gt($this->reviewed_at) : false;
    }
}

