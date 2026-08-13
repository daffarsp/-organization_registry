<x-layouts.public title="Organization Registration System">
    <section class="relative isolate overflow-hidden bg-zinc-950">
        <img
            src="https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=1800&q=80"
            alt="Kelompok anak muda sedang berkolaborasi dalam kegiatan komunitas"
            class="absolute inset-0 -z-10 h-full w-full object-cover opacity-45"
        >
        <div class="absolute inset-0 -z-10 bg-gradient-to-r from-zinc-950 via-zinc-950/80 to-emerald-950/40"></div>

        <div class="mx-auto grid min-h-[76vh] max-w-7xl items-center gap-10 px-4 py-20 sm:px-6 lg:grid-cols-[1.05fr_0.95fr] lg:px-8">
            <div class="max-w-3xl">
                <div class="mb-5 inline-flex items-center gap-2 rounded-md border border-white/20 bg-white/10 px-3 py-1.5 text-sm font-medium text-emerald-50 backdrop-blur">
                    <x-heroicon-o-sparkles class="h-4 w-4" />
                    Pendaftaran anggota baru dibuka
                </div>
                <h1 class="max-w-4xl text-4xl font-semibold tracking-normal text-white sm:text-5xl lg:text-6xl">
                    Organization Registration System
                </h1>
                <p class="mt-6 max-w-2xl text-base leading-8 text-zinc-100 sm:text-lg">
                    Bergabung dengan organisasi yang memberi ruang untuk belajar, berkolaborasi, dan menjalankan program nyata bersama tim lintas divisi.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('registrations.create') }}" class="inline-flex items-center justify-center gap-2 rounded-md bg-emerald-500 px-5 py-3 text-sm font-semibold text-emerald-950 shadow-sm transition hover:bg-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-300 focus:ring-offset-2 focus:ring-offset-zinc-950">
                        <x-heroicon-o-user-plus class="h-5 w-5" />
                        Daftar Sekarang
                    </a>
                    <a href="#divisi" class="inline-flex items-center justify-center gap-2 rounded-md border border-white/30 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                        <x-heroicon-o-squares-2x2 class="h-5 w-5" />
                        Lihat Divisi
                    </a>
                </div>
            </div>

            <div class="grid gap-3 rounded-lg border border-white/15 bg-white/10 p-4 text-white backdrop-blur sm:grid-cols-3 lg:grid-cols-1">
                <div class="rounded-md bg-white/10 p-4">
                    <p class="text-3xl font-semibold">{{ $divisions->count() }}</p>
                    <p class="mt-1 text-sm text-zinc-100">Divisi aktif</p>
                </div>
                <div class="rounded-md bg-white/10 p-4">
                    <p class="text-3xl font-semibold">{{ $divisions->sum('registrations_count') }}</p>
                    <p class="mt-1 text-sm text-zinc-100">Pendaftar tercatat</p>
                </div>
                <div class="rounded-md bg-white/10 p-4">
                    <p class="text-3xl font-semibold">24/7</p>
                    <p class="mt-1 text-sm text-zinc-100">Form online</p>
                </div>
            </div>
        </div>
    </section>

    <section id="tentang" class="bg-white py-16 sm:py-20">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[0.85fr_1.15fr] lg:px-8">
            <div>
                <p class="text-sm font-semibold uppercase tracking-normal text-emerald-700">Tentang organisasi</p>
                <h2 class="mt-3 text-3xl font-semibold text-zinc-950">Tempat bertumbuh lewat kerja nyata.</h2>
            </div>
            <div class="grid gap-4 text-base leading-8 text-zinc-700">
                <p>
                    Organisasi ini dirancang sebagai ruang kolaborasi untuk anggota yang ingin mengasah kepemimpinan, komunikasi, manajemen program, dan keterampilan praktis lain melalui kegiatan yang terstruktur.
                </p>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-lg border border-zinc-200 p-5">
                        <x-heroicon-o-eye class="h-6 w-6 text-emerald-700" />
                        <h3 class="mt-4 font-semibold text-zinc-950">Visi</h3>
                        <p class="mt-2 text-sm leading-6">Menjadi komunitas yang aktif, adaptif, dan berdampak bagi anggota serta lingkungan sekitar.</p>
                    </div>
                    <div class="rounded-lg border border-zinc-200 p-5">
                        <x-heroicon-o-flag class="h-6 w-6 text-sky-700" />
                        <h3 class="mt-4 font-semibold text-zinc-950">Misi</h3>
                        <p class="mt-2 text-sm leading-6">Membangun program berkualitas, budaya kolaboratif, dan kaderisasi yang berkelanjutan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="divisi" class="bg-zinc-50 py-16 sm:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-normal text-emerald-700">Divisi</p>
                    <h2 class="mt-3 text-3xl font-semibold text-zinc-950">Pilih ruang kontribusimu.</h2>
                </div>
                <a href="{{ route('registrations.create') }}" class="inline-flex items-center gap-2 rounded-md border border-zinc-300 bg-white px-4 py-2 text-sm font-semibold text-zinc-900 transition hover:border-emerald-500 hover:text-emerald-700">
                    <x-heroicon-o-arrow-right class="h-4 w-4" />
                    Mulai pendaftaran
                </a>
            </div>

            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($divisions as $division)
                    <article class="rounded-lg border border-zinc-200 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <h3 class="text-lg font-semibold text-zinc-950">{{ $division->name }}</h3>
                            <span class="rounded-md bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700">Aktif</span>
                        </div>
                        <p class="mt-3 text-sm leading-6 text-zinc-600">{{ $division->description }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section id="program" class="bg-white py-16 sm:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-normal text-emerald-700">Program</p>
            <h2 class="mt-3 text-3xl font-semibold text-zinc-950">Kegiatan yang dekat dengan praktik.</h2>
            <div class="mt-8 grid gap-4 md:grid-cols-3">
                @foreach ($programs as $program)
                    <article class="rounded-lg border border-zinc-200 p-5">
                        <x-dynamic-component :component="$program['icon']" class="h-7 w-7 text-sky-700" />
                        <h3 class="mt-4 font-semibold text-zinc-950">{{ $program['title'] }}</h3>
                        <p class="mt-2 text-sm leading-6 text-zinc-600">{{ $program['description'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-emerald-950 py-16 text-white sm:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-[0.8fr_1.2fr]">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-normal text-emerald-200">Benefit</p>
                    <h2 class="mt-3 text-3xl font-semibold">Lebih dari sekadar ikut kegiatan.</h2>
                </div>
                <div class="grid gap-4 sm:grid-cols-3">
                    @foreach ($benefits as $benefit)
                        <article class="rounded-lg border border-white/15 bg-white/10 p-5">
                            <x-dynamic-component :component="$benefit['icon']" class="h-7 w-7 text-emerald-200" />
                            <h3 class="mt-4 font-semibold">{{ $benefit['title'] }}</h3>
                            <p class="mt-2 text-sm leading-6 text-emerald-50">{{ $benefit['description'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section id="faq" class="bg-zinc-50 py-16 sm:py-20">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-normal text-emerald-700">FAQ</p>
            <h2 class="mt-3 text-3xl font-semibold text-zinc-950">Pertanyaan yang sering muncul.</h2>
            <div class="mt-8 divide-y divide-zinc-200 rounded-lg border border-zinc-200 bg-white">
                @foreach ($faqs as $faq)
                    <details class="group p-5">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-semibold text-zinc-950">
                            {{ $faq['question'] }}
                            <x-heroicon-o-chevron-down class="h-5 w-5 text-zinc-500 transition group-open:rotate-180" />
                        </summary>
                        <p class="mt-3 text-sm leading-6 text-zinc-600">{{ $faq['answer'] }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-white py-16 sm:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg border border-zinc-200 bg-zinc-950 px-6 py-10 text-white sm:px-10">
                <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold">Siap bergabung?</h2>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-zinc-200">Isi form pendaftaran dan simpan nomor pendaftaran setelah berhasil submit.</p>
                    </div>
                    <a href="{{ route('registrations.create') }}" class="inline-flex items-center justify-center gap-2 rounded-md bg-emerald-500 px-5 py-3 text-sm font-semibold text-emerald-950 transition hover:bg-emerald-400">
                        <x-heroicon-o-paper-airplane class="h-5 w-5" />
                        Daftar Sekarang
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
