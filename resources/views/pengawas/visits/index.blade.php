@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold mb-4">Jadwal Visitasi Saya</h1>

@if(session('success'))
  <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
@endif

{{-- JADWAL TERDEKAT --}}
<section class="bg-white shadow rounded p-4 mb-6">
  <h2 class="font-semibold mb-3">Jadwal Mendatang</h2>
  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-3 py-2">Tanggal</th>
          <th class="px-3 py-2">Waktu</th>
          <th class="px-3 py-2 text-left">Sekolah</th>
          <th class="px-3 py-2 text-left">Alamat</th>
          <th class="px-3 py-2">Diterima</th>
          <th class="px-3 py-2 text-left">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($upcoming as $v)
        <tr class="border-t">
          <td class="px-3 py-2 text-center">{{ \Carbon\Carbon::parse($v->visit_date)->format('d M Y') }}</td>
          <td class="px-3 py-2 text-center">{{ \Carbon\Carbon::parse($v->visit_time)->format('H:i') }}</td>
          <td class="px-3 py-2">{{ $v->school->nama }}</td>
          <td class="px-3 py-2">{{ $v->school->alamat }}</td>
          <td class="px-3 py-2 text-center">{{ $v->accepted_at ? 'Ya' : 'Belum' }}</td>
          <td class="px-3 py-2">
            <div class="flex flex-wrap items-center gap-2">

              {{-- Buka halaman penilaian untuk sekolah terkait --}}
              @if($v->status === 'scheduled')
                <a href="{{ route('pengawas.evaluations.create', $v->school) }}"
                   class="px-3 py-1 rounded bg-indigo-600 text-white hover:bg-indigo-700">
                  Penilaian
                </a>
              @endif

              {{-- Hasil Laporan (preview laporan evaluasi terbaru untuk sekolah) --}}
              <a href="{{ route('pengawas.evaluations.report', $v->school) }}"
                 target="_blank"
                 class="px-3 py-1 rounded bg-indigo-50 text-indigo-700 hover:bg-indigo-100">
                Hasil Laporan
              </a>

              {{-- Terima / Tolak tugas (hanya jika belum respon & masih scheduled) --}}
              @if(!$v->accepted_at && !$v->declined_at && $v->status === 'scheduled')
                <form action="{{ route('pengawas.visits.accept', $v) }}" method="POST" class="inline">
                  @csrf
                  <button class="px-3 py-1 rounded bg-blue-600 text-white">Terima Tugas</button>
                </form>

                <form action="{{ route('pengawas.visits.decline', $v) }}" method="POST" class="inline">
                  @csrf
                  <input type="text" name="decline_reason" class="border rounded px-2 py-1" placeholder="Alasan" required>
                  <button class="px-3 py-1 rounded bg-red-600 text-white">Tolak</button>
                </form>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-3 py-4 text-center text-gray-500">Tidak ada jadwal.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>

{{-- RIWAYAT & PENYELESAIAN --}}
<section class="bg-white shadow rounded p-4">
  <h2 class="font-semibold mb-3">Riwayat & Penyelesaian</h2>
  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-3 py-2">Tanggal</th>
          <th class="px-3 py-2">Waktu</th>
          <th class="px-3 py-2 text-left">Sekolah</th>
          <th class="px-3 py-2">Status</th>
          <th class="px-3 py-2 text-left">Laporan</th>
          <th class="px-3 py-2 text-left">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($history as $v)
        <tr class="border-t align-top">
          <td class="px-3 py-2 text-center">{{ \Carbon\Carbon::parse($v->visit_date)->format('d M Y') }}</td>
          <td class="px-3 py-2 text-center">{{ \Carbon\Carbon::parse($v->visit_time)->format('H:i') }}</td>
          <td class="px-3 py-2">{{ $v->school->nama }}</td>
          <td class="px-3 py-2 text-center">
            <span class="px-2 py-1 rounded text-white {{ $v->status==='done' ? 'bg-green-600' : ($v->status==='rejected' ? 'bg-red-600' : 'bg-gray-500') }}">
              {{ strtoupper($v->status) }}
            </span>
          </td>

          {{-- ⬇️ Kolom LAPORAN — disatukan sesuai snippet --}}
          <td class="px-3 py-2">
            @if($v->report_file)
              <a href="/{{ $v->report_file }}" target="_blank" class="text-indigo-600">Lihat File</a><br>
            @endif

            <a href="{{ route('pengawas.evaluations.report', $v->school) }}"
               target="_blank"
               class="text-indigo-600 hover:underline">
              Hasil Laporan
            </a>

            <div class="text-gray-700">{{ $v->report_summary ?? '-' }}</div>
          </td>

          {{-- Kolom AKSI — form Tandai Selesai (tanpa link Hasil Laporan, karena sudah di kolom Laporan) --}}
          <td class="px-3 py-2">
            @if($v->status!=='done')
              <form action="{{ route('pengawas.visits.complete',$v) }}" method="POST" enctype="multipart/form-data" class="space-y-2">
                @csrf
                <input type="file" name="report_file" class="block">
                <input type="text" name="report_summary" class="border rounded px-2 py-1 w-72" placeholder="Ringkasan hasil visitasi">
                <button class="px-3 py-1 rounded bg-green-600 text-white">Tandai Selesai</button>
              </form>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="px-3 py-4 text-center text-gray-500">Belum ada riwayat.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-3">{{ $history->links() }}</div>
</section>
@endsection
