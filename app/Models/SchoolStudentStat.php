<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolStudentStat extends Model
{
    protected $fillable = [
        'school_id',
        'tahun_ajaran',
        'jumlah_kelas',
        'laki_laki',
        'perempuan',
        'jumlah_siswa',
        'file_path',
    ];

    protected $casts = [
        'jumlah_kelas' => 'integer',
        'laki_laki'    => 'integer',
        'perempuan'    => 'integer',
        'jumlah_siswa' => 'integer',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
