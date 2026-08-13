@extends('layouts.public')

@section('title', 'Tes Pengetahuan Divisi - ' . $registration->division->name)

@section('content')
<div class="py-12 bg-slate-900 min-h-screen text-slate-100">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header & Instructions -->
        <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 sm:p-8 mb-8 shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-700 pb-6 mb-6">
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20 mb-2">
                        Tahap 3 dari 5: Tes Lanjutan
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white">Tes Lanjutan Divisi: {{ $registration->division->name }}</h1>
                    <p class="text-slate-400 text-sm mt-1">Nomor Pendaftaran: <span class="font-mono text-amber-400 font-semibold">{{ $registration->registration_number }}</span> ({{ $registration->name }})</p>
                </div>
                <div class="text-right">
                    <span class="text-xs text-slate-400 block">Total Soal</span>
                    <span class="text-2xl font-black text-white">{{ $questions->count() }} Soal</span>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 p-4 rounded-xl text-sm mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-slate-900/60 rounded-xl p-4 border border-slate-700/60 text-sm text-slate-300 space-y-2">
                <p class="font-semibold text-amber-300">📌 Petunjuk Pengerjaan:</p>
                <ul class="list-disc list-inside space-y-1 text-slate-400">
                    <li>Pilih satu jawaban yang paling tepat untuk setiap pertanyaan di bawah ini.</li>
                    <li>Setiap soal memiliki bobot poin yang berkontribusi pada skor akhir Anda (0 - 100).</li>
                    <li>Setelah selesai menjawab, tekan tombol <strong>"Kirim Jawaban & Selesaikan Tes"</strong> di bagian bawah.</li>
                </ul>
            </div>
        </div>

        @if($questions->isEmpty())
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-8 text-center">
                <div class="w-16 h-16 bg-amber-500/10 text-amber-400 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 101-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h2 class="text-xl font-bold text-white mb-2">Belum Ada Soal Khusus untuk Divisi Ini</h2>
                <p class="text-slate-400 text-sm mb-6">Silakan langsung selesaikan proses pendaftaran kamu untuk diverifikasi oleh admin.</p>
                <form action="{{ route('registrations.quiz.submit', $registration) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-xl shadow-lg transition">
                        Lanjut ke Peninjauan Admin &rarr;
                    </button>
                </form>
            </div>
        @else
            <!-- Quiz Form -->
            <form action="{{ route('registrations.quiz.submit', $registration) }}" method="POST" class="space-y-6">
                @csrf

                @foreach($questions as $index => $question)
                    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-lg">
                        <div class="flex items-start gap-4 mb-4">
                            <span class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 font-bold text-sm flex items-center justify-center border border-amber-500/20 shrink-0">
                                {{ $index + 1 }}
                            </span>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-white leading-snug">
                                    {{ $question->question_text }}
                                </h3>
                                <span class="text-xs text-slate-500 mt-1 inline-block">Bobot: {{ $question->points }} Poin</span>
                            </div>
                        </div>

                        <!-- Options -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pl-0 sm:pl-12">
                            @foreach(['a' => $question->option_a, 'b' => $question->option_b, 'c' => $question->option_c, 'd' => $question->option_d] as $key => $optionValue)
                                <label class="flex items-center p-3.5 bg-slate-900/60 hover:bg-slate-700/50 border border-slate-700 rounded-xl cursor-pointer transition group">
                                    <input type="radio" 
                                           name="answers[{{ $question->id }}]" 
                                           value="{{ $key }}" 
                                           class="w-4 h-4 text-amber-500 bg-slate-800 border-slate-600 focus:ring-amber-500 focus:ring-offset-slate-900" 
                                           required>
                                    <span class="ml-3 text-sm font-medium text-slate-300 group-hover:text-white">
                                        <span class="font-bold uppercase text-amber-400 mr-1.5">{{ strtoupper($key) }}.</span> {{ $optionValue }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <!-- Submit Action -->
                <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-xl">
                    <p class="text-xs text-slate-400 text-center sm:text-left">
                        Pastikan seluruh jawaban sudah Anda periksa dengan seksama sebelum dikirim.
                    </p>
                    <button type="submit" class="w-full sm:w-auto px-8 py-3.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-xl shadow-lg transition duration-200">
                        Kirim Jawaban & Selesaikan Tes &rarr;
                    </button>
                </div>
            </form>
        @endif

    </div>
</div>
@endsection
