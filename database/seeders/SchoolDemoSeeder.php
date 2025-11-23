<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchoolDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    $school = \App\Models\School::create([
        'nama' => 'TK Penwari',
        'npsn' => '69096869',
        'jenjang' => 'TK',
        'status_kepemilikan' => 'Swasta',
        'tanggal_sk_sekolah' => '1977-09-01',
        'alamat' => 'Jl. Arif Rahman Hakim No.37 43214 Muka Jawa Barat',
        'kepala_sekolah' => 'N Jubedah S.Pd',
        'email' => 'tkpenwaricianjur@yahoo.com',
        'complete_profile' => true,
    ]);

    // Guru (contoh 4 baris sama seperti screenshoot)
    foreach (range(1,4) as $i) {
        \App\Models\SchoolTeacher::create([
            'school_id'=>$school->id,
            'nama'=>'Elis Suciati S.Pd',
            'nip_nik'=>'19770302200100207',
            'pangkat_golongan'=>'PENATA (III/c)',
            'jabatan'=>'Guru TK',
            'sertifikasi'=>true,
        ]);
    }

    // Rekap siswa + file opsional
    \App\Models\SchoolStudentStat::create([
        'school_id'=>$school->id,
        'tahun_ajaran'=>'2024/2025',
        'jumlah_kelas'=>3,
        'laki_laki'=>25,
        'perempuan'=>23,
        'jumlah_siswa'=>48,
        'file_path'=>null,
    ]);

    // Dokumen administrasi
    foreach (['RKS','RKAS','EDS'] as $jenis) {
        \App\Models\SchoolDocument::create([
            'school_id'=>$school->id,
            'jenis'=>$jenis,
            'nama'=>$jenis.' 2024',
            'file_path'=>'', // isi nanti setelah upload
            'tanggal_upload'=>now()->toDateString(),
        ]);
    }

    // Sarpras
    \App\Models\SchoolFacility::create([
        'school_id'=>$school->id,'item'=>'Ruang Kelas','jumlah'=>3,'kondisi'=>'Baik','keterangan'=>null
    ]);
    \App\Models\SchoolFacility::create([
        'school_id'=>$school->id,'item'=>'Toilet Siswa','jumlah'=>4,'kondisi'=>'Cukup','keterangan'=>'Perlu renovasi'
    ]);
    \App\Models\SchoolFacility::create([
        'school_id'=>$school->id,'item'=>'Air Bersih','jumlah'=>null,'kondisi'=>'Cukup','keterangan'=>null
    ]);

    // Tandai kelengkapan
    $school->update([
        'complete_guru'=>true,'complete_siswa'=>true,'complete_dokumen'=>true,'complete_sarpras'=>true
    ]);
}

}
