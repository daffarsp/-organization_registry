<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $modelLabel = 'Akun Panel';

    protected static ?string $pluralModelLabel = 'Akun Panel';

    protected static string|UnitEnum|null $navigationGroup = 'Superadmin';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return self::currentUser()?->isSuperAdmin() ?? false;
    }

    public static function canView(Model $record): bool
    {
        return self::currentUser()?->isSuperAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return self::currentUser()?->isSuperAdmin() ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return self::currentUser()?->isSuperAdmin() ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return self::currentUser()?->isSuperAdmin() && auth()->id() !== $record->getKey();
    }

    public static function canDeleteAny(): bool
    {
        return self::currentUser()?->isSuperAdmin() ?? false;
    }

    private static function currentUser(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }
}
