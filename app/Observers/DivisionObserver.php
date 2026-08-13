<?php

namespace App\Observers;

use App\Models\Division;
use App\Services\ScreeningQuestionManager;

class DivisionObserver
{
    public function created(Division $division): void
    {
        app(ScreeningQuestionManager::class)->ensureDivisionQuestions($division);
    }

    public function updated(Division $division): void
    {
        if (! $division->wasChanged('name')) {
            return;
        }

        app(ScreeningQuestionManager::class)->ensureDivisionQuestions($division);
    }
}
