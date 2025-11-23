<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evaluation;
use App\Models\School;
use App\Http\Controllers\PengawasEvaluationController;

class SchoolEvaluationReportController extends Controller
{
    /**
     * Laporan visitasi TERBARU untuk sekolah milik user (role: sekolah).
     * Route: schools.report.me
     */
    public function me(Request $request)
    {
        $school = $request->user()->school ?? abort(404, 'Sekolah tidak ditemukan.');
        $evaluation = Evaluation::with(['items','pengawas'])
            ->where('school_id', $school->id)
            ->where('status', 'submitted')
            ->latest('tanggal')
            ->firstOrFail();

        $aspects = PengawasEvaluationController::ASPECTS;
        $built   = $this->buildReport($evaluation, $aspects);

        // Reuse view laporan yang sudah ada
        return view('pengawas.evaluations.report', array_merge($built, [
            'school'     => $school,
            'evaluation' => $evaluation,
        ]));
    }

    /**
     * Laporan visitasi untuk evaluation tertentu milik sekolah user.
     * Route: schools.report.by_evaluation
     */
    public function showByEvaluation(Request $request, Evaluation $evaluation)
    {
        $school = $request->user()->school ?? abort(404, 'Sekolah tidak ditemukan.');
        abort_if((int)$evaluation->school_id !== (int)$school->id, 403, 'Tidak berhak mengakses.');
        abort_if($evaluation->status !== 'submitted', 404, 'Laporan belum final.');

        $evaluation->load(['items','pengawas']);

        $aspects = PengawasEvaluationController::ASPECTS;
        $built   = $this->buildReport($evaluation, $aspects);

        return view('pengawas.evaluations.report', array_merge($built, [
            'school'     => $school,
            'evaluation' => $evaluation,
        ]));
    }

    /**
     * === Builder laporan (fallback) ===
     * Jika kamu sudah punya App\Support\EvaluationReport, kamu bisa ganti
     * pemanggilan ke class itu. Fungsi ini setara dan berdiri sendiri.
     *
     * Return: ['detail','overallAvg','overallGrade','overallText','colorMap']
     */
    private function buildReport(Evaluation $evaluation, array $aspects): array
    {
        $point     = ['A'=>4,'B'=>3,'C'=>2,'D'=>1];
        $gradeText = ['A'=>'Sangat Baik','B'=>'Baik','C'=>'Cukup','D'=>'Perlu Perbaikan'];
        $colorMap  = ['A'=>'#10B981','B'=>'#3B82F6','C'=>'#F59E0B','D'=>'#EF4444'];

        $detail  = [];
        $allNums = [];

        foreach ($aspects as $akey => $adef) {
            $rows = [];
            $nums = [];

            $indics = $adef['indicators'] ?? [];
            if (empty($indics)) continue;

            foreach ($indics as $ikey => $label) {
                // Gunakan first() dengan closure (bukan firstWhere(fn))
                $item = $evaluation->items->first(function ($i) use ($akey, $ikey) {
                    return $i->aspect === $akey && $i->indicator === $ikey;
                });

                $score = $item->score ?? null;
                if ($score && isset($point[$score])) $nums[] = $point[$score];

                $rows[] = [
                    'label'    => $label,
                    'score'    => $score,
                    'evidence' => $item->evidence_path ?? null,
                ];
            }

            $avg = count($nums) ? round(array_sum($nums)/count($nums), 2) : null;
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

        $overallAvg   = count($allNums) ? round(array_sum($allNums)/count($allNums), 2) : null;
        $overallGrade = is_null($overallAvg) ? '-' : ($overallAvg >= 3.5 ? 'A' : ($overallAvg >= 2.5 ? 'B' : ($overallAvg >= 1.5 ? 'C' : 'D')));
        $overallText  = $overallGrade === '-' ? '-' : $gradeText[$overallGrade];

        return compact('detail','overallAvg','overallGrade','overallText','colorMap');
    }
}
