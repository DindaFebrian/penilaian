@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-bold">Dashboard</h1>
</div>

<div class="py-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow border border-green-300">
            <h2 class="text-center font-semibold text-gray-700">Data Sekolah</h2>
            <div class="text-5xl text-center font-bold text-green-600 my-3">
                {{ $totalSchools }}
            </div>
            <p class="text-center text-gray-500 text-sm">Total Sekolah Terdaftar</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow border border-green-300">
            <h2 class="text-center font-semibold text-gray-700">Visitasi Sekolah</h2>
            <div class="text-5xl text-center font-bold text-green-600 my-3">
                {{ $totalVisitasiAktif }}
            </div>
            <p class="text-center text-gray-500 text-sm">Jadwal Visitasi Aktif</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow border border-green-300">
            <h2 class="text-center font-semibold text-gray-700">Hasil Visitasi</h2>
            <div class="text-5xl text-center font-bold text-green-600 my-3">
                {{ $totalVisitasiSelesai }}
            </div>
            <p class="text-center text-gray-500 text-sm">Visitasi Selesai</p>
        </div>

    </div>
</div>
@endsection
