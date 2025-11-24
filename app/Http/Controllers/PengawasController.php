<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class PengawasController extends Controller
{
    // List + form buat akun baru
    public function index()
    {
        // hanya tampilkan user dengan role pengawas/sekolah
        $users = User::role(['pengawas'])->latest()->paginate(20);
        return view('admin.pengawas.index', compact('users'));
    }

    // Simpan akun baru
    public function store(Request $request)
    {

    }

    // Hapus akun (opsional)
        public function destroy(User $user)
    {
        if (!$user->hasAnyRole(['admin','sekolah'])) {
            abort(403);
        }
        $user->delete();
        return back()->with('success', 'Akun dihapus.');
    }
}
