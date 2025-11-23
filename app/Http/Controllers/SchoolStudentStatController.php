<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;

class SchoolStudentStatController extends Controller
{
    private function userSchool(): School { return School::where('user_id', auth()->id())->firstOrFail(); }

    public function store(Request $request) {
        $school = $this->userSchool();
        $data = $request->validate([
            'tahun_ajaran'=>'nullable|max:9', 'jumlah_kelas'=>'required|integer|min:0',
            'laki_laki'=>'required|integer|min:0', 'perempuan'=>'required|integer|min:0',
            'jumlah_siswa'=>'required|integer|min:0', 'file'=>'nullable|file|max:8192',
        ]);
        $path = $request->hasFile('file')
            ? 'storage/'.$request->file('file')->store("schools/{$school->id}/students",'public')
            : null;

        $school->studentStats()->updateOrCreate(
            ['tahun_ajaran'=>$data['tahun_ajaran'] ?? now()->year.'/'.(now()->year+1)],
            ['jumlah_kelas'=>$data['jumlah_kelas'],'laki_laki'=>$data['laki_laki'],
             'perempuan'=>$data['perempuan'],'jumlah_siswa'=>$data['jumlah_siswa'],'file_path'=>$path]
        );

        return back()->with('success','Rekap siswa tersimpan.');
    }
}
