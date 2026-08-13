<?php

namespace App\Filament\Resources\Questions\Pages;

use App\Filament\Resources\Questions\QuestionResource;
use App\Models\AdminActivityLog;
use App\Models\Division;
use App\Models\User;
use App\Services\QuestionWordImporter;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ListQuestions extends ListRecords
{
    protected static string $resource = QuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importWord')
                ->label('Import Word')
                ->icon('heroicon-o-arrow-up-tray')
                ->visible(fn (): bool => self::currentUser()?->canManageQuestions() ?? false)
                ->schema([
                    Select::make('division_id')
                        ->label('Divisi Tujuan')
                        ->options(fn (): array => Division::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->required()
                        ->native(false),
                    FileUpload::make('question_file')
                        ->label('File Word (.docx)')
                        ->disk('local')
                        ->directory('imports/questions')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        ])
                        ->helperText('Gunakan format: Pertanyaan:, A., B., C., D., Jawaban:, dan opsional Poin:.')
                        ->required(),
                    TextInput::make('default_points')
                        ->label('Poin Default')
                        ->numeric()
                        ->default(20)
                        ->minValue(1)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $storedFile = Arr::first(Arr::wrap($data['question_file'] ?? null));

                    if (! is_string($storedFile)) {
                        Notification::make()
                            ->title('Import gagal')
                            ->body('File Word belum terunggah.')
                            ->danger()
                            ->send();

                        return;
                    }

                    try {
                        $division = Division::query()->findOrFail($data['division_id']);
                        $result = app(QuestionWordImporter::class)->import(
                            Storage::disk('local')->path($storedFile),
                            (int) $division->id,
                            (int) ($data['default_points'] ?? 20),
                        );

                        AdminActivityLog::record(
                            'question_imported',
                            $division,
                            "Mengimpor {$result['created']} soal Word untuk divisi {$division->name}.",
                            ['created' => $result['created']],
                        );

                        Notification::make()
                            ->title('Import Word berhasil')
                            ->body("{$result['created']} soal baru ditambahkan.")
                            ->success()
                            ->send();
                    } catch (Throwable $exception) {
                        Notification::make()
                            ->title('Import Word gagal')
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();
                    } finally {
                        Storage::disk('local')->delete($storedFile);
                    }
                }),
            CreateAction::make()
                ->label('Tambah Soal Baru')
                ->visible(fn (): bool => self::currentUser()?->canManageQuestions() ?? false),
        ];
    }

    private static function currentUser(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }
}
