<?php

namespace App\Services;

use App\Models\Division;
use App\Models\ScreeningQuestion;

class ScreeningQuestionManager
{
    /**
     * @return array<int, array{question_text: string, sort_order: int}>
     */
    public function commonQuestions(): array
    {
        return [
            ['question_text' => 'Dari mana kamu mengetahui organisasi ini?', 'sort_order' => 1],
            ['question_text' => 'Kenapa kamu ingin bergabung dengan organisasi ini?', 'sort_order' => 2],
            ['question_text' => 'Kenapa kamu layak menjadi bagian dari organisasi ini?', 'sort_order' => 3],
        ];
    }

    /**
     * @return array<int, array{question_text: string, sort_order: int}>
     */
    public function divisionQuestions(Division $division): array
    {
        $divisionLabel = str($division->name)->startsWith('Divisi ')
            ? $division->name
            : "Divisi {$division->name}";

        return [
            [
                'question_text' => "Apa yang kamu ketahui tentang tugas utama {$divisionLabel}?",
                'sort_order' => 4,
            ],
            [
                'question_text' => "Kegiatan sederhana apa yang ingin kamu bantu lakukan di {$divisionLabel}?",
                'sort_order' => 5,
            ],
        ];
    }

    public function ensureCommonQuestions(): void
    {
        collect($this->commonQuestions())->each(
            fn (array $question): ScreeningQuestion => ScreeningQuestion::query()->updateOrCreate(
                [
                    'division_id' => null,
                    'sort_order' => $question['sort_order'],
                ],
                $question + ['is_active' => true],
            ),
        );
    }

    public function ensureDivisionQuestions(Division $division): void
    {
        collect($this->divisionQuestions($division))->each(
            fn (array $question): ScreeningQuestion => ScreeningQuestion::query()->updateOrCreate(
                [
                    'division_id' => $division->id,
                    'sort_order' => $question['sort_order'],
                ],
                $question + ['is_active' => true],
            ),
        );
    }
}
