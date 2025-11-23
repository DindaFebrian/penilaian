
@extends('layouts.app')

@section('content')
<h1 class="text-xl font-semibold mb-4">Daftar Sekolah</h1>

<div class="bg-white shadow rounded overflow-x-auto">
  <table class="min-w-full text-sm">
    <thead class="bg-gray-50">
      <tr>
        <th class="px-4 py-2 text-left">Nama</th>
        <th class="px-4 py-2">NPSN</th>
        <th class="px-4 py-2">Kelengkapan</th>
        <th class="px-4 py-2">Status Review</th>
        <th class="px-4 py-2"></th>
      </tr>
    </thead>
    <tbody>
      @forelse($schools as $s)
      <tr class="border-t">
        <td class="px-4 py-2">{{ $s->nama }}</td>
        <td class="px-4 py-2 text-center">{{ $s->npsn }}</td>
        <td class="px-4 py-2 text-center">
          @php
            $ok = collect([
              $s->complete_profile,
              $s->complete_guru,
              $s->complete_siswa,
              $s->complete_dokumen,
              $s->complete_sarpras
            ])->filter()->count();
          @endphp
          {{ $ok }}/5
        </td>
        <td class="px-4 py-2 text-center">
          <span class="px-2 py-1 rounded text-white
            {{ $s->review_status==='approved' ? 'bg-green-600' : ($s->review_status==='rejected' ? 'bg-red-600' : 'bg-gray-500') }}">
            {{ strtoupper($s->review_status) }}
          </span>
        </td>
        <td class="px-4 py-2 text-right">
          <a class="text-indigo-600 hover:underline" href="{{ route('pengawas.schools.show',$s) }}">Periksa</a>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada data sekolah.</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">
  {{ $schools->links() }}
</div>
@endsection
