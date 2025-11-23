<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
        public function index()
    {
        // tidak perlu kirim data apa pun karena view hanya menampilkan teks
        return view('dashboard');
    }
}
