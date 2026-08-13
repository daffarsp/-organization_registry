<?php

namespace App\Observers;

use App\Models\AdminActivityLog;
use Illuminate\Database\Eloquent\Model;

class AdminActivityObserver
{
    public function created(Model $model): void
    {
        AdminActivityLog::recordModelChange('created', $model);
    }

    public function updated(Model $model): void
    {
        $changes = collect($model->getChanges())
            ->except(['password', 'remember_token', 'updated_at'])
            ->all();

        if ($changes === []) {
            return;
        }

        AdminActivityLog::recordModelChange('updated', $model, $changes);
    }

    public function deleted(Model $model): void
    {
        AdminActivityLog::recordModelChange('deleted', $model);
    }
}
