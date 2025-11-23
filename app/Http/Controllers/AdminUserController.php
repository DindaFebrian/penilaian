<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    // List + form buat akun baru
    public function index()
    {
        // hanya tampilkan user dengan role pengawas/sekolah
        $users = User::role(['pengawas','sekolah'])->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    // Simpan akun baru
    public function store(Request $request)
    {
        $data = $request->validate([
        'name' => 'required|string|max:120',
        'username' => 'required|alpha_dash:ascii|min:3|max:30|unique:users,username',
        'email' => 'nullable|email|max:150|unique:users,email', // boleh nullable jika tak dipakai
        'role' => 'required|in:pengawas,sekolah',
        'password' => 'nullable|string|min:6|max:100',
        ]);

        $user = User::create([
        'name' => $data['name'],
        'username' => $data['username'],
        'email' => $data['email'] ?? null,
        'password' => Hash::make($data['password'] ?: Str::random(10)),
        ]);

        $user->assignRole($data['role']);

        // tampilkan password sekali lewat flash
        return back()->with('success', "Akun {$data['role']} dibuat.");
    }

    // Hapus akun (opsional)
    public function destroy(User $user)
    {
        if (!$user->hasAnyRole(['pengawas','sekolah'])) {
            abort(403);
        }
        $user->delete();
        return back()->with('success', 'Akun dihapus.');
    }
}
