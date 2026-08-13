<?php

namespace App\Filament\Resources\ScreeningQuestions;

use App\Filament\Resources\ScreeningQuestions\Pages\CreateScreeningQuestion;
use App\Filament\Resources\ScreeningQuestions\Pages\EditScreeningQuestion;
use App\Filament\Resources\ScreeningQuestions\Pages\ListScreeningQuestions;
use App\Filament\Resources\ScreeningQuestions\Schemas\ScreeningQuestionForm;
use App\Filament\Resources\ScreeningQuestions\Tables\ScreeningQuestionsTable;
use App\Models\ScreeningQuestion;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class ScreeningQuestionResource extends Resource
{
    protected static ?string $model = ScreeningQuestion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static ?string $modelLabel = 'Pertanyaan Dasar';

    protected static ?string $pluralModelLabel = 'Pertanyaan Dasar';

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Divisi';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return ScreeningQuestionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ScreeningQuestionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListScreeningQuestions::route('/'),
            'create' => CreateScreeningQuestion::route('/create'),
            'edit' => EditScreeningQuestion::route('/{record}/edit'),
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
        return self::currentUser()?->canManageQuestions() ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return self::currentUser()?->canManageQuestions() ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return self::currentUser()?->canManageQuestions() ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return self::currentUser()?->canManageQuestions() ?? false;
    }

    private static function currentUser(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }
}
