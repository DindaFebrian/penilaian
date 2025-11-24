<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Visit;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
        public function index()
    {
        $totalSchools = School::count();
        $totalVisitasiAktif = Visit::whereIn('status', ['scheduled', 'accepted'])->count();
        $totalVisitasiSelesai = Visit::where('status', 'done')->count();

        return view('dashboard', compact(
            'totalSchools',
            'totalVisitasiAktif',
            'totalVisitasiSelesai'
        ));
    }
}
