<div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
    <h3 class="text-lg font-semibold mb-4">Daftar Akun Pengawas &amp; Sekolah</h3>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b">
                    <th class="text-left py-2 px-2">Nama</th>
                    <th class="text-left py-2 px-2">Username</th>
                    <th class="text-left py-2 px-2">Role</th>
                    <th class="text-left py-2 px-2">Dibuat</th>
                    {{-- kolom aksi (Edit + Hapus) --}}
                    <th class="text-left py-2 px-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($accounts as $account)
                    <tr class="border-b">
                        <td class="py-2 px-2">{{ $account->name }}</td>
                        <td class="py-2 px-2">{{ $account->username }}</td>
                        <td class="py-2 px-2">{{ $account->role }}</td>
                        <td class="py-2 px-2">
                            {{ $account->created_at?->format('d M Y') }}
                        </td>
                        <td class="py-2 px-2 space-x-3">
                            {{-- tombol Edit --}}
                            <a href="{{ route('admin.accounts.edit', $account) }}"
                               class="text-indigo-600 hover:underline">
                                Edit
                            </a>

                            {{-- tombol Hapus --}}
                            <form action="{{ route('admin.accounts.destroy', $account) }}"
                                  method="POST" class="inline"
                                  onsubmit="return confirm('Yakin ingin menghapus akun ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-gray-500">
                            Belum ada akun.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
