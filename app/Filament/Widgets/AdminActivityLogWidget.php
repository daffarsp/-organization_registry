<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\AdminActivityLogs\AdminActivityLogResource;
use App\Models\AdminActivityLog;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class AdminActivityLogWidget extends TableWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->isSuperAdmin();
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Log Admin Terbaru')
            ->query(AdminActivityLog::query()->latest('created_at'))
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('actor_name')
                    ->label('Admin')
                    ->searchable(),
                TextColumn::make('action')
                    ->label('Aksi')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => AdminActivityLogResource::actionLabel($state)),
                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(100),
            ]);
    }
}
