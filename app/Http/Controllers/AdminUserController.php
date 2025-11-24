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
            'name'     => 'required|string|max:120',
            'username' => 'required|alpha_dash:ascii|min:3|max:30|unique:users,username',
            'email'    => 'nullable|email|max:150|unique:users,email',
            'role'     => 'required|in:pengawas,sekolah',
            'password' => 'nullable|string|min:6|max:100',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'username' => $data['username'],
            'email'    => $data['email'] ?? null,
            'password' => Hash::make($data['password'] ?: Str::random(10)),
        ]);

        $user->assignRole($data['role']);

        return back()->with('success', "Akun {$data['role']} dibuat.");
    }

    // FORM EDIT akun
    public function edit(User $user)
    {
        // hanya boleh edit akun pengawas/sekolah
        if (! $user->hasAnyRole(['pengawas','sekolah'])) {
            abort(403);
        }

        // ambil role aktif sekarang (Spatie)
        $currentRole = $user->getRoleNames()->first();

        return view('admin.users.edit', compact('user', 'currentRole'));
    }

    // UPDATE akun
    public function update(Request $request, User $user)
    {
        if (! $user->hasAnyRole(['pengawas','sekolah'])) {
            abort(403);
        }

        $data = $request->validate([
            'name'     => 'required|string|max:120',
            'username' => 'required|alpha_dash:ascii|min:3|max:30|unique:users,username,' . $user->id,
            'email'    => 'nullable|email|max:150|unique:users,email,' . $user->id,
            'role'     => 'required|in:pengawas,sekolah',
            'password' => 'nullable|string|min:6|max:100',
        ]);

        $user->name     = $data['name'];
        $user->username = $data['username'];
        $user->email    = $data['email'] ?? null;

        // password hanya diubah kalau diisi
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        // update role di Spatie
        $user->syncRoles([$data['role']]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Akun diperbarui.');
    }

    // Hapus akun
    public function destroy(User $user)
    {
        if (! $user->hasAnyRole(['pengawas','sekolah'])) {
            abort(403);
        }
        $user->delete();
        return back()->with('success', 'Akun dihapus.');
    }
}
