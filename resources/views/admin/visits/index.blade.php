@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold mb-4">Kelola Jadwal Visitasi</h1>

@if(session('success'))
  <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
@endif

<div class="bg-white shadow rounded p-4 overflow-x-auto">
  <table class="min-w-full text-sm">
    <thead class="bg-gray-50">
      <tr>
        <th class="px-3 py-2">Tanggal</th>
        <th class="px-3 py-2">Waktu</th>
        <th class="px-3 py-2 text-left">Sekolah</th>
        <th class="px-3 py-2 text-left">Alamat</th>
        <th class="px-3 py-2 text-left">Pengawas</th>
        <th class="px-3 py-2">Status</th>
        <th class="px-3 py-2">Catatan</th>
        <th class="px-3 py-2 text-left">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($visits as $v)
      <tr class="border-t align-top">
        <td class="px-3 py-2 text-center">{{ \Carbon\Carbon::parse($v->visit_date)->format('d M Y') }}</td>
        <td class="px-3 py-2 text-center">{{ \Carbon\Carbon::parse($v->visit_time)->format('H:i') }}</td>
        <td class="px-3 py-2">{{ $v->school->nama }}</td>
        <td class="px-3 py-2">{{ $v->school->alamat }}</td>
        <td class="px-3 py-2">{{ $v->pengawas?->name ?? '-' }}</td>
        <td class="px-3 py-2 text-center">
          <span class="px-2 py-1 rounded text-white {{ $v->status_badge }}">{{ strtoupper($v->status) }}</span>
        </td>
        <td class="px-3 py-2">{{ $v->note  }}</td>
        @if ($v->status_badge == 'requested')
        <td class="px-3 py-2">
          {{-- Form Schedule/Approve --}}
          <form action="{{ route('admin.visits.schedule',$v) }}" method="POST" class="flex flex-wrap gap-2 items-center mb-2">
            @csrf
            {{-- <input type="date" name="visit_date" value="{{ $v->visit_date }}" class="border rounded px-2 py-1">
            <input type="time" name="visit_time" value="{{ \Carbon\Carbon::parse($v->visit_time)->format('H:i') }}" class="border rounded px-2 py-1"> --}}
            <select name="pengawas_id" class="border rounded px-2 py-1" required>
              <option value="">-- Pilih Pengawas --</option>
              @foreach($pengawas as $p)
                <option value="{{ $p->id }}" @selected($v->pengawas_id==$p->id)>{{ $p->name }}</option>
              @endforeach
            </select>
            <button class="px-3 py-1 rounded bg-blue-600 text-white">Setujui & Tugaskan</button>
          </form>

          {{-- Form Reject --}}
          <form action="{{ route('admin.visits.reject',$v) }}" method="POST" onsubmit="return confirm('Tolak pengajuan ini?')">
            @csrf
            <input type="text" name="note" class="border rounded px-2 py-1" placeholder="Alasan tolak" required>
            <button class="px-3 py-1 rounded bg-red-600 text-white">Tolak</button>
          </form>
        </td>

        @endif

      </tr>
      @empty
      <tr><td colspan="7" class="px-3 py-4 text-center text-gray-500">Belum ada pengajuan.</td></tr>
      @endforelse
    </tbody>
  </table>

  <div class="mt-3">{{ $visits->links() }}</div>
</div>
@endsection
