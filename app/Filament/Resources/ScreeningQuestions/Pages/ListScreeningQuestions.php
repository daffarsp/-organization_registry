<?php

namespace App\Filament\Resources\ScreeningQuestions\Pages;

use App\Filament\Resources\ScreeningQuestions\ScreeningQuestionResource;
use App\Models\Division;
use App\Models\User;
use App\Services\ScreeningQuestionManager;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListScreeningQuestions extends ListRecords
{
    protected static string $resource = ScreeningQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('syncDefaults')
                ->label('Sinkron Default')
                ->icon('heroicon-o-arrow-path')
                ->visible(fn (): bool => self::currentUser()?->canManageQuestions() ?? false)
                ->requiresConfirmation()
                ->action(function (): void {
                    $manager = app(ScreeningQuestionManager::class);

                    $manager->ensureCommonQuestions();
                    Division::query()
                        ->orderBy('name')
                        ->get()
                        ->each(fn (Division $division) => $manager->ensureDivisionQuestions($division));

                    Notification::make()
                        ->title('Pertanyaan default tersinkron')
                        ->body('Pertanyaan umum dan pertanyaan sederhana per divisi sudah diperbarui.')
                        ->success()
                        ->send();
                }),
            CreateAction::make()
                ->label('Tambah Pertanyaan')
                ->visible(fn (): bool => self::currentUser()?->canManageQuestions() ?? false),
        ];
    }

    private static function currentUser(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }
}
