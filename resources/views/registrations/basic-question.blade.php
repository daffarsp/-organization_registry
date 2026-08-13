@extends('layouts.public')

@section('title', 'Pertanyaan Dasar Divisi')

@section('content')
@php
    $totalQuestions = $questions->count();
@endphp

<div class="bg-zinc-50 py-10 sm:py-14">
    <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-[0.86fr_1.14fr] lg:px-8">
        <aside class="lg:sticky lg:top-24 lg:self-start">
            <a href="{{ route('registrations.create') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 hover:text-emerald-800">
                <x-heroicon-o-arrow-left class="h-4 w-4" />
                Kembali ke formulir
            </a>

            <div class="mt-6">
                <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                    <x-heroicon-o-clipboard-document-check class="h-4 w-4" />
                    Tahap 2 dari 5
                </span>
                <h1 class="mt-4 text-3xl font-semibold tracking-normal text-zinc-950">
                    Pertanyaan Dasar
                </h1>
                <p class="mt-3 text-base leading-7 text-zinc-600">
                    Jawab singkat dan jujur agar tim bisa memahami motivasi awal kamu sebelum tes lanjutan.
                </p>
            </div>

            <div class="mt-7 rounded-lg border border-zinc-200 bg-white p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-md bg-zinc-950 text-white">
                        <x-heroicon-o-user class="h-5 w-5" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-normal text-zinc-500">Pendaftar</p>
                        <h2 class="mt-1 truncate text-lg font-semibold text-zinc-950">{{ $registration->name }}</h2>
                        <p class="mt-1 break-all font-mono text-xs font-semibold text-emerald-700">{{ $registration->registration_number }}</p>
                    </div>
                </div>

                <dl class="mt-5 grid gap-4 text-sm">
                    <div>
                        <dt class="font-semibold text-zinc-900">Divisi Pilihan</dt>
                        <dd class="mt-1 text-zinc-600">{{ $registration->division->name }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-zinc-900">Email</dt>
                        <dd class="mt-1 break-all text-zinc-600">{{ $registration->email }}</dd>
                    </div>
                    <div>
                        <dt class="font-semibold text-zinc-900">Jumlah Pertanyaan</dt>
                        <dd class="mt-1 text-zinc-600">{{ $totalQuestions }} pertanyaan</dd>
                    </div>
                </dl>
            </div>

            <div class="mt-5 rounded-lg border border-amber-200 bg-amber-50 p-5">
                <div class="flex gap-3">
                    <x-heroicon-o-light-bulb class="h-5 w-5 shrink-0 text-amber-700" />
                    <div>
                        <h2 class="text-sm font-semibold text-amber-950">Setelah pertanyaan dasar</h2>
                        <p class="mt-2 text-sm leading-6 text-amber-900">
                            Kamu akan diarahkan ke portal status. Login dengan nomor pendaftaran dan email untuk membuka tes pilihan ganda lanjutan.
                        </p>
                    </div>
                </div>
            </div>
        </aside>

        <section>
            @if(session('success'))
                <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('info'))
                <div class="mb-5 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm font-medium text-amber-900">
                    {{ session('info') }}
                </div>
            @endif

            <form action="{{ route('registrations.basic-question.submit', $registration) }}" method="POST" class="rounded-lg border border-zinc-200 bg-white shadow-sm">
                @csrf

                <div class="border-b border-zinc-200 p-5 sm:p-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-zinc-950">Jawaban Awal Pendaftaran</h2>
                            <p class="mt-1 text-sm leading-6 text-zinc-600">Semua pertanyaan wajib dijawab sebelum lanjut ke tahap berikutnya.</p>
                        </div>
                        <span class="inline-flex w-fit items-center gap-2 rounded-md bg-zinc-100 px-3 py-2 text-xs font-semibold text-zinc-700">
                            <x-heroicon-o-list-bullet class="h-4 w-4" />
                            {{ $totalQuestions }} item
                        </span>
                    </div>
                </div>

                <div class="p-5 sm:p-6">
                    @error('answers')
                        <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                            {{ $message }}
                        </div>
                    @enderror

                    @if($questions->isEmpty())
                        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                            Pertanyaan dasar belum tersedia. Jalankan seeder pertanyaan terlebih dahulu atau sinkronkan dari panel admin.
                        </div>
                    @else
                        <div class="space-y-5">
                            @foreach($questions as $index => $question)
                                <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 sm:p-5">
                                    <div class="flex items-start gap-3">
                                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-md bg-emerald-600 text-sm font-bold text-white">
                                            {{ $index + 1 }}
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <label for="answer_{{ $question->id }}" class="block text-sm font-semibold leading-6 text-zinc-950">
                                                {{ $question->question_text }}
                                            </label>
                                            <textarea
                                                id="answer_{{ $question->id }}"
                                                name="answers[{{ $question->id }}]"
                                                rows="4"
                                                required
                                                minlength="5"
                                                maxlength="1000"
                                                class="mt-3 w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-950 outline-none transition placeholder:text-zinc-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                                                placeholder="Tulis jawaban singkat..."
                                            >{{ old("answers.{$question->id}") }}</textarea>
                                            <div class="mt-2 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                                                @error("answers.{$question->id}")
                                                    <p class="text-sm text-rose-600">{{ $message }}</p>
                                                @else
                                                    <p class="text-xs text-zinc-500">Minimal 5 karakter, maksimal 1000 karakter.</p>
                                                @enderror
                                                <p class="text-xs font-medium text-zinc-500">Wajib diisi</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-zinc-200 bg-zinc-50 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                    <a href="{{ route('registrations.status') }}" class="inline-flex items-center justify-center gap-2 rounded-md border border-zinc-300 px-4 py-2.5 text-sm font-semibold text-zinc-800 transition hover:border-emerald-500 hover:text-emerald-700">
                        <x-heroicon-o-magnifying-glass class="h-4 w-4" />
                        Cek Status
                    </a>
                    <button type="submit" @disabled($questions->isEmpty()) class="inline-flex items-center justify-center gap-2 rounded-md bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:bg-zinc-400">
                        <x-heroicon-o-paper-airplane class="h-5 w-5" />
                        Kirim Jawaban Dasar
                    </button>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection
