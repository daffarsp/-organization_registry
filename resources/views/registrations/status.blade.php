@extends('layouts.public')

@section('title', 'Cek Status Pendaftaran')

@section('content')
<div class="min-h-screen bg-slate-900 py-12 text-slate-100">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <div class="mb-10 text-center">
            <span class="mb-3 inline-flex rounded-full border border-amber-500/20 bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-400">
                Portal Informasi Pendaftar
            </span>
            <h1 class="text-3xl font-extrabold text-white sm:text-4xl">Status Progress Pendaftaran</h1>
            <p class="mx-auto mt-2 max-w-xl text-sm text-slate-400">
                Login memakai Nomor Pendaftaran dan Email untuk memantau proses seleksi, mengerjakan tes lanjutan, dan melihat hasil kelulusan.
            </p>
        </div>

        <div class="mx-auto mb-10 max-w-2xl rounded-2xl border border-slate-700 bg-slate-800 p-6 shadow-xl sm:p-8">
            <form action="{{ route('registrations.status.check') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="registration_number" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            Nomor Pendaftaran *
                        </label>
                        <input
                            id="registration_number"
                            name="registration_number"
                            type="text"
                            value="{{ old('registration_number', $registrationNumber) }}"
                            placeholder="Contoh: REG-2026-0001"
                            required
                            class="w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 font-mono text-sm text-white placeholder-slate-500 outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                        >
                        @error('registration_number')
                            <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            Email Terdaftar *
                        </label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email', $email) }}"
                            placeholder="nama@email.com"
                            required
                            class="w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white placeholder-slate-500 outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                        >
                        @error('email')
                            <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="w-full rounded-xl bg-amber-500 py-3.5 font-bold text-slate-950 shadow-lg transition hover:bg-amber-400">
                    Login & Cek Status
                </button>
            </form>
        </div>

        @if(session('info'))
            <div class="mx-auto mb-6 max-w-2xl rounded-xl border border-amber-500/30 bg-amber-500/10 p-4 text-center text-sm text-amber-300">
                {{ session('info') }}
            </div>
        @endif

        @if(session('success'))
            <div class="mx-auto mb-6 max-w-2xl rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-center text-sm text-emerald-400">
                {{ session('success') }}
            </div>
        @endif

        @if($registration)
            <div class="space-y-8 rounded-2xl border border-slate-700 bg-slate-800 p-6 shadow-xl sm:p-8">
                <div class="flex flex-col justify-between gap-4 border-b border-slate-700 pb-6 sm:flex-row sm:items-center">
                    <div>
                        <span class="block font-mono text-xs font-semibold uppercase tracking-wider text-amber-400">
                            {{ $registration->registration_number }}
                        </span>
                        <h2 class="mt-1 text-2xl font-bold text-white">{{ $registration->name }}</h2>
                        <p class="text-sm text-slate-400">
                            Divisi: <strong class="text-white">{{ $registration->division->name }}</strong> | Email: {{ $registration->email }}
                        </p>
                    </div>

                    <div class="flex flex-col items-start sm:items-end">
                        <span class="mb-1 text-xs text-slate-400">Status Keputusan Admin:</span>
                        <span class="rounded-full border px-4 py-1.5 text-xs font-bold uppercase tracking-wider
                            @if($registration->status->value === 'accepted') border-emerald-500/30 bg-emerald-500/20 text-emerald-400
                            @elseif($registration->status->value === 'rejected') border-rose-500/30 bg-rose-500/20 text-rose-400
                            @elseif($registration->status->value === 'review') border-amber-500/30 bg-amber-500/20 text-amber-400
                            @else border-slate-600 bg-slate-700 text-slate-300 @endif">
                            {{ $registration->status->getLabel() }}
                        </span>

                        @if($isCandidateAuthenticated)
                            <form action="{{ route('registrations.logout') }}" method="POST" class="mt-3">
                                @csrf
                                <button type="submit" class="text-xs font-semibold text-slate-400 underline hover:text-amber-300">
                                    Keluar dari sesi pendaftar
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="mb-6 text-sm font-semibold uppercase tracking-wider text-slate-400">Tahapan Seleksi Pendaftaran</h3>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
                        <div class="rounded-xl border border-emerald-500/40 bg-slate-900/80 p-4">
                            <div class="mb-2 flex items-center gap-2">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500 text-xs font-bold text-slate-950">OK</span>
                                <span class="text-xs font-semibold text-emerald-400">Tahap 1</span>
                            </div>
                            <h4 class="text-sm font-bold text-white">Formulir Terisi</h4>
                            <p class="mt-1 text-xs text-slate-400">Data pendaftaran telah tersimpan.</p>
                        </div>

                        <div class="rounded-xl border @if($registration->hasCompletedBasicQuestion()) border-emerald-500/40 @else border-amber-500/40 @endif bg-slate-900/80 p-4">
                            <div class="mb-2 flex items-center gap-2">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full @if($registration->hasCompletedBasicQuestion()) bg-emerald-500 @else bg-amber-500 @endif text-xs font-bold text-slate-950">
                                    @if($registration->hasCompletedBasicQuestion()) OK @else 2 @endif
                                </span>
                                <span class="text-xs font-semibold @if($registration->hasCompletedBasicQuestion()) text-emerald-400 @else text-amber-400 @endif">Tahap 2</span>
                            </div>
                            <h4 class="text-sm font-bold text-white">Pertanyaan Dasar</h4>
                            <p class="mt-1 text-xs text-slate-400">
                                @if($registration->hasCompletedBasicQuestion())
                                    Selesai {{ $registration->basic_completed_at?->format('d M Y H:i') }}
                                @else
                                    <a href="{{ route('registrations.basic-question', $registration) }}" class="font-semibold text-amber-400 underline hover:text-amber-300">Jawab sekarang</a>
                                @endif
                            </p>
                        </div>

                        <div class="rounded-xl border @if($registration->isQuizCompleted()) border-emerald-500/40 @else border-amber-500/40 @endif bg-slate-900/80 p-4">
                            <div class="mb-2 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="flex h-6 w-6 items-center justify-center rounded-full @if($registration->isQuizCompleted()) bg-emerald-500 @else bg-amber-500 @endif text-xs font-bold text-slate-950">
                                        @if($registration->isQuizCompleted()) OK @else 3 @endif
                                    </span>
                                    <span class="text-xs font-semibold @if($registration->isQuizCompleted()) text-emerald-400 @else text-amber-400 @endif">Tahap 3</span>
                                </div>
                                @if($registration->score !== null)
                                    <span class="font-mono text-xs font-bold text-amber-400">{{ $registration->score }}/100</span>
                                @endif
                            </div>
                            <h4 class="text-sm font-bold text-white">Tes Divisi</h4>
                            <p class="mt-1 text-xs text-slate-400">
                                @if($registration->isQuizCompleted())
                                    Selesai {{ $registration->test_completed_at?->format('d M Y H:i') }}
                                @elseif(! $registration->hasCompletedBasicQuestion())
                                    Selesaikan pertanyaan dasar dulu.
                                @elseif($isCandidateAuthenticated)
                                    <a href="{{ route('registrations.quiz', $registration) }}" class="font-semibold text-amber-400 underline hover:text-amber-300">Kerjakan soal sekarang</a>
                                @else
                                    Login pendaftar untuk membuka tes.
                                @endif
                            </p>
                        </div>

                        <div class="rounded-xl border @if(in_array($registration->status->value, ['review', 'accepted', 'rejected'])) border-emerald-500/40 @else border-slate-700 @endif bg-slate-900/80 p-4">
                            <div class="mb-2 flex items-center gap-2">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full @if(in_array($registration->status->value, ['review', 'accepted', 'rejected'])) bg-emerald-500 text-slate-950 @else bg-slate-700 text-slate-400 @endif text-xs font-bold">
                                    @if(in_array($registration->status->value, ['review', 'accepted', 'rejected'])) OK @else 4 @endif
                                </span>
                                <span class="text-xs font-semibold text-slate-400">Tahap 4</span>
                            </div>
                            <h4 class="text-sm font-bold text-white">Review Admin</h4>
                            <p class="mt-1 text-xs text-slate-400">
                                @if($registration->status->value === 'pending')
                                    Menunggu tes lanjutan selesai.
                                @elseif($registration->status->value === 'review')
                                    <span class="font-semibold text-amber-400">Sedang direview oleh tim.</span>
                                @else
                                    Peninjauan selesai.
                                @endif
                            </p>
                        </div>

                        <div class="rounded-xl border
                            @if($registration->status->value === 'accepted') border-emerald-500/60
                            @elseif($registration->status->value === 'rejected') border-rose-500/60
                            @else border-slate-700 @endif bg-slate-900/80 p-4">
                            <div class="mb-2 flex items-center gap-2">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full
                                    @if($registration->status->value === 'accepted') bg-emerald-500 text-slate-950
                                    @elseif($registration->status->value === 'rejected') bg-rose-500 text-white
                                    @else bg-slate-700 text-slate-400 @endif text-xs font-bold">
                                    @if(in_array($registration->status->value, ['accepted', 'rejected'])) OK @else 5 @endif
                                </span>
                                <span class="text-xs font-semibold text-slate-400">Tahap 5</span>
                            </div>
                            <h4 class="text-sm font-bold text-white">Pengumuman</h4>
                            <p class="mt-1 text-xs text-slate-400">
                                @if($registration->status->value === 'accepted')
                                    <span class="font-bold text-emerald-400">Selamat, Anda diterima.</span>
                                @elseif($registration->status->value === 'rejected')
                                    <span class="font-bold text-rose-400">Mohon maaf, Anda belum lolos.</span>
                                @else
                                    Menunggu keputusan akhir.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                @if($registration->screeningAnswers->isNotEmpty())
                    <div class="rounded-xl border border-slate-700 bg-slate-900/60 p-4">
                        <h4 class="mb-4 text-xs font-bold uppercase tracking-wider text-slate-400">Jawaban Pertanyaan Dasar</h4>
                        <div class="space-y-4">
                            @foreach($registration->screeningAnswers as $answer)
                                <div>
                                    <p class="text-sm font-semibold text-white">{{ $answer->screeningQuestion?->question_text }}</p>
                                    <p class="mt-1 text-sm leading-6 text-slate-300">{{ $answer->answer }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @elseif($registration->basic_question_answer)
                    <div class="rounded-xl border border-slate-700 bg-slate-900/60 p-4">
                        <h4 class="mb-1 text-xs font-bold uppercase tracking-wider text-slate-400">Jawaban Pertanyaan Dasar</h4>
                        <p class="whitespace-pre-line text-sm leading-6 text-slate-300">{{ $registration->basic_question_answer }}</p>
                    </div>
                @endif

                @if($registration->notes)
                    <div class="rounded-xl border border-amber-500/30 bg-slate-900/60 p-4">
                        <h4 class="mb-1 text-xs font-bold uppercase tracking-wider text-amber-400">Catatan dari Admin</h4>
                        <p class="text-sm text-slate-300">{{ $registration->notes }}</p>
                    </div>
                @endif
            </div>
        @elseif($registrationNumber || $email)
            <div class="mx-auto max-w-2xl rounded-2xl border border-slate-700 bg-slate-800 p-8 text-center">
                <p class="text-sm text-slate-300">Data pendaftaran tidak ditemukan. Pastikan Nomor Pendaftaran dan Email sudah benar.</p>
            </div>
        @endif
    </div>
</div>
@endsection
