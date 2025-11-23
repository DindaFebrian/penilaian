<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\Evaluation;
use Illuminate\Http\Request;

class PengawasVisitController extends Controller
{
    /**
     * Jadwal mendatang & riwayat milik pengawas yang login.
     */
    public function index(Request $request)
    {
        $uid = $request->user()->id;

        // Jadwal mendatang: scheduled/accepted
        $upcoming = Visit::with('school')
            ->where('pengawas_id', $uid)
            ->whereIn('status', ['scheduled','accepted'])
            ->orderBy('visit_date')
            ->orderBy('visit_time')
            ->get();

        // Riwayat: done/rejected
        $history = Visit::with('school')
            ->where('pengawas_id', $uid)
            ->whereIn('status', ['done','rejected'])
            ->orderByDesc('visit_date')
            ->paginate(10);

        return view('pengawas.visits.index', compact('upcoming','history'));
    }

    /**
     * Terima penugasan (hanya saat masih scheduled & belum pernah decline).
     */
    public function accept(Request $request, Visit $visit)
{
    $this->authorizeVisit($request, $visit);

    if ($visit->status !== 'scheduled' || $visit->declined_at) {
        return back()->withErrors('Penugasan tidak bisa diterima.');
    }

    $visit->accepted_at = now();
    // HAPUS baris berikut karena enum tidak punya 'accepted':
    // $visit->status = 'accepted';
    $visit->save();

    return back()->with('success', 'Tugas visitasi diterima.');
}

    /**
     * Tolak penugasan.
     * - Jika BELUM diterima (status scheduled & belum accepted_at) → kembalikan ke antrian admin (requested) & lepas pengawas_id.
     * - Jika SUDAH diterima → tandai rejected (agar masuk riwayat pengawas).
     */
    public function decline(Request $request, Visit $visit)
    {
        $this->authorizeVisit($request, $visit);

        $data = $request->validate([
            'decline_reason' => ['required','string','max:255'],
        ]);

        if (in_array($visit->status, ['rejected','done'])) {
            return back()->withErrors('Penugasan sudah tidak aktif.');
        }

        $visit->declined_at    = now();
        $visit->decline_reason = $data['decline_reason'];

        if ($visit->status === 'scheduled' && is_null($visit->accepted_at)) {
            // kembalikan ke antrian admin
            $visit->status      = 'requested';
            $visit->pengawas_id = null; // lepas penugasan
            $visit->save();

            return back()->with('success','Penugasan dikembalikan ke admin untuk dijadwalkan ulang.');
        }

        // sudah diterima → masuk riwayat pengawas sebagai rejected
        $visit->status = 'rejected';
        $visit->save();

        return back()->with('success','Penugasan ditolak.');
    }

    /**
     * Selesaikan visitasi (opsional upload file & ringkasan).
     * Jika ringkasan kosong, sistem coba isi otomatis dari evaluation submitted terbaru pengawas ini untuk sekolah tsb.
     */
    public function complete(Request $request, Visit $visit)
    {
        $this->authorizeVisit($request, $visit);

        $data = $request->validate([
            'report_file'    => ['nullable','file','mimes:pdf,doc,docx,png,jpg,jpeg','max:5120'],
            'report_summary' => ['nullable','string','max:1000'],
        ]);

        // Upload file (opsional)
        if ($request->hasFile('report_file')) {
            $path = $request->file('report_file')->store("visit_reports/{$visit->id}", 'public');
            $visit->report_file = 'storage/'.$path;
        }

        // Ringkasan (pakai input; kalau kosong → ambil dari evaluation terbaru)
        $summary = $data['report_summary'] ?? null;

        if (!$summary) {
            $evaluation = Evaluation::where('school_id', $visit->school_id)
                ->where('pengawas_id', $request->user()->id)
                ->where('status', 'submitted')
                ->latest('tanggal')
                ->first();

            if ($evaluation) {
                $point     = ['A'=>4,'B'=>3,'C'=>2,'D'=>1];
                $gradeText = ['A'=>'Sangat Baik','B'=>'Baik','C'=>'Cukup','D'=>'Perlu Perbaikan'];

                $evaluation->load('items');
                $nums = [];
                foreach ($evaluation->items as $it) {
                    if (!empty($point[$it->score])) { $nums[] = $point[$it->score]; }
                }
                $overallAvg   = count($nums) ? round(array_sum($nums)/count($nums), 2) : null;
                $overallGrade = is_null($overallAvg) ? '-' : ($overallAvg >= 3.5 ? 'A' : ($overallAvg >= 2.5 ? 'B' : ($overallAvg >= 1.5 ? 'C' : 'D')));
                $overallText  = $overallGrade === '-' ? '-' : $gradeText[$overallGrade];

                $summary = 'Predikat: '.$overallGrade.' ('.$overallText.')'
                         . (!is_null($overallAvg) ? ', Rata-rata: '.$overallAvg : '');
            }
        }

        if ($summary) {
            $visit->report_summary = $summary;
        }

        $visit->status       = 'done';
        $visit->completed_at = now();
        $visit->save();

        return back()->with('success','Visitasi ditandai selesai.');
    }

    /**
     * Pastikan visit ini milik pengawas yang login.
     */
    private function authorizeVisit(Request $request, Visit $visit): void
    {
        if ((int)$visit->pengawas_id !== (int)$request->user()->id) {
            abort(403, 'Anda tidak berhak mengelola visitasi ini.');
        }
    }
}
