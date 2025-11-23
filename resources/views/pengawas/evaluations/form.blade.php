@extends('layouts.app')

@section('content')
@php
  $locked = ($evaluation->status === 'submitted');
  $aspectsWithItems = collect($aspects)->filter(fn($a) => !empty($a['indicators'] ?? []));
@endphp

{{-- Header + status --}}
<div class="flex items-start justify-between gap-3 mb-4">
  <h1 class="text-xl font-semibold">Penilaian Sekolah – {{ $school->nama }}</h1>
  <div class="flex items-center gap-2">
    <span class="px-2 py-1 rounded text-white text-xs {{ $evaluation->status==='submitted' ? 'bg-green-600' : 'bg-gray-500' }}">
      {{ strtoupper($evaluation->status) }}
    </span>
    <span class="text-xs text-gray-500">
      Tanggal: {{ \Carbon\Carbon::parse($evaluation->tanggal)->format('d M Y') }}
    </span>
  </div>
</div>

{{-- Flash & errors --}}
@if(session('success'))
  <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
@endif
@if($errors->any())
  <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
    <ul class="list-disc list-inside">
      @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
    </ul>
  </div>
@endif

{{-- Profil ringkas --}}
<div class="bg-white shadow rounded p-4 mb-4 grid md:grid-cols-2 gap-3 text-sm">
  <div><div class="text-gray-500">NPSN</div><div class="font-medium">{{ $school->npsn ?? '-' }}</div></div>
  <div><div class="text-gray-500">Jenjang</div><div class="font-medium">{{ $school->jenjang ?? '-' }}</div></div>
  <div><div class="text-gray-500">Alamat</div><div class="font-medium">{{ $school->alamat ?? '-' }}</div></div>
  <div><div class="text-gray-500">Kepala Sekolah</div><div class="font-medium">{{ $school->kepala_sekolah ?? '-' }}</div></div>
</div>

{{-- Keterangan Nilai --}}
<div class="bg-white shadow rounded p-4 mb-4">
  <h2 class="font-semibold mb-2">Keterangan Nilai</h2>
  <div class="flex flex-wrap gap-3 text-sm">
    <span class="px-3 py-1 rounded bg-green-600 text-white">A = Sangat Baik</span>
    <span class="px-3 py-1 rounded bg-blue-600 text-white">B = Baik</span>
    <span class="px-3 py-1 rounded bg-yellow-500 text-white">C = Cukup</span>
    <span class="px-3 py-1 rounded bg-red-600 text-white">D = Perlu Perbaikan</span>
  </div>
</div>

{{-- Nav cepat aspek --}}
@if($aspectsWithItems->count() > 1)
  <div class="bg-white shadow rounded p-3 mb-4 text-sm flex flex-wrap gap-2">
    @foreach($aspectsWithItems as $key => $a)
      <a href="#aspect-{{ $key }}" class="px-2 py-1 rounded bg-gray-100 hover:bg-gray-200">{{ $a['label'] }}</a>
    @endforeach
  </div>
@endif

<form action="{{ route('pengawas.evaluations.store',$school) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
@csrf

{{-- === Render dinamis seluruh aspek & indikator === --}}
@foreach($aspects as $aspectKey => $def)
  @php $indicators = $def['indicators'] ?? []; @endphp
  @continue(empty($indicators))

  <section id="aspect-{{ $aspectKey }}" class="bg-white shadow rounded p-4">
    <h2 class="font-semibold mb-3">{{ $def['label'] }}</h2>

    @foreach($indicators as $indKey => $label)
      @php
        $current = $evaluation->getScore($aspectKey, $indKey);
        $item = $evaluation->items->firstWhere(['aspect'=>$aspectKey,'indicator'=>$indKey]);
      @endphp

      <div class="grid md:grid-cols-12 items-start gap-3 border-t py-3 first:border-0">
        {{-- Label indikator --}}
        <div class="md:col-span-4">
          <div class="font-medium">{{ $label }}</div>
          <div class="text-xs text-gray-500">Indikator: {{ $aspectKey }}.{{ $indKey }}</div>
        </div>

        {{-- Nilai A/B/C/D --}}
        <div class="md:col-span-3 flex items-center gap-4">
          @foreach(['A','B','C','D'] as $opt)
            <label class="inline-flex items-center gap-2">
              <input type="radio"
                     class="score-radio" {{-- penting untuk ringkasan otomatis --}}
                     name="items[{{ $aspectKey }}][{{ $indKey }}]"
                     value="{{ $opt }}"
                     {{ $current===$opt ? 'checked' : '' }}
                     {{ $locked ? 'disabled' : '' }}>
              <span>{{ $opt }}</span>
            </label>
          @endforeach
        </div>

        {{-- Catatan indikator --}}
        <div class="md:col-span-3">
          <input type="text"
                 name="notes[{{ $aspectKey }}][{{ $indKey }}]"
                 value="{{ old("notes.$aspectKey.$indKey", $item->notes ?? '') }}"
                 placeholder="Catatan indikator (opsional)"
                 class="w-full border rounded px-3 py-2 text-sm"
                 {{ $locked ? 'disabled' : '' }}>
        </div>

        {{-- Bukti --}}
        <div class="md:col-span-2">
          <input type="file"
                 name="evidence[{{ $aspectKey }}][{{ $indKey }}]"
                 {{ $locked ? 'disabled' : '' }}>
          @if(!empty($item?->evidence_path))
            <a href="/{{ $item->evidence_path }}" target="_blank" class="block text-indigo-600 text-sm mt-1">Lihat bukti</a>
          @endif
        </div>
      </div>
    @endforeach
  </section>
@endforeach

{{-- Ringkasan nilai singkat (LIVE + rata-rata) --}}
<section class="bg-white shadow rounded p-4">
  <h2 class="font-semibold mb-3">Ringkasan Penilaian</h2>

  <div class="grid md:grid-cols-7 gap-3">
    <div class="border rounded p-3 text-center">
      <div class="text-sm text-gray-500">Nilai A</div>
      <div id="count-A" class="text-2xl font-semibold mt-1">0</div>
    </div>
    <div class="border rounded p-3 text-center">
      <div class="text-sm text-gray-500">Nilai B</div>
      <div id="count-B" class="text-2xl font-semibold mt-1">0</div>
    </div>
    <div class="border rounded p-3 text-center">
      <div class="text-sm text-gray-500">Nilai C</div>
      <div id="count-C" class="text-2xl font-semibold mt-1">0</div>
    </div>
    <div class="border rounded p-3 text-center">
      <div class="text-sm text-gray-500">Nilai D</div>
      <div id="count-D" class="text-2xl font-semibold mt-1">0</div>
    </div>

    <div class="border rounded p-3">
      <div class="text-sm text-gray-500">Terisi / Total</div>
      <div class="mt-1">
        <span id="filled" class="text-2xl font-semibold">0</span>
        <span class="text-gray-500">/</span>
        <span id="total" class="text-2xl font-semibold">0</span>
      </div>
      <div class="mt-2 h-2 bg-gray-200 rounded">
        <div id="progress" class="h-2 bg-indigo-600 rounded" style="width:0%"></div>
      </div>
      <div id="percent" class="text-xs text-gray-500 mt-1">0%</div>
    </div>

    <div class="border rounded p-3 text-center">
      <div class="text-sm text-gray-500">Rata-rata</div>
      <div id="avg-score" class="text-2xl font-semibold mt-1">0.00</div>
    </div>
    <div class="border rounded p-3 text-center">
      <div class="text-sm text-gray-500">Predikat</div>
      <div id="avg-grade" class="inline-block mt-1 px-2 py-1 rounded text-white text-lg bg-gray-500">-</div>
    </div>
  </div>

  {{-- (opsional) dikirim ke server saat submit --}}
  <input type="hidden" name="avg_score" id="avg_score_input">
  <input type="hidden" name="avg_grade" id="avg_grade_input">
</section>

{{-- Catatan umum --}}
<section class="bg-white shadow rounded p-4">
  <h2 class="font-semibold mb-2">Catatan & Rekomendasi Umum</h2>
  <textarea name="overall_notes" rows="4" class="w-full mt-1 border rounded px-3 py-2"
            placeholder="Catatan lintas aspek (opsional)" {{ $locked ? 'disabled' : '' }}>{{ old('overall_notes', $evaluation->overall_notes) }}</textarea>
</section>

{{-- Aksi --}}
<div class="flex flex-wrap gap-3">
  <a href="{{ url()->previous() }}" class="px-4 py-2 rounded bg-gray-200 text-gray-800 hover:bg-gray-300">Kembali</a>

  @if(!$locked)
    <button type="submit" name="submit" value="0" class="px-4 py-2 rounded bg-gray-600 text-white">
      Simpan Draft
    </button>
    <button type="submit" name="submit" value="1"
            onclick="return confirm('Kirim penilaian? Setelah dikirim akan dikunci.');"
            class="px-4 py-2 rounded bg-indigo-700 text-white">
      Kirim Penilaian
    </button>
  @else
    <span class="px-4 py-2 rounded bg-green-100 text-green-800">Penilaian sudah dikirim (terkunci).</span>
  @endif
</div>

</form>

{{-- ===== Script auto-recap + rata-rata ===== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  const radios = Array.from(document.querySelectorAll('input.score-radio'));
  const elCount = {
    A: document.getElementById('count-A'),
    B: document.getElementById('count-B'),
    C: document.getElementById('count-C'),
    D: document.getElementById('count-D'),
  };
  const elFilled  = document.getElementById('filled');
  const elTotal   = document.getElementById('total');
  const elProg    = document.getElementById('progress');
  const elPercent = document.getElementById('percent');
  const elAvg     = document.getElementById('avg-score');
  const elGrade   = document.getElementById('avg-grade');
  const hiddenAvg = document.getElementById('avg_score_input');
  const hiddenGra = document.getElementById('avg_grade_input');

  const point = {A:4, B:3, C:2, D:1};

  function gradeFrom(avg) {
    if (avg >= 3.5) return 'A';
    if (avg >= 2.5) return 'B';
    if (avg >= 1.5) return 'C';
    return 'D';
  }
  function setGradeBadge(g) {
    elGrade.textContent = g || '-';
    elGrade.classList.remove('bg-green-600','bg-blue-600','bg-yellow-500','bg-red-600','bg-gray-500');
    elGrade.classList.add(
      g==='A' ? 'bg-green-600' :
      g==='B' ? 'bg-blue-600'  :
      g==='C' ? 'bg-yellow-500':
                'bg-red-600'
    );
    if (!g || g==='-') elGrade.classList.add('bg-gray-500');
  }

  function recalc() {
    const groups = new Map(); // name -> checked value/null
    radios.forEach(r => {
      if (!groups.has(r.name)) groups.set(r.name, null);
      if (r.checked) groups.set(r.name, r.value);
    });

    const counts = {A:0,B:0,C:0,D:0};
    let filled = 0, sum = 0;
    groups.forEach(val => {
      if (val) {
        filled++;
        counts[val] = (counts[val]||0)+1;
        sum += point[val] || 0;
      }
    });

    const total = groups.size;
    const pct = total ? Math.round(filled / total * 100) : 0;
    const avg = filled ? (sum / filled) : 0;
    const gra = filled ? gradeFrom(avg) : '-';

    elCount.A.textContent = counts.A;
    elCount.B.textContent = counts.B;
    elCount.C.textContent = counts.C;
    elCount.D.textContent = counts.D;
    elFilled.textContent  = filled;
    elTotal.textContent   = total;
    elProg.style.width    = pct + '%';
    elPercent.textContent = pct + '%';
    elAvg.textContent     = avg.toFixed(2);
    setGradeBadge(gra);

    if (hiddenAvg) hiddenAvg.value = avg.toFixed(2);
    if (hiddenGra) hiddenGra.value = gra;
  }

  radios.forEach(r => r.addEventListener('change', recalc));
  recalc(); // inisialisasi dari nilai yang sudah ada
});
</script>
@endsection
