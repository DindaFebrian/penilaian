@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold mb-4">Kelola Akun (Admin)</h1>

<div class="grid md:grid-cols-3 gap-6">
  {{-- Form Buat Akun --}}
  <section class="bg-white shadow rounded p-4">
    <h2 class="font-semibold mb-3">Buat Akun Baru</h2>
    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-3">
      @csrf
      <div>
        <label class="text-sm">Nama</label>
        <input type="text" name="name" value="{{ old('name') }}" class="w-full mt-1 border rounded px-3 py-2" required>
      </div>
      <div>
        <label class="text-sm">Username</label>
        <input type="username" name="username" value="{{ old('username') }}" class="w-full mt-1 border rounded px-3 py-2" required>
      </div>
      <div>
        <label class="text-sm">Role</label>
        <select name="role" class="w-full mt-1 border rounded px-3 py-2" required>
          <option value="">-- Pilih Role --</option>
          <option value="pengawas" @selected(old('role')==='pengawas')>Pengawas</option>
          <option value="sekolah" @selected(old('role')==='sekolah')>Sekolah</option>
        </select>
      </div>
      <div>
        <label class="text-sm">Password (opsional)</label>
        <input type="text" name="password" class="w-full mt-1 border rounded px-3 py-2" placeholder="Kosongkan untuk generate otomatis">
      </div>
      <button class="px-4 py-2 bg-indigo-600 text-white rounded">Simpan</button>
      <p class="text-xs text-gray-500 mt-2">Jika password dikosongkan, sistem akan membuat password acak dan ditampilkan sekali di notifikasi.</p>
    </form>
  </section>

  {{-- Tabel Akun --}}
  <section class="bg-white shadow rounded p-4 md:col-span-2">
    <h2 class="font-semibold mb-3">Daftar Akun Pengawas & Sekolah</h2>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
  <thead class="bg-gray-50">
    <tr>
      <th class="px-3 py-2 text-left">Nama</th>
      <th class="px-3 py-2">Username</th>
      <th class="px-3 py-2">Role</th>
      <th class="px-3 py-2">Dibuat</th>
      <th class="px-3 py-2"></th>
    </tr>
  </thead>
  <tbody>
    @forelse($users as $u)
    <tr class="border-t">
      <td class="px-3 py-2">{{ $u->name }}</td>
      <td class="px-3 py-2 text-center">{{ $u->username ?? '-' }}</td>
      <td class="px-3 py-2 text-center">{{ $u->getRoleNames()->implode(', ') }}</td>
      <td class="px-3 py-2 text-center">{{ $u->created_at?->format('d M Y') }}</td>
      <td class="px-3 py-2 text-right">
        <form onsubmit="return confirm('Hapus akun ini?')" action="{{ route('admin.users.destroy', $u) }}" method="POST">
          @csrf @method('DELETE')
          <button class="text-red-600">Hapus</button>
        </form>
      </td>
    </tr>
    @empty
    <tr><td colspan="5" class="px-3 py-4 text-center text-gray-500">Belum ada akun.</td></tr>
    @endforelse
  </tbody>
</table>
    </div>
    <div class="mt-3">{{ $users->links() }}</div>
  </section>
</div>
@endsection
