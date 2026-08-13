<?php

namespace App\Filament\Resources\AdminActivityLogs;

use App\Filament\Resources\AdminActivityLogs\Pages\ListAdminActivityLogs;
use App\Models\AdminActivityLog;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class AdminActivityLogResource extends Resource
{
    protected static ?string $model = AdminActivityLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?string $modelLabel = 'Log Admin';

    protected static ?string $pluralModelLabel = 'Log Admin';

    protected static string|UnitEnum|null $navigationGroup = 'Superadmin';

    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i:s')
                    ->sortable(),
                TextColumn::make('actor_name')
                    ->label('Admin')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('actor_email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('action')
                    ->label('Aksi')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::actionLabel($state)),
                TextColumn::make('subject_label')
                    ->label('Objek')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->searchable()
                    ->limit(90),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->label('Aksi')
                    ->options([
                        'created' => 'Membuat',
                        'updated' => 'Memperbarui',
                        'deleted' => 'Menghapus',
                        'question_imported' => 'Import Soal Word',
                    ])
                    ->native(false),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdminActivityLogs::route('/'),
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

    public static function actionLabel(string $action): string
    {
        return match ($action) {
            'created' => 'Membuat',
            'updated' => 'Memperbarui',
            'deleted' => 'Menghapus',
            'question_imported' => 'Import Soal Word',
            default => str($action)->replace('_', ' ')->title()->toString(),
        };
    }

    private static function currentUser(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }
}
