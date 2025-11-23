<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\School;
use Illuminate\Http\Request;

class SchoolVisitController extends Controller
{
    // Halaman pengajuan + list milik sekolah
    public function index(Request $request)
    {
        $school = School::where('user_id', $request->user()->id)->firstOrFail();

        $visits = Visit::with('pengawas')
            ->where('school_id', $school->id)
            ->latest()
            ->paginate(10);

        // ambil pengajuan terbaru
        $latest = Visit::where('school_id', $school->id)->latest()->first();

        // terkunci jika status TERJADWAL (disetujui admin)
        $locked = $latest && $latest->status === 'scheduled';

        return view('schools.visits', compact('school','visits','locked','latest'));
    }

    // Kirim pengajuan baru
    public function store(Request $request)
    {
        $school = School::where('user_id', $request->user()->id)->firstOrFail();

        // Cegah pengajuan baru jika masih requested/scheduled
        $latest = Visit::where('school_id', $school->id)->latest()->first();
        if ($latest && in_array($latest->status, ['scheduled','requested'])) {
            return back()
                ->withErrors(['visit' => 'Pengajuan sebelumnya sudah disetujui/masih diproses. Form dikunci sampai selesai atau ditolak.'])
                ->withInput();
        }

        $data = $request->validate([
            'visit_date' => 'required|date|after_or_equal:today',
            'visit_time' => 'required|date_format:H:i',
            'note'       => 'nullable|string|max:500',
        ]);

        Visit::create([
            'school_id'  => $school->id,
            'visit_date' => $data['visit_date'],
            'visit_time' => $data['visit_time'],
            'note'       => $data['note'] ?? null,
            'status'     => 'requested',
        ]);

        return back()->with('success','Pengajuan visitasi dikirim. Menunggu persetujuan admin.');
    }
}
