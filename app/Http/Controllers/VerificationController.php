<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;

class VerificationController extends Controller
{

    // app/Http/Controllers/VerificationController.php
    public function approve(School $school)
    {
        $canApprove = $school->review_status === 'pending'
            || ($school->review_status === 'rejected' && $school->updatedAfterReview());

        if (!$canApprove) {
            return back()->with('info', 'Belum ada perubahan setelah penolakan.');
        }

        $school->update([
            'review_status' => 'approved',
            'reviewed_at'   => now(),
            'review_notes'  => null,
        ]);

        return back()->with('success', 'Sekolah disetujui.');
    }

    public function reject(\Illuminate\Http\Request $request, School $school)
    {
        if ($school->review_status === 'approved') {
            return back()->with('info', 'Sekolah sudah disetujui.');
        }

        $request->validate(['alasan' => 'required|string|max:500']);

        $school->update([
            'review_status' => 'rejected',
            'reviewed_at'   => now(),
            'review_notes'  => $request->alasan,
        ]);

        return back()->with('success', 'Sekolah ditolak.');
    }

}
