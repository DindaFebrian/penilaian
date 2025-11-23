@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold mb-4">Jadwal Visitasi</h1>

{{-- Tabel status pengajuan --}}
<section class="bg-white shadow rounded p-4 mb-6">
  <h2 class="font-semibold mb-3">{{ $school->nama }}</h2>
  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-3 py-2">Tanggal</th>
          <th class="px-3 py-2">Waktu</th>
          <th class="px-3 py-2 text-left">Pengawas</th>
          <th class="px-3 py-2">Status</th>
          <th class="px-3 py-2 text-left">Catatan</th>
        </tr>
      </thead>
      <tbody>
        @forelse($visits as $v)
        <tr class="border-t">
          <td class="px-3 py-2 text-center">{{ \Carbon\Carbon::parse($v->visit_date)->format('d M Y') }}</td>
          <td class="px-3 py-2 text-center">{{ \Carbon\Carbon::parse($v->visit_time)->format('H:i') }}</td>
          <td class="px-3 py-2">{{ $v->pengawas?->name ?? '-' }}</td>
          <td class="px-3 py-2 text-center">
            <span class="px-2 py-1 rounded text-white {{ $v->status_badge }}">{{ strtoupper($v->status) }}</span>
          </td>
          <td class="px-3 py-2">{{ $v->note ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-3 py-4 text-center text-gray-500">Belum pernah mengajukan.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-3">{{ $visits->links() }}</div>
</section>

{{-- Form pengajuan --}}
<section class="bg-white shadow rounded p-4">
  <h2 class="font-semibold mb-3">Ajukan Jadwal Visitasi</h2>
  <form action="{{ route('schools.visits.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-3">
    @csrf
    <div class="md:col-span-2">
      <label class="text-sm">Tanggal</label>
      <input type="date" name="visit_date" class="w-full mt-1 border rounded px-3 py-2" required>
    </div>
    <div class="md:col-span-1">
      <label class="text-sm">Waktu</label>
      <input type="time" name="visit_time" class="w-full mt-1 border rounded px-3 py-2" required>
    </div>
    <div class="md:col-span-5">
      <label class="text-sm">Catatan (opsional)</label>
      <input type="text" name="note" class="w-full mt-1 border rounded px-3 py-2" placeholder="Mis: mohon jadwal pagi">
    </div>
    <div class="md:col-span-5">
      <button class="px-4 py-2 bg-indigo-600 text-white rounded">Ajukan</button>
    </div>
  </form>
</section>
@endsection
