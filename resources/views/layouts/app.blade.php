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
    <title>{{ config('app.name','Pantau Ceria') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

<div class="flex">

    <aside class="w-64 bg-[#A4D6C4] min-h-screen p-6">

        <h1 class="text-white font-semibold text-xl mb-10">
            Pantau Ceria
        </h1>

        @php
        $active = fn($r) => request()->routeIs($r)
            ? 'bg-white text-black font-semibold'
            : 'text-white hover:bg-white/20';
        @endphp

        <nav class="space-y-4">

            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 p-3 rounded-lg {{ $active('dashboard') }}">
                <span class="text-lg">📊</span> Dashboard
            </a>

            @hasrole('sekolah')

                @if (Route::has('schools.my'))
                <a href="{{ route('schools.my') }}"
                   class="flex items-center gap-3 p-3 rounded-lg {{ $active('schools.my') }}">
                    <span class="text-lg">🏫</span> Panel Sekolah
                </a>
                @endif

                @if (Route::has('schools.visits.index'))
                <a href="{{ route('schools.visits.index') }}"
                   class="flex items-center gap-3 p-3 rounded-lg {{ $active('schools.visits.*') }}">
                    <span class="text-lg">📋</span> Pengajuan Visitasi
                </a>
                @endif

                @if (Route::has('schools.report.me'))
                <a href="{{ route('schools.report.me') }}"
                   class="flex items-center gap-3 p-3 rounded-lg {{ $active('schools.report.*') }}">
                    <span class="text-lg">📄</span> Hasil Penilaian
                </a>
                @endif

            @endhasrole

            @hasrole('pengawas')

                @if (Route::has('pengawas.schools.index'))
                <a href="{{ route('pengawas.schools.index') }}"
                   class="flex items-center gap-3 p-3 rounded-lg {{ $active('pengawas.schools.*') }}">
                    <span class="text-lg">🏫</span> Data Sekolah
                </a>
                @endif

                @if (Route::has('pengawas.visits.index'))
                <a href="{{ route('pengawas.visits.index') }}"
                   class="flex items-center gap-3 p-3 rounded-lg {{ $active('pengawas.visits.*') }}">
                    <span class="text-lg">📅</span> Jadwal Visitasi
                </a>
                @endif

                @if (Route::has('pengawas.visits.completed'))
                <a href="{{ route('pengawas.visits.completed') }}"
                   class="flex items-center gap-3 p-3 rounded-lg {{ $active('pengawas.visits.completed') }}">
                    <span class="text-lg">📄</span> Hasil Visitasi
                </a>
                @endif

            @endhasrole

            @hasrole('admin')

                @if (Route::has('admin.users.index'))
                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center gap-3 p-3 rounded-lg {{ $active('admin.users.*') }}">
                    <span class="text-lg">👤</span> Kelola Pengguna
                </a>
                @endif

                @if (Route::has('admin.visits.index'))
                <a href="{{ route('admin.visits.index') }}"
                   class="flex items-center gap-3 p-3 rounded-lg {{ $active('admin.visits.*') }}">
                    <span class="text-lg">📋</span> Manajemen Visitasi
                </a>
                @endif

                @if (Route::has('pengawas.schools.index'))
                <a href="{{ route('pengawas.schools.index') }}"
                   class="flex items-center gap-3 p-3 rounded-lg {{ $active('admin.schools.*') }}">
                    <span class="text-lg">📋</span> Kelola Sekolah
                </a>
                @endif

                @if (Route::has('admin.pengawas.index'))
                <a href="{{ route('admin.pengawas.index') }}"
                   class="flex items-center gap-3 p-3 rounded-lg {{ $active('admin.pengawas.*') }}">
                    <span class="text-lg">📋</span> Kelola Pengawas
                </a>
                @endif

            @endhasrole


            {{-- Logout --}}
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="mt-6 text-red-600 font-semibold hover:underline">
                    Log Out
                </button>
            </form>

        </nav>
    </aside>

    <div class="flex-1">
        <header class="bg-white shadow px-6 py-4 flex items-center justify-between">
            <div class="flex-1">
            </div>
            <div class="flex items-center gap-4">

                <div class="flex flex-col text-right">
                    <span class="font-semibold text-sm">
                        {{ auth()->user()->name }}
                    </span>
                    <span class="text-xs text-gray-500">
                        {{ auth()->user()->getRoleNames()->implode(', ') }}
                    </span>
                </div>

                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}"
                        class="w-10 h-10 rounded-full">
            </div>
        </header>

        <main class="p-6">
            @yield('content')
        </main>

    </div>

</div>

</body>
</html>
