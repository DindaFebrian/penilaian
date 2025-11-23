<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\Request;

class AdminVisitController extends Controller
{
    // daftar semua pengajuan
    public function index()
    {
        $visits = Visit::with(['school','pengawas'])
            ->latest()->paginate(20);
        $pengawas = User::role('pengawas')->orderBy('name')->get(['id','name']);
        return view('admin.visits.index', compact('visits','pengawas'));
    }

    // setujui + tetapkan pengawas + (opsional) ubah tgl/jam
    public function schedule(Request $request, Visit $visit)
    {
        $data = $request->validate([
            // 'visit_date' => 'required|date',
            // 'visit_time' => 'required|date_format:H:i',
            'pengawas_id'=> 'required|exists:users,id',
            'note'       => 'nullable|string|max:500',
        ]);

        $visit->update([
            // 'visit_date' => $data['visit_date'],
            // 'visit_time' => $data['visit_time'],
            'pengawas_id'=> $data['pengawas_id'],
            'approved_by'=> $request->user()->id,
            'note'       => $data['note'] ?? null,
            'status'     => 'scheduled',
        ]);

        return back()->with('success','Jadwal disetujui & pengawas ditetapkan.');
    }

    // tolak
    public function reject(Request $request, Visit $visit)
    {
        $data = $request->validate(['note'=>'required|string|max:500']);
        $visit->update([
            'approved_by'=> $request->user()->id,
            'note'       => $data['note'],
            'status'     => 'rejected',
        ]);
        return back()->with('success','Pengajuan ditolak.');
    }
}
