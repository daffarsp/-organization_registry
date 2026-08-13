<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Services\ScreeningQuestionManager;
use Illuminate\Database\Seeder;

class ScreeningQuestionSeeder extends Seeder
{
    /**
     * Seed the basic screening questions.
     */
    public function run(): void
    {
        $manager = app(ScreeningQuestionManager::class);

        $manager->ensureCommonQuestions();

        Division::query()
            ->orderBy('name')
            ->get()
            ->each(fn (Division $division) => $manager->ensureDivisionQuestions($division));
    }
}
