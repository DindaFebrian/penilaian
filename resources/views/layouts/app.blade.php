{{-- <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html> --}}
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ config('app.name','SekolahApp') }}</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
<nav class="bg-white shadow sticky top-0 z-10">
  <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
    {{-- Kiri: brand + menu sesuai role --}}
    <div class="flex items-center gap-6">

      <a href="{{ url('/') }}" class="font-semibold">SekolahApp</a>

      @php
        // helper kelas aktif
        $is = fn($pattern) => request()->routeIs($pattern)
              ? 'text-indigo-600 font-semibold'
              : 'text-gray-600 hover:text-gray-900';
      @endphp

      @auth
        {{-- Semua role bisa melihat Dashboard --}}
        @if (Route::has('dashboard'))
          <a href="{{ route('dashboard') }}" class="{{ $is('dashboard') }}">Dashboard</a>
        @endif

        {{-- ========== MENU ROLE SEKOLAH ========== --}}
        @hasrole('sekolah')
          @if (Route::has('schools.my'))
            <a href="{{ route('schools.my') }}" class="{{ $is('schools.my') }}">
              Panel Sekolah
            </a>
          @endif

          @if (Route::has('schools.visits.index'))
            <a href="{{ route('schools.visits.index') }}" class="{{ $is('schools.visits.*') }}">
              Pengajuan Visitasi
            </a>
          @endif

          @if (Route::has('schools.report.me'))
            <a href="{{ route('schools.report.me') }}" class="{{ $is('schools.report.*') }}">
              Hasil Penilaian
            </a>
          @endif
        @endhasrole
        

        {{-- ========== MENU ROLE PENGAWAS ========== --}}
        @hasrole('pengawas')
          @if (Route::has('pengawas.schools.index'))
            <a href="{{ route('pengawas.schools.index') }}" class="{{ $is('pengawas.schools.*') }}">
              Daftar Sekolah
            </a>
          @endif

          @if (Route::has('pengawas.visits.index'))
            <a href="{{ route('pengawas.visits.index') }}" class="{{ $is('pengawas.visits.*') }}">
              Jadwal Visitasi
            </a>
          @endif
        @endhasrole

        {{-- ========== MENU ROLE ADMIN ========== --}}
        @hasrole('admin')
          @if (Route::has('admin.users.index'))
            <a href="{{ route('admin.users.index') }}" class="{{ $is('admin.users.*') }}">
              Kelola Pengguna
            </a>
          @endif

          @if (Route::has('admin.visits.index'))
            <a href="{{ route('admin.visits.index') }}" class="{{ $is('admin.visits.*') }}">
              Manajemen Visitasi
            </a>
          @endif
        @endhasrole
      @endauth
    </div>

    {{-- Kanan: info user + logout/login --}}
    <div class="text-sm text-gray-600 flex items-center gap-3">
      @auth
        <span>Halo, <span class="font-medium">{{ auth()->user()->name }}</span></span>
        <span class="px-2 py-1 rounded bg-gray-100 border text-xs">
          {{ auth()->user()->getRoleNames()->implode(', ') ?: '-' }}
        </span>
        <form action="{{ route('logout') }}" method="POST" class="inline">
          @csrf
          <button type="submit" class="px-3 py-1 rounded bg-red-600 text-white hover:bg-red-700">
            Logout
          </button>
        </form>
      @else
        @if (Route::has('login'))
          <a href="{{ route('login') }}" class="px-3 py-1 rounded bg-indigo-600 text-white">Login</a>
        @endif
      @endauth
    </div>
  </div>
</nav>



<main class="max-w-7xl mx-auto p-4">
  @if(session('success'))
    <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
  @endif

  @if($errors->any())
    <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
      <ul class="list-disc list-inside">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
      </ul>
    </div>
  @endif

  @yield('content')
</main>
</body>
</html>
