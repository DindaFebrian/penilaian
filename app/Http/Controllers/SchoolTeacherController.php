<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\SchoolTeacher;
use Illuminate\Http\Request;

class SchoolTeacherController extends Controller
{
    private function userSchool(): School { return School::where('user_id', auth()->id())->firstOrFail(); }

    public function store(Request $request) {
        $school = $this->userSchool();
        $data = $request->validate([
            'nama'=>'required|max:120', 'nip_nik'=>'nullable|max:40',
            'pangkat_golongan'=>'nullable|max:60', 'jabatan'=>'nullable|max:100',
            'sertifikasi'=>'nullable|boolean',
        ]);
        $data['sertifikasi'] = (bool)($data['sertifikasi'] ?? false);
        $school->teachers()->create($data);
         return redirect()->route('schools.my')->with('success','Guru ditambahkan.');
    }

    public function update(Request $request, SchoolTeacher $teacher) {
        abort_unless($teacher->school_id === $this->userSchool()->id, 403);
        $data = $request->validate([
            'nama'=>'required|max:120', 'nip_nik'=>'nullable|max:40',
            'pangkat_golongan'=>'nullable|max:60', 'jabatan'=>'nullable|max:100',
            'sertifikasi'=>'nullable|boolean',
        ]);
        $teacher->update(['sertifikasi'=>(bool)($data['sertifikasi'] ?? false)] + $data);
        return back()->with('success','Data guru diperbarui.');
    }

    public function destroy(SchoolTeacher $teacher) {
        abort_unless($teacher->school_id === $this->userSchool()->id, 403);
        $teacher->delete();
        return back()->with('success','Guru dihapus.');
    }
}
