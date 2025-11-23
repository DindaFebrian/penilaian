<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\SchoolDocument;
use Illuminate\Http\Request;

class SchoolDocumentController extends Controller
{
    private function userSchool(): School { return School::where('user_id', auth()->id())->firstOrFail(); }

    public function store(Request $request) {
        $school = $this->userSchool();
        $data = $request->validate([
            'jenis'=>'required|in:RKS,RKAS,EDS,LAINNYA',
            'nama'=>'nullable|max:120',
            'file'=>'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:8192',
        ]);
        $path = 'storage/'.$request->file('file')->store("schools/{$school->id}/docs",'public');
        $school->documents()->create([
            'jenis'=>$data['jenis'],'nama'=>$data['nama'] ?? $data['jenis'],
            'file_path'=>$path,'tanggal_upload'=>now()->toDateString()
        ]);
        return back()->with('success','Dokumen diunggah.');
    }

    public function destroy(SchoolDocument $document) {
        abort_unless($document->school_id === $this->userSchool()->id, 403);
        $document->delete();
        return back()->with('success','Dokumen dihapus.');
    }
}
