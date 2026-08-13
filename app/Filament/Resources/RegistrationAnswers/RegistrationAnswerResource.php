<?php

namespace App\Filament\Resources\RegistrationAnswers;

use App\Filament\Resources\RegistrationAnswers\Pages\ListRegistrationAnswers;
use App\Models\Division;
use App\Models\RegistrationAnswer;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class RegistrationAnswerResource extends Resource
{
    protected static ?string $model = RegistrationAnswer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $modelLabel = 'Jawaban Tes';

    protected static ?string $pluralModelLabel = 'Jawaban Tes';

    protected static string|UnitEnum|null $navigationGroup = 'Pendaftaran';

    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('registration.registration_number')
                    ->label('Nomor')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('registration.name')
                    ->label('Pendaftar')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('registration.division.name')
                    ->label('Divisi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('question.question_text')
                    ->label('Soal')
                    ->limit(70)
                    ->searchable(),
                TextColumn::make('selected_option')
                    ->label('Jawaban')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),
                TextColumn::make('question.correct_option')
                    ->label('Kunci')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn (?string $state): string => $state ? strtoupper($state) : '-'),
                IconColumn::make('is_correct')
                    ->label('Benar')
                    ->boolean(),
                TextColumn::make('score_earned')
                    ->label('Skor')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dikirim')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('division_id')
                    ->label('Divisi')
                    ->options(fn (): array => Division::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'] ?? null,
                        fn (Builder $query, int|string $divisionId): Builder => $query->whereHas(
                            'registration',
                            fn (Builder $query): Builder => $query->where('division_id', $divisionId),
                        ),
                    ))
                    ->searchable()
                    ->native(false),
                SelectFilter::make('is_correct')
                    ->label('Benar/Salah')
                    ->options([
                        '1' => 'Benar',
                        '0' => 'Salah',
                    ])
                    ->native(false),
                Filter::make('completed_candidates')
                    ->label('Hanya tes selesai')
                    ->query(fn (Builder $query): Builder => $query->whereHas(
                        'registration',
                        fn (Builder $query): Builder => $query->whereNotNull('test_completed_at'),
                    )),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRegistrationAnswers::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return self::currentUser()?->isAdmin() ?? false;
    }

    public static function canView(Model $record): bool
    {
        return self::currentUser()?->isAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    private static function currentUser(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }
}
