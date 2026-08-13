<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Sistem pendaftaran anggota organisasi yang sederhana, modern, dan responsif.">

        @php($pageTitle = $title ?? trim($__env->yieldContent('title')))
        <title>{{ $pageTitle !== '' ? $pageTitle : config('app.name', 'Organization Registration System') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-zinc-50 font-sans text-zinc-950 antialiased">
        <div class="min-h-screen">
            <header class="sticky top-0 z-40 border-b border-zinc-200 bg-white/90 backdrop-blur">
                <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8" aria-label="Main navigation">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 text-sm font-semibold text-zinc-950">
                        <span class="grid h-9 w-9 place-items-center rounded-md bg-emerald-600 text-white">
                            <x-heroicon-o-users class="h-5 w-5" />
                        </span>
                        <span>Organization Registration</span>
                    </a>

                    <div class="hidden items-center gap-6 text-sm font-medium text-zinc-700 md:flex">
                        <a class="hover:text-emerald-700" href="{{ route('home') }}#tentang">Tentang</a>
                        <a class="hover:text-emerald-700" href="{{ route('home') }}#divisi">Divisi</a>
                        <a class="hover:text-emerald-700" href="{{ route('home') }}#program">Program</a>
                        <a class="hover:text-emerald-700" href="{{ route('home') }}#faq">FAQ</a>
                    </div>

                    <a href="{{ route('registrations.create') }}" class="inline-flex items-center gap-2 rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                        <x-heroicon-o-user-plus class="h-4 w-4" />
                        Daftar
                    </a>
                </nav>
            </header>

            <main>
                @isset($slot)
                    {{ $slot }}
                @else
                    @yield('content')
                @endisset
            </main>

            <footer class="border-t border-zinc-200 bg-white">
                <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-8 text-sm text-zinc-600 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
                    <p>&copy; {{ date('Y') }} Organization Registration System.</p>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('home') }}#tentang" class="hover:text-emerald-700">Tentang</a>
                        <a href="{{ route('home') }}#faq" class="hover:text-emerald-700">FAQ</a>
                        <a href="{{ route('registrations.create') }}" class="hover:text-emerald-700">Pendaftaran</a>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
