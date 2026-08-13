<x-layouts.public title="Pendaftaran Berhasil - Organization Registration System">
    <section class="bg-white py-16 sm:py-24">
        <div class="mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
            <div class="mx-auto grid h-16 w-16 place-items-center rounded-lg bg-emerald-100 text-emerald-700">
                <x-heroicon-o-check-circle class="h-9 w-9" />
            </div>

            <h1 class="mt-8 text-3xl font-semibold text-zinc-950 sm:text-4xl">Pendaftaran berhasil!</h1>
            <p class="mt-4 text-base leading-8 text-zinc-600">
                Terima kasih, {{ $registration->name }}. Data pendaftaran kamu sudah masuk dan akan direview oleh admin.
            </p>

            <div class="mt-8 rounded-lg border border-emerald-200 bg-emerald-50 p-6 text-left">
                <p class="text-sm font-semibold uppercase tracking-normal text-emerald-700">Nomor Pendaftaran</p>
                <p class="mt-3 break-words text-3xl font-semibold text-emerald-950">{{ $registration->registration_number }}</p>
                <dl class="mt-6 grid gap-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="font-semibold text-zinc-900">Divisi</dt>
                        <dd class="mt-1 text-zinc-700">{{ $registration->division->name }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-zinc-900">Status</dt>
                        <dd class="mt-1 text-zinc-700">{{ $registration->status->getLabel() }}</dd>
                    </div>
                </dl>
            </div>

            <p class="mt-6 text-sm leading-6 text-zinc-600">
                Simpan nomor pendaftaran ini untuk follow-up proses review.
            </p>

            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 rounded-md border border-zinc-300 px-5 py-3 text-sm font-semibold text-zinc-900 transition hover:border-emerald-500 hover:text-emerald-700">
                    <x-heroicon-o-home class="h-5 w-5" />
                    Kembali ke Beranda
                </a>
                <a href="{{ route('registrations.create') }}" class="inline-flex items-center justify-center gap-2 rounded-md bg-zinc-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">
                    <x-heroicon-o-user-plus class="h-5 w-5" />
                    Daftar Lagi
                </a>
            </div>
        </div>
    </section>
</x-layouts.public>
