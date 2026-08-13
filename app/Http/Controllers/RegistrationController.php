<?php

namespace App\Http\Controllers;

use App\Enums\RegistrationStatus;
use App\Http\Requests\StoreRegistrationRequest;
use App\Models\Division;
use App\Models\Question;
use App\Models\Registration;
use App\Models\RegistrationAnswer;
use App\Models\RegistrationScreeningAnswer;
use App\Models\ScreeningQuestion;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class RegistrationController extends Controller
{
    public function index(): View
    {
        return view('home', [
            'divisions' => $this->activeDivisions(withCount: true),
            'programs' => $this->programs(),
            'benefits' => $this->benefits(),
            'faqs' => $this->faqs(),
        ]);
    }

    public function create(): View
    {
        return view('registrations.create', [
            'divisions' => $this->activeDivisions(),
        ]);
    }

    public function store(StoreRegistrationRequest $request): RedirectResponse
    {
        $registration = retry(
            3,
            fn (): Registration => DB::transaction(
                fn (): Registration => Registration::query()->create(
                    $request->safe()->merge([
                        'status' => RegistrationStatus::Pending,
                    ])->all(),
                ),
            ),
            100,
            fn (Throwable $exception): bool => $this->causedByDuplicateRegistrationNumber($exception),
        );

        return redirect()->route('registrations.basic-question', $registration)
            ->with('success', 'Data pendaftaran awal berhasil disimpan. Silakan jawab pertanyaan dasar divisi berikut.');
    }

    public function success(Registration $registration): View
    {
        return view('registrations.success', [
            'registration' => $registration->load('division'),
        ]);
    }

    public function showBasicQuestion(Registration $registration): View|RedirectResponse
    {
        if ($registration->hasCompletedBasicQuestion()) {
            return redirect()->route('registrations.status', [
                'registration_number' => $registration->registration_number,
                'email' => $registration->email,
            ])->with('info', 'Pertanyaan dasar sudah dijawab. Silakan lanjutkan dari portal status pendaftaran.');
        }

        return view('registrations.basic-question', [
            'registration' => $registration->load('division'),
            'questions' => $this->basicScreeningQuestions($registration),
        ]);
    }

    public function submitBasicQuestion(Request $request, Registration $registration): RedirectResponse
    {
        if ($registration->hasCompletedBasicQuestion()) {
            return redirect()->route('registrations.status', [
                'registration_number' => $registration->registration_number,
                'email' => $registration->email,
            ]);
        }

        $questions = $this->basicScreeningQuestions($registration);

        if ($questions->isEmpty()) {
            return back()
                ->withErrors(['answers' => 'Pertanyaan dasar belum tersedia. Jalankan seeder pertanyaan terlebih dahulu.'])
                ->withInput();
        }

        $validated = $request->validate(
            $questions
                ->mapWithKeys(fn (ScreeningQuestion $question): array => [
                    "answers.{$question->id}" => ['required', 'string', 'min:5', 'max:1000'],
                ])
                ->all(),
            [
                'answers.*.required' => 'Semua pertanyaan dasar wajib dijawab.',
                'answers.*.min' => 'Jawaban minimal 5 karakter.',
                'answers.*.max' => 'Jawaban maksimal 1000 karakter.',
            ],
            $questions
                ->mapWithKeys(fn (ScreeningQuestion $question, int $index): array => [
                    "answers.{$question->id}" => 'jawaban pertanyaan '.($index + 1),
                ])
                ->all(),
        );

        $answersInput = $validated['answers'] ?? [];
        $summary = '';

        DB::transaction(function () use ($registration, $questions, $answersInput, &$summary): void {
            $summary = $questions
                ->map(function (ScreeningQuestion $question) use ($registration, $answersInput): string {
                    $answer = str((string) ($answersInput[$question->id] ?? ''))
                        ->replaceMatches('/\s+/', ' ')
                        ->trim()
                        ->toString();

                    RegistrationScreeningAnswer::query()->updateOrCreate([
                        'registration_id' => $registration->id,
                        'screening_question_id' => $question->id,
                    ], [
                        'answer' => $answer,
                    ]);

                    return "{$question->question_text}\n{$answer}";
                })
                ->implode("\n\n");

            $registration->update([
                'basic_question_answer' => $summary,
                'basic_completed_at' => now(),
            ]);
        });

        $request->session()->put('candidate_registration_id', $registration->id);

        return redirect()->route('registrations.status', [
            'registration_number' => $registration->registration_number,
            'email' => $registration->email,
        ])->with('success', 'Pertanyaan dasar berhasil dikirim. Pendaftaran kamu sudah diterima sistem, lanjutkan tes seleksi dari portal status.');
    }

    public function showQuiz(Request $request, Registration $registration): View|RedirectResponse
    {
        if ($redirect = $this->redirectIfCannotAccessAdvancedQuiz($request, $registration)) {
            return $redirect;
        }

        if ($registration->isQuizCompleted()) {
            return redirect()->route('registrations.status', [
                'registration_number' => $registration->registration_number,
                'email' => $registration->email,
            ])->with('info', 'Kamu telah menyelesaikan tes divisi untuk pendaftaran ini.');
        }

        $questions = Question::query()
            ->where('division_id', $registration->division_id)
            ->take(5)
            ->get();

        return view('registrations.quiz', [
            'registration' => $registration->load('division'),
            'questions' => $questions,
        ]);
    }

    public function submitQuiz(Request $request, Registration $registration): RedirectResponse
    {
        if ($redirect = $this->redirectIfCannotAccessAdvancedQuiz($request, $registration)) {
            return $redirect;
        }

        if ($registration->isQuizCompleted()) {
            return redirect()->route('registrations.status', [
                'registration_number' => $registration->registration_number,
                'email' => $registration->email,
            ]);
        }

        $questions = Question::query()
            ->where('division_id', $registration->division_id)
            ->take(5)
            ->get();

        $request->validate(
            $questions
                ->mapWithKeys(fn (Question $question): array => [
                    "answers.{$question->id}" => ['required', 'string', 'in:a,b,c,d'],
                ])
                ->all(),
            [],
            $questions
                ->mapWithKeys(fn (Question $question, int $index): array => [
                    "answers.{$question->id}" => 'jawaban soal '.($index + 1),
                ])
                ->all(),
        );

        $answersInput = $request->input('answers', []);
        $totalScore = 0;

        DB::transaction(function () use ($registration, $questions, $answersInput, &$totalScore): void {
            foreach ($questions as $question) {
                $selectedOption = strtolower((string) ($answersInput[$question->id] ?? ''));
                $isCorrect = $selectedOption !== '' && $selectedOption === strtolower($question->correct_option);
                $scoreEarned = $isCorrect ? $question->points : 0;
                $totalScore += $scoreEarned;

                RegistrationAnswer::query()->updateOrCreate([
                    'registration_id' => $registration->id,
                    'question_id' => $question->id,
                ], [
                    'selected_option' => $selectedOption ?: '-',
                    'is_correct' => $isCorrect,
                    'score_earned' => $scoreEarned,
                ]);
            }

            $registration->update([
                'score' => $totalScore,
                'test_completed_at' => now(),
                'status' => RegistrationStatus::Review,
            ]);
        });

        return redirect()->route('registrations.status', [
            'registration_number' => $registration->registration_number,
            'email' => $registration->email,
        ])->with('success', 'Tes divisi berhasil dikerjakan! Pendaftaran Anda kini sedang dalam peninjauan admin.');
    }

    public function status(Request $request): View
    {
        $registrationNumber = $request->query('registration_number');
        $email = $request->query('email');
        $registration = null;

        if ($registrationNumber && $email) {
            $registration = Registration::query()
                ->where('registration_number', trim((string) $registrationNumber))
                ->where('email', strtolower(trim((string) $email)))
                ->with(['division', 'answers.question', 'screeningAnswers.screeningQuestion'])
                ->first();
        }

        if (! $registration && $request->session()->has('candidate_registration_id')) {
            $registration = Registration::query()
                ->with(['division', 'answers.question', 'screeningAnswers.screeningQuestion'])
                ->find($request->session()->get('candidate_registration_id'));

            $registrationNumber = $registration?->registration_number;
            $email = $registration?->email;
        }

        return view('registrations.status', [
            'registration' => $registration,
            'registrationNumber' => $registrationNumber,
            'email' => $email,
            'isCandidateAuthenticated' => $registration !== null
                && (int) $request->session()->get('candidate_registration_id') === (int) $registration->id,
        ]);
    }

    public function checkStatus(Request $request): RedirectResponse
    {
        $request->validate([
            'registration_number' => ['required', 'string'],
            'email' => ['required', 'email'],
        ], [
            'registration_number.required' => 'Nomor Pendaftaran wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $registrationNumber = trim((string) $request->input('registration_number'));
        $email = strtolower(trim((string) $request->input('email')));

        $registration = Registration::query()
            ->where('registration_number', $registrationNumber)
            ->where('email', $email)
            ->first();

        if ($registration) {
            $request->session()->put('candidate_registration_id', $registration->id);
        } else {
            $request->session()->forget('candidate_registration_id');
        }

        return redirect()->route('registrations.status', [
            'registration_number' => $registrationNumber,
            'email' => $email,
        ]);
    }

    public function logoutCandidate(Request $request): RedirectResponse
    {
        $request->session()->forget('candidate_registration_id');

        return redirect()->route('registrations.status')
            ->with('info', 'Sesi pendaftar sudah keluar.');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Division>
     */
    private function activeDivisions(bool $withCount = false): \Illuminate\Database\Eloquent\Collection
    {
        $query = Division::query()
            ->active()
            ->orderBy('name');

        if ($withCount) {
            $query->withCount('registrations');
        }

        return $query->get();
    }

    /**
     * @return array<int, array{title: string, description: string, icon: string}>
     */
    private function programs(): array
    {
        return [
            [
                'title' => 'Leadership Class',
                'description' => 'Sesi pengembangan kepemimpinan, komunikasi, dan problem solving untuk anggota baru.',
                'icon' => 'heroicon-o-academic-cap',
            ],
            [
                'title' => 'Community Project',
                'description' => 'Program kolaboratif untuk membantu anggota belajar menjalankan project nyata.',
                'icon' => 'heroicon-o-briefcase',
            ],
            [
                'title' => 'Member Gathering',
                'description' => 'Agenda rutin untuk mengenal tim, membangun relasi, dan menjaga budaya organisasi.',
                'icon' => 'heroicon-o-users',
            ],
        ];
    }

    /**
     * @return array<int, array{title: string, description: string, icon: string}>
     */
    private function benefits(): array
    {
        return [
            [
                'title' => 'Portfolio kegiatan',
                'description' => 'Ikut project, acara, dan dokumentasi kegiatan yang bisa diceritakan kembali secara profesional.',
                'icon' => 'heroicon-o-folder-open',
            ],
            [
                'title' => 'Networking sehat',
                'description' => 'Bertemu anggota lintas minat dengan kultur kolaboratif dan saling bantu.',
                'icon' => 'heroicon-o-chat-bubble-left-right',
            ],
            [
                'title' => 'Skill praktis',
                'description' => 'Belajar komunikasi, manajemen acara, publikasi, keuangan, teknologi, dan kerja tim.',
                'icon' => 'heroicon-o-sparkles',
            ],
        ];
    }

    /**
     * @return array<int, array{question: string, answer: string}>
     */
    private function faqs(): array
    {
        return [
            [
                'question' => 'Apakah harus punya pengalaman organisasi?',
                'answer' => 'Tidak. Pengalaman membantu, tetapi yang paling penting adalah komitmen untuk belajar dan bekerja sama.',
            ],
            [
                'question' => 'Bolehkah memilih divisi yang belum pernah saya kuasai?',
                'answer' => 'Boleh. Divisi adalah tempat berkembang, bukan hanya tempat untuk orang yang sudah mahir.',
            ],
            [
                'question' => 'Apa yang terjadi setelah mendaftar?',
                'answer' => 'Data kamu akan direview admin. Simpan nomor pendaftaran untuk kebutuhan follow-up.',
            ],
        ];
    }

    private function causedByDuplicateRegistrationNumber(Throwable $exception): bool
    {
        return $exception instanceof QueryException
            && $exception->getCode() === '23000'
            && str($exception->getMessage())->contains('registration_number', ignoreCase: true);
    }

    private function redirectIfCannotAccessAdvancedQuiz(Request $request, Registration $registration): ?RedirectResponse
    {
        if (! $registration->hasCompletedBasicQuestion()) {
            return redirect()->route('registrations.basic-question', $registration)
                ->with('info', 'Jawab pertanyaan dasar terlebih dahulu sebelum mengerjakan tes lanjutan.');
        }

        if ((int) $request->session()->get('candidate_registration_id') !== (int) $registration->id) {
            return redirect()->route('registrations.status')
                ->with('info', 'Silakan login pendaftar memakai nomor pendaftaran dan email sebelum mengerjakan tes lanjutan.');
        }

        return null;
    }

    /**
     * @return EloquentCollection<int, ScreeningQuestion>
     */
    private function basicScreeningQuestions(Registration $registration): EloquentCollection
    {
        return ScreeningQuestion::query()
            ->where('is_active', true)
            ->where(function ($query) use ($registration): void {
                $query
                    ->whereNull('division_id')
                    ->orWhere('division_id', $registration->division_id);
            })
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }
}
