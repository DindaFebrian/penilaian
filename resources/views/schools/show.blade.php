@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">

  <!-- PROFIL SEKOLAH -->
  <section class="bg-white shadow rounded p-4">
    <div class="flex items-center justify-between mb-3">
      <h2 class="font-semibold">Profil Sekolah</h2>
      <div class="flex items-center gap-2">
        <span class="text-sm">Status Review:</span>
        <span class="px-2 py-1 rounded text-white {{ $school->review_status==='approved'?'bg-green-600':($school->review_status==='rejected'?'bg-red-600':'bg-gray-500') }}">
          {{ strtoupper($school->review_status) }}
        </span>
      </div>
    </div>

    @if($canEdit)
    <form action="{{ url('/sekolah/profile') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-3">
      @csrf @method('PUT')
      <div>
        <label class="text-sm">Nama Sekolah</label>
        <input type="text" name="nama" value="{{ old('nama',$school->nama) }}" class="w-full mt-1 border rounded px-3 py-2"/>
      </div>
      <div>
        <label class="text-sm">NPSN</label>
        <input type="text" name="npsn" value="{{ old('npsn',$school->npsn) }}" class="w-full mt-1 border rounded px-3 py-2"/>
      </div>
      <div>
        <label class="text-sm">Alamat Lengkap</label>
        <input type="text" name="alamat" value="{{ old('alamat',$school->alamat) }}" class="w-full mt-1 border rounded px-3 py-2"/>
      </div>
      <div>
        <label class="text-sm">Jenjang</label>
        <input type="text" name="jenjang" value="{{ old('jenjang',$school->jenjang) }}" class="w-full mt-1 border rounded px-3 py-2"/>
      </div>
      <div>
        <label class="text-sm">Tanggal SK Sekolah</label>
        <input type="date" name="tanggal_sk_sekolah" value="{{ old('tanggal_sk_sekolah', optional($school->tanggal_sk_sekolah)->format('Y-m-d')) }}" class="w-full mt-1 border rounded px-3 py-2"/>
      </div>
      <div>
        <label class="text-sm">Kepala Sekolah</label>
        <input type="text" name="kepala_sekolah" value="{{ old('kepala_sekolah',$school->kepala_sekolah) }}" class="w-full mt-1 border rounded px-3 py-2"/>
      </div>
      <div>
        <label class="text-sm">Email</label>
        <input type="email" name="email" value="{{ old('email',$school->email) }}" class="w-full mt-1 border rounded px-3 py-2"/>
      </div>
      <div class="md:col-span-2 mt-2">
        <button class="px-4 py-2 bg-indigo-600 text-white rounded">Simpan Profil</button>
      </div>
    </form>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
      <div><div class="text-gray-500">Nama Sekolah</div><div class="font-medium">{{ $school->nama }}</div></div>
      <div><div class="text-gray-500">NPSN</div><div class="font-medium">{{ $school->npsn }}</div></div>
      <div><div class="text-gray-500">Alamat</div><div class="font-medium">{{ $school->alamat }}</div></div>
      <div><div class="text-gray-500">Jenjang</div><div class="font-medium">{{ $school->jenjang }}</div></div>
      <div><div class="text-gray-500">Tanggal SK Sekolah</div><div class="font-medium">{{ optional($school->tanggal_sk_sekolah)->format('d M Y') }}</div></div>
      <div><div class="text-gray-500">Kepala Sekolah</div><div class="font-medium">{{ $school->kepala_sekolah }}</div></div>
      <div><div class="text-gray-500">Email</div><div class="font-medium">{{ $school->email }}</div></div>
    </div>
    @endif
  </section>

  <!-- DATA GURU -->
  <section class="bg-white shadow rounded p-4">
    <h2 class="font-semibold mb-3">Data Guru</h2>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-3 py-2 text-left">Nama</th>
            <th class="px-3 py-2">NIP/NIK</th>
            <th class="px-3 py-2">Pangkat/Gol</th>
            <th class="px-3 py-2">Jabatan</th>
            <th class="px-3 py-2">Sertifikasi</th>
            @if($canEdit)<th class="px-3 py-2"></th>@endif
          </tr>
        </thead>
        <tbody>
          @forelse($school->teachers as $t)
          <tr class="border-t">
            <td class="px-3 py-2">{{ $t->nama }}</td>
            <td class="px-3 py-2 text-center">{{ $t->nip_nik }}</td>
            <td class="px-3 py-2 text-center">{{ $t->pangkat_golongan }}</td>
            <td class="px-3 py-2 text-center">{{ $t->jabatan }}</td>
            <td class="px-3 py-2 text-center">{{ $t->sertifikasi ? 'Ya' : 'Tidak' }}</td>
            @if($canEdit)
            <td class="px-3 py-2 text-right">
              <form onsubmit="return confirm('Hapus guru ini?')" action="{{ route('teachers.destroy',$t) }}" method="POST">
                @csrf @method('DELETE')
                <button class="text-red-600">Hapus</button>
              </form>
            </td>
            @endif
          </tr>
          @empty
          <tr><td colspan="6" class="px-3 py-4 text-center text-gray-500">Belum ada data guru.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($canEdit)
    <details class="mt-3">
      <summary class="cursor-pointer text-indigo-600">+ Tambah Guru</summary>
      <form action="{{ route('teachers.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-3 mt-3">
        @csrf
        <input class="border rounded px-3 py-2" name="nama" placeholder="Nama" required>
        <input class="border rounded px-3 py-2" name="nip_nik" placeholder="NIP/NIK">
        <input class="border rounded px-3 py-2" name="pangkat_golongan" placeholder="Pangkat/Golongan">
        <input class="border rounded px-3 py-2" name="jabatan" placeholder="Jabatan" value="Guru TK">
        <label class="inline-flex items-center gap-2"><input type="checkbox" name="sertifikasi" value="1"> Sertifikasi</label>
        <div class="md:col-span-5"><button class="px-4 py-2 bg-indigo-600 text-white rounded">Simpan Guru</button></div>
      </form>
    </details>
    @endif
  </section>

 <!-- DATA SISWA -->
<section class="bg-white shadow-sm rounded-lg p-4 sm:p-6 mb-6">
  <div class="flex items-center justify-between mb-4">
    <div>
      <h2 class="text-lg font-semibold text-gray-800">Data Siswa</h2>
      <p class="text-xs text-gray-500">Rekap jumlah kelas dan siswa berdasarkan jenis kelamin.</p>
    </div>
  </div>

  <div class="space-y-6">
    {{-- FORM INPUT --}}
    @if ($canEdit)
      <form
        action="{{ route('student-stats.store') }}"
        method="POST"
        enctype="multipart/form-data"
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4"
      >
        @csrf

        <div>
          <label class="text-sm font-medium text-gray-700">Jumlah Kelas</label>
          <input
            type="number"
            min="0"
            name="jumlah_kelas"
            value="{{ old('jumlah_kelas', optional($school->studentStats->first())->jumlah_kelas) }}"
            class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
          >
        </div>

        <div>
          <label class="text-sm font-medium text-gray-700">Laki-laki</label>
          <input
            id="input_laki_laki"
            type="number"
            min="0"
            name="laki_laki"
            value="{{ old('laki_laki', optional($school->studentStats->first())->laki_laki) }}"
            class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
          >
        </div>

        <div>
          <label class="text-sm font-medium text-gray-700">Perempuan</label>
          <input
            id="input_perempuan"
            type="number"
            min="0"
            name="perempuan"
            value="{{ old('perempuan', optional($school->studentStats->first())->perempuan) }}"
            class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
          >
        </div>

        <div>
          <label class="text-sm font-medium text-gray-700">Jumlah Siswa</label>
          <input
            id="input_jumlah_siswa"
            type="number"
            min="0"
            name="jumlah_siswa"
            value="{{ old('jumlah_siswa', optional($school->studentStats->first())->jumlah_siswa) }}"
            class="mt-1 w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-gray-100 focus:outline-none"
            readonly
          >
          <p class="mt-1 text-xs text-gray-400">Otomatis terisi dari Laki-laki + Perempuan.</p>
        </div>

        <div class="md:col-span-2 lg:col-span-1">
          <label class="text-sm font-medium text-gray-700">File Data Siswa</label>
          <input
            type="file"
            name="file"
            class="mt-1 w-full text-sm"
          >
          <p class="mt-1 text-xs text-gray-400">Opsional, unggah rekap detail (Excel/PDF).</p>
        </div>

        <div class="md:col-span-2 lg:col-span-5">
          <button
            type="submit"
            class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1"
          >
            Simpan Rekap
          </button>
        </div>
      </form>
    @endif

    {{-- RINGKASAN REKAP --}}
    @if (! $school->studentStats->isEmpty())
      @php $ss = $school->studentStats->first(); @endphp

      <div class="border-t border-gray-200 pt-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Rekap Terakhir</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 text-sm">
          <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
            <p class="text-xs text-gray-500">Jumlah Kelas</p>
            <p class="mt-1 text-base font-semibold text-gray-800">{{ $ss->jumlah_kelas }}</p>
          </div>
          <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
            <p class="text-xs text-gray-500">Laki-laki</p>
            <p class="mt-1 text-base font-semibold text-gray-800">{{ $ss->laki_laki }}</p>
          </div>
          <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
            <p class="text-xs text-gray-500">Perempuan</p>
            <p class="mt-1 text-base font-semibold text-gray-800">{{ $ss->perempuan }}</p>
          </div>
          <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
            <p class="text-xs text-gray-500">Total Siswa</p>
            <p class="mt-1 text-base font-semibold text-gray-800">{{ $ss->jumlah_siswa }}</p>
          </div>
          <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
            <p class="text-xs text-gray-500">File Rekap</p>
            <p class="mt-1">
              @if ($ss->file_path)
                <a
                  href="/{{ $ss->file_path }}"
                  target="_blank"
                  class="inline-flex items-center text-xs font-medium text-indigo-600 hover:underline"
                >
                  Lihat file
                </a>
              @else
                <span class="text-xs text-gray-400">Belum ada file</span>
              @endif
            </p>
          </div>
        </div>
      </div>
    @endif
  </div>
</section>

{{-- SCRIPT UNTUK HITUNG OTOMATIS JUMLAH SISWA --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  const lakiInput = document.getElementById('input_laki_laki');
  const perempuanInput = document.getElementById('input_perempuan');
  const totalInput = document.getElementById('input_jumlah_siswa');

  function hitungTotal() {
    const l = parseInt(lakiInput.value) || 0;
    const p = parseInt(perempuanInput.value) || 0;
    totalInput.value = l + p;
  }

  if (lakiInput && perempuanInput && totalInput) {
    lakiInput.addEventListener('input', hitungTotal);
    perempuanInput.addEventListener('input', hitungTotal);

    // hitung saat pertama kali halaman load (misal ada nilai dari database)
    hitungTotal();
  }
});
</script>

  <!-- DOKUMEN ADMINISTRASI -->
  <section class="bg-white shadow rounded p-4">
    <h2 class="font-semibold mb-3">Dokumen Administrasi</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      @foreach($docTypes as $type)
        @php $doc = $school->documents->firstWhere('jenis',$type); @endphp
        <div class="border rounded p-3">
          <div class="font-medium mb-2">{{ $type }}</div>
          @if($doc)
            <div class="text-sm mb-2">Terunggah: {{ $doc->tanggal_upload }}</div>
            <div class="flex items-center justify-between">
              <a href="/{{ $doc->file_path }}" target="_blank" class="text-indigo-600">Lihat File</a>
              @if($canEdit)
              <form onsubmit="return confirm('Hapus dokumen ini?')" action="{{ route('documents.destroy',$doc) }}" method="POST">
                @csrf @method('DELETE')
                <button class="text-red-600">Hapus</button>
              </form>
              @endif
            </div>
          @else
            @if($canEdit)
            <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="text-sm">
              @csrf
              <input type="hidden" name="jenis" value="{{ $type }}">
              <input type="file" name="file" class="mb-2" required>
              <button class="px-3 py-1 rounded bg-indigo-600 text-white">Unggah {{ $type }}</button>
            </form>
            @else
              <div class="text-gray-500 text-sm">Belum diunggah.</div>
            @endif
          @endif
        </div>
      @endforeach
    </div>
  </section>

  <!-- SARANA & PRASARANA -->
  <section class="bg-white shadow rounded p-4">
    <h2 class="font-semibold mb-3">Kondisi Sarana & Prasarana</h2>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-3 py-2 text-left">Item</th>
            <th class="px-3 py-2">Jumlah</th>
            <th class="px-3 py-2">Kondisi</th>
            <th class="px-3 py-2">Keterangan</th>
            <th class="px-3 py-2">File</th>
            @if($canEdit)<th class="px-3 py-2"></th>@endif
          </tr>
        </thead>
        <tbody>
          @forelse($school->facilities as $f)
          <tr class="border-t">
            <td class="px-3 py-2">{{ $f->item }}</td>
            <td class="px-3 py-2 text-center">{{ $f->jumlah ?? '-' }}</td>
            <td class="px-3 py-2 text-center">{{ $f->kondisi ?? '-' }}</td>
            <td class="px-3 py-2">{{ $f->keterangan ?? '-' }}</td>
            <td class="px-3 py-2 text-center">@if($f->file_path)<a class="text-indigo-600" target="_blank" href="/{{ $f->file_path }}">Lihat</a>@else - @endif</td>
            @if($canEdit)
            <td class="px-3 py-2 text-right">
              <form onsubmit="return confirm('Hapus baris ini?')" action="{{ route('facilities.destroy',$f) }}" method="POST">
                @csrf @method('DELETE')
                <button class="text-red-600">Hapus</button>
              </form>
            </td>
            @endif
          </tr>
          @empty
          <tr><td colspan="6" class="px-3 py-4 text-center text-gray-500">Belum ada data.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($canEdit)
    <details class="mt-3">
      <summary class="cursor-pointer text-indigo-600">+ Tambah Sarpras</summary>
      <form action="{{ route('facilities.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-5 gap-3 mt-3">
        @csrf
        <input class="border rounded px-3 py-2" name="item" placeholder="Item (mis: Ruang Kelas)" required>
        <input class="border rounded px-3 py-2" name="jumlah" type="number" min="0" placeholder="Jumlah">
        <select class="border rounded px-3 py-2" name="kondisi">
          <option value="">- Kondisi -</option>
          <option>Baik</option><option>Cukup</option><option>Rusak Ringan</option><option>Rusak Berat</option>
        </select>
        <input class="border rounded px-3 py-2" name="keterangan" placeholder="Keterangan">
        <input type="file" name="file">
        <div class="md:col-span-5"><button class="px-4 py-2 bg-indigo-600 text-white rounded">Simpan Sarpras</button></div>
      </form>
    </details>
    @endif
  </section>

  <!-- STATUS + VERIFIKASI -->
  <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-white shadow rounded p-4 md:col-span-2">
      <h2 class="font-semibold mb-2">Status Kelengkapan Data</h2>
      @php
        $items = [
          'Profil Sekolah'=>$school->complete_profile,
          'Data Guru TK'=>$school->complete_guru,
          'Siswa'=>$school->complete_siswa,
          'Dokumen'=>$school->complete_dokumen,
          'Sarana & Prasarana'=>$school->complete_sarpras,
        ];
      @endphp
      <ul class="space-y-2">
        @foreach($items as $label=>$ok)
        <li class="flex items-center gap-2">
          <span class="w-3 h-3 rounded-full {{ $ok?'bg-green-500':'bg-gray-300' }}"></span>
          <span>{{ $label }}</span>
        </li>
        @endforeach
      </ul>
    </div>

    <div class="bg-white shadow rounded p-4">
      <h2 class="font-semibold mb-2">Verifikasi</h2>
      @if($canVerify)
    @if($school->review_status === 'rejected' && $updatedAfterReview)
      <p class="text-xs text-amber-700 mb-2">
        Perubahan terdeteksi setelah penolakan. Anda bisa menyetujui sekarang.
      </p>
    @endif

    <form action="{{ route('pengawas.schools.approve',$school) }}" method="POST" class="mb-2">@csrf
      <button class="w-full px-4 py-2 bg-green-600 text-white rounded">Terima</button>
    </form>
    <form action="{{ route('pengawas.schools.reject',$school) }}" method="POST">@csrf
      <textarea name="alasan" class="w-full border rounded px-3 py-2 mb-2" placeholder="Alasan penolakan (wajib bila Ditolak)"></textarea>
      <button class="w-full px-4 py-2 bg-red-600 text-white rounded">Tolak</button>
    </form>
  @else
    @if($school->review_status === 'approved')
      <p class="text-sm text-green-700">Sudah disetujui.</p>
    @elseif($school->review_status === 'rejected')
      <p class="text-sm text-red-700">
        Ditolak @if($school->review_notes): {{ $school->review_notes }} @endif
      </p>
      <p class="text-xs text-gray-600">Tombol verifikasi akan muncul kembali setelah data diperbarui.</p>
    @else
      <p class="text-sm text-gray-600">Menunggu verifikasi pengawas.</p>
    @endif
  @endif
    </div>
  </section>

</div>
@endsection
