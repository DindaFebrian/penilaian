<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\SchoolFacility;
use Illuminate\Http\Request;

class SchoolFacilityController extends Controller
{
    private function userSchool(): School { return School::where('user_id', auth()->id())->firstOrFail(); }

    public function store(Request $request) {
        $school = $this->userSchool();
        $data = $request->validate([
            'item'=>'required|max:120','jumlah'=>'nullable|integer|min:0',
            'kondisi'=>'nullable|in:Baik,Cukup,Rusak Ringan,Rusak Berat',
            'keterangan'=>'nullable|max:255','file'=>'nullable|file|mimes:pdf,jpg,jpeg,png|max:8192',
        ]);
        $path = $request->hasFile('file')
            ? 'storage/'.$request->file('file')->store("schools/{$school->id}/facilities",'public')
            : null;

        $school->facilities()->create([
            'item'=>$data['item'],'jumlah'=>$data['jumlah'] ?? null,
            'kondisi'=>$data['kondisi'] ?? null,'keterangan'=>$data['keterangan'] ?? null,
            'file_path'=>$path,
        ]);
        return back()->with('success','Data sarpras ditambahkan.');
    }

    public function destroy(SchoolFacility $facility) {
        abort_unless($facility->school_id === $this->userSchool()->id, 403);
        $facility->delete();
        return back()->with('success','Data sarpras dihapus.');
    }
}
