<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Evaluation;
use App\Models\EvaluationItem;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View; // only if your method has : View


class PengawasEvaluationController extends Controller
{
    /**
     * Definisi aspek & indikator (KEY unik).
     */
    public const ASPECTS = [
        'manajemen_sekolah' => [
            'label' => 'Penilaian Aspek Manajemen Sekolah',
            'indicators' => [
                'kepemimpinan'     => 'Kepemimpinan kepala sekolah',
                'perencanaan'      => 'Perencanaan program tingkat satuan',
                'tindak_lanjut'    => 'Tindak lanjut dan pengikutsertaan',
                'sarana_prasarana' => 'Pengelolaan sarana & prasarana',
            ],
        ],

        'kesiswaan' => [
            'label' => 'Aspek Kesiswaan',
            'indicators' => [
                'data_absensi'   => 'Data jumlah siswa dan rekap absensi',
                'pembinaan'      => 'Pembinaan kesiswaan (OSIS, ekstrakurikuler, bimbingan konseling)',
                'kedisiplinan'   => 'Ketertiban dan kedisiplinan siswa',
            ],
        ],

        'pendidik_tendik' => [
            'label' => 'Aspek Pendidik dan Tenaga Kependidikan',
            'indicators' => [
                'kualifikasi_kompetensi' => 'Kualifikasi dan kompetensi guru',
                'beban_kerja'            => 'Beban kerja dan distribusi tugas',
                'pkb_pelatihan'          => 'Program pengembangan profesi guru (PKB, pelatihan)',
                'evaluasi_kinerja'       => 'Evaluasi kinerja guru dan tenaga kependidikan',
            ],
        ],

        'sarpras' => [
            'label' => 'Aspek Sarana dan Prasarana',
            'indicators' => [
                'kondisi_ruang'   => 'Kondisi fisik ruang kelas, laboratorium, perpustakaan',
                'sanitasi'        => 'Ketersediaan & kelayakan sanitasi/kebersihan',
                'pemeliharaan'    => 'Pemeliharaan dan penggunaan fasilitas',
            ],
        ],

        'keuangan' => [
            'label' => 'Aspek Keuangan dan Pembiayaan',
            'indicators' => [
                'transparansi_bos'    => 'Transparansi & akuntabilitas dana BOS',
                'perencanaan_laporan' => 'Perencanaan dan pelaporan anggaran',
                'kesesuaian_rks'      => 'Kesesuaian penggunaan dana dengan RKS',
            ],
        ],

        'hubmas' => [
            'label' => 'Aspek Hubungan Sekolah dengan Masyarakat',
            'indicators' => [
                'partisipasi_komite' => 'Partisipasi komite sekolah',
                'hub_orangtua'       => 'Hubungan sekolah-orang tua/wali',
                'kemitraan'          => 'Kemitraan dengan lembaga luar',
            ],
        ],

        'inovasi_digital' => [
            'label' => 'Aspek Inovasi dan Digitalisasi',
            'indicators' => [
                'ti_pembelajaran'   => 'Pemanfaatan TI dalam manajemen & pembelajaran',
                'digitalisasi_prog' => 'Program digitalisasi administrasi/pembelajaran',
                'keamanan_data'     => 'Keamanan data dan sistem informasi',
            ],
        ],

        // Placeholder (tanpa indikator)
        'kurikulum_pembelajaran' => ['label' => 'Aspek Kurikulum dan Pembelajaran', 'indicators' => []],
        'kelembagaan'            => ['label' => 'Aspek Kelembagaan',                'indicators' => []],
        'peserta_guru'           => ['label' => 'Aspek Peserta Didik & Tendik',     'indicators' => []],
    ];

    /** Form penilaian (buat draft jika belum ada untuk hari ini) */
    public function create(School $school) // : View  (keep the return type only if you imported it)
{
    $uid = (int) Auth::id(); // safer for IDEs

    $evaluation = Evaluation::firstOrCreate(
        [
            'school_id'   => $school->id,
            'pengawas_id' => $uid,
            'tanggal'     => now()->toDateString(),
        ],
        ['status' => 'draft']
    );

    $evaluation->load('items');

    return view('pengawas.evaluations.form', [
        'school'     => $school,
        'evaluation' => $evaluation,
        'aspects'    => self::ASPECTS,
    ]);
}


    /** Simpan (draft/submit) + auto-complete Visit bila submit */
    public function store(School $school, Request $request)
    {
        $pengawasId = (int) $request->user()->id;

        $evaluation = Evaluation::firstOrCreate(
            [
                'school_id'   => $school->id,
                'pengawas_id' => $pengawasId,
                'tanggal'     => now()->toDateString(),
            ],
            ['status' => 'draft']
        );

        $scores = $request->input('items', []);   // items[aspect][indicator] = A/B/C/D
        $valid  = ['A','B','C','D'];

        foreach (self::ASPECTS as $aspectKey => $def) {
            foreach (($def['indicators'] ?? []) as $indKey => $label) {
                $score = data_get($scores, "$aspectKey.$indKey");

                if ($score && !in_array($score, $valid, true)) {
                    return back()
                        ->withErrors(['items' => "Nilai untuk \"$label\" tidak valid."])
                        ->withInput();
                }

                $item = EvaluationItem::firstOrNew([
                    'evaluation_id' => $evaluation->id,
                    'aspect'        => $aspectKey,
                    'indicator'     => $indKey,
                ]);

                $item->score = $score;

                // Evidence (opsional)
                if ($request->hasFile("evidence.$aspectKey.$indKey")) {
                    $path = $request->file("evidence.$aspectKey.$indKey")
                        ->store("evaluations/{$evaluation->id}/$aspectKey", 'public');
                    $item->evidence_path = 'storage/' . $path;
                }

                $item->save();
            }
        }

        // Catatan umum
        $evaluation->overall_notes = $request->string('overall_notes')->toString();

        // Submit?
        if ($request->boolean('submit')) {
            $evaluation->status = 'submitted';
        }

        $evaluation->save();

        // Auto-complete Visit ketika sudah submitted
        if ($evaluation->status === 'submitted') {
            $visit = Visit::where('school_id', $school->id)
                ->where('pengawas_id', $pengawasId)
                ->whereIn('status', ['scheduled', 'accepted']) // aman meski enum tidak punya 'accepted'
                ->orderByDesc('visit_date')
                ->orderByDesc('visit_time')
                ->first();

            if ($visit) {
                $evaluation->load('items');

                $point     = ['A' => 4, 'B' => 3, 'C' => 2, 'D' => 1];
                $gradeText = ['A' => 'Sangat Baik', 'B' => 'Baik', 'C' => 'Cukup', 'D' => 'Perlu Perbaikan'];

                $nums = [];
                foreach ($evaluation->items as $it) {
                    if (isset($point[$it->score])) {
                        $nums[] = $point[$it->score];
                    }
                }

                $overallAvg   = $nums ? round(array_sum($nums) / count($nums), 2) : null;
                $overallGrade = is_null($overallAvg) ? '-' : ($overallAvg >= 3.5 ? 'A' : ($overallAvg >= 2.5 ? 'B' : ($overallAvg >= 1.5 ? 'C' : 'D')));
                $overallText  = $overallGrade === '-' ? '-' : $gradeText[$overallGrade];

                $visit->update([
                    'status'         => 'done',
                    'completed_at'   => now(),
                    'report_summary' => 'Predikat: ' . $overallGrade . ' (' . $overallText . ')'
                        . (!is_null($overallAvg) ? ', Rata-rata: ' . $overallAvg : ''),
                ]);
            }
        }

        return back()->with(
            'success',
            $evaluation->status === 'submitted'
                ? 'Penilaian disimpan & dikirim. Visitasi otomatis masuk Riwayat.'
                : 'Draft penilaian disimpan.'
        );
    }

    /** Tampilkan laporan terakhir untuk sekolah */
    public function report(School $school)
    {
        $evaluation = Evaluation::with(['items', 'pengawas'])
            ->where('school_id', $school->id)
            ->latest('tanggal')
            ->firstOrFail();

        $point     = ['A' => 4, 'B' => 3, 'C' => 2, 'D' => 1];
        $gradeText = ['A' => 'Sangat Baik', 'B' => 'Baik', 'C' => 'Cukup', 'D' => 'Perlu Perbaikan'];
        $colorMap  = ['A' => '#10B981', 'B' => '#3B82F6', 'C' => '#F59E0B', 'D' => '#EF4444'];

        $detail  = [];
        $allNums = [];

        foreach (self::ASPECTS as $akey => $adef) {
            $indicators = $adef['indicators'] ?? [];
            if (empty($indicators)) {
                continue; // lewati placeholder tanpa indikator
            }

            $rows = [];
            $nums = [];

            foreach ($indicators as $ikey => $label) {
                $item = $evaluation->items
                    ->where('aspect', $akey)
                    ->where('indicator', $ikey)
                    ->first();

                $score = $item->score ?? null;
                if ($score && isset($point[$score])) {
                    $nums[] = $point[$score];
                }

                $rows[] = [
                    'label'    => $label,
                    'score'    => $score,
                    'evidence' => $item->evidence_path ?? null,
                ];
            }

            $avg   = $nums ? round(array_sum($nums) / count($nums), 2) : null;
            $grade = is_null($avg) ? '-' : ($avg >= 3.5 ? 'A' : ($avg >= 2.5 ? 'B' : ($avg >= 1.5 ? 'C' : 'D')));

            $detail[] = [
                'key'        => $akey,
                'label'      => $adef['label'],
                'rows'       => $rows,
                'avg'        => $avg,
                'grade'      => $grade,
                'grade_text' => $grade === '-' ? '-' : $gradeText[$grade],
            ];

            $allNums = array_merge($allNums, $nums);
        }

        $overallAvg   = $allNums ? round(array_sum($allNums) / count($allNums), 2) : null;
        $overallGrade = is_null($overallAvg) ? '-' : ($overallAvg >= 3.5 ? 'A' : ($overallAvg >= 2.5 ? 'B' : ($overallAvg >= 1.5 ? 'C' : 'D')));
        $overallText  = $overallGrade === '-' ? '-' : $gradeText[$overallGrade];

        return view('pengawas.evaluations.report', [
            'school'       => $school,
            'evaluation'   => $evaluation,
            'detail'       => $detail,
            'overallAvg'   => $overallAvg,
            'overallGrade' => $overallGrade,
            'overallText'  => $overallText,
            'colorMap'     => $colorMap,
        ]);
    }
}
