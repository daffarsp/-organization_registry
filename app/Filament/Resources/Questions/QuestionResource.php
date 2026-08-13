<?php

namespace App\Filament\Resources\Questions;

use App\Filament\Resources\Questions\Pages\CreateQuestion;
use App\Filament\Resources\Questions\Pages\EditQuestion;
use App\Filament\Resources\Questions\Pages\ListQuestions;
use App\Filament\Resources\Questions\Schemas\QuestionForm;
use App\Filament\Resources\Questions\Tables\QuestionsTable;
use App\Models\Question;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Divisi';

    protected static ?string $modelLabel = 'Soal Divisi';

    protected static ?string $pluralModelLabel = 'Soal Divisi';

    protected static ?int $navigationSort = 2;

    public static function schema(Schema $schema): Schema
    {
        return QuestionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuestionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuestions::route('/'),
            'create' => CreateQuestion::route('/create'),
            'edit' => EditQuestion::route('/{record}/edit'),
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
