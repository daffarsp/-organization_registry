<?php

namespace App\Filament\Resources\RegistrationScreeningAnswers;

use App\Filament\Resources\RegistrationScreeningAnswers\Pages\ListRegistrationScreeningAnswers;
use App\Models\Division;
use App\Models\RegistrationScreeningAnswer;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class RegistrationScreeningAnswerResource extends Resource
{
    protected static ?string $model = RegistrationScreeningAnswer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static ?string $modelLabel = 'Jawaban Dasar';

    protected static ?string $pluralModelLabel = 'Jawaban Dasar';

    protected static string|UnitEnum|null $navigationGroup = 'Pendaftaran';

    protected static ?int $navigationSort = 3;

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
                TextColumn::make('screeningQuestion.question_text')
                    ->label('Pertanyaan')
                    ->limit(70)
                    ->searchable(),
                TextColumn::make('answer')
                    ->label('Jawaban')
                    ->limit(90)
                    ->searchable(),
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
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRegistrationScreeningAnswers::route('/'),
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
