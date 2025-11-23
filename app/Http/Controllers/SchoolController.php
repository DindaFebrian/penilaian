<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;

class SchoolController extends Controller
{

    // PENGAWAS: daftar semua sekolah
    public function index() {
        $schools = School::latest()->paginate(15);
        return view('schools.index', compact('schools'));
    }

    // SEKOLAH: panel milik sekolah (otomatis by user)
    public function mySchool(Request $request) {
        $school = School::firstOrCreate(
            ['user_id' => $request->user()->id],
            ['nama' => 'Nama Sekolah', 'status_kepemilikan' => 'Swasta']
        );
        $this->recompute($school);
        $docTypes = ['RKS','RKAS','EDS'];
        return view('schools.show', [
            'school'=>$school->fresh(), 'docTypes'=>$docTypes,
            'canEdit'=>true, 'canVerify'=>false
        ]);
    }

    // PENGAWAS: lihat detail satu sekolah
    public function show(School $school) {
        $this->recompute($school);

        return view('schools.show', [
            'school'     => $school->fresh(),
            'docTypes'   => ['RKS','RKAS','EDS'],
            'canEdit'    => false,
            'canVerify'  => auth()->user()->hasRole('pengawas')
                && (
                    $school->review_status === 'pending' ||
                    ($school->review_status === 'rejected' && $school->updatedAfterReview())
                ),
            'updatedAfterReview' => $school->updatedAfterReview(),
        ]);
    }


    // SEKOLAH: update profil
    public function updateProfile(Request $request) {
    $user = $request->user();

    // kalau baris belum ada (jarang, tapi jaga-jaga), buat dulu
    $school = $user->school ?? $user->school()->create([
        'nama' => 'Nama Sekolah',
        'status_kepemilikan' => 'Swasta',
    ]);

    $data = $request->validate([
        'nama'=>'required|max:150',
        'npsn'=>'nullable|max:20',
        'jenjang'=>'nullable|max:20',
        'status_kepemilikan'=>'nullable|max:20',
        'tanggal_sk_sekolah'=>'nullable|date',
        'alamat'=>'nullable|max:255',
        'kepala_sekolah'=>'nullable|max:120',
        'email'=>'nullable|email|max:150',
    ]);

    $school->update($data);
    $this->recompute($school);

    // redirect ke panel supaya pasti 200 (bukan back ke URL non-GET)
    return redirect()->route('schools.my')->with('success','Profil sekolah diperbarui.');
}
    // hitung kelengkapan (dipanggil otomatis)
    private function recompute(School $school): void {
        $school->forceFill([
            'complete_profile' => filled($school->nama) && filled($school->alamat) && filled($school->kepala_sekolah),
            'complete_guru'    => $school->teachers()->count() > 0,
            'complete_siswa'   => $school->studentStats()->count() > 0,
            'complete_dokumen' => $school->documents()->whereIn('jenis',['RKS','RKAS','EDS'])->count() >= 3,
            'complete_sarpras' => $school->facilities()->count() > 0,
        ])->save();
    }
}
