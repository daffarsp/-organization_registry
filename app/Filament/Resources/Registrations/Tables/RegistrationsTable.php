<?php

namespace App\Filament\Resources\Registrations\Tables;

use App\Enums\RegistrationStatus;
use App\Models\Registration;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class RegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('registration_number')
                    ->label('Nomor')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('phone')
                    ->label('WhatsApp')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('division.name')
                    ->label('Divisi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('score')
                    ->label('Skor Tes')
                    ->badge()
                    ->sortable()
                    ->color(fn (?int $state): string => match (true) {
                        $state >= 70 => 'success',
                        $state >= 50 => 'warning',
                        $state !== null => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?int $state): string => $state !== null ? "{$state} / 100" : 'Belum Tes'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Tanggal Daftar')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(RegistrationStatus::options())
                    ->native(false),
                SelectFilter::make('division_id')
                    ->label('Divisi')
                    ->relationship('division', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),
                Filter::make('created_at')
                    ->label('Tanggal daftar')
                    ->schema([
                        DatePicker::make('created_from')
                            ->label('Dari tanggal')
                            ->native(false),
                        DatePicker::make('created_until')
                            ->label('Sampai tanggal')
                            ->native(false),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when(
                            $data['created_from'] ?? null,
                            fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['created_until'] ?? null,
                            fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '<=', $date),
                        )),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('changeStatus')
                    ->label('Ubah Status')
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn (): bool => self::currentUser()?->canApproveRegistrations() ?? false)
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options(RegistrationStatus::options())
                            ->required()
                            ->native(false),
                        Textarea::make('notes')
                            ->label('Catatan Admin')
                            ->rows(4)
                            ->maxLength(2000),
                    ])
                    ->fillForm(fn (Registration $record): array => [
                        'status' => $record->status->value,
                        'notes' => $record->notes,
                    ])
                    ->action(fn (Registration $record, array $data): bool => $record->update([
                        'status' => $data['status'],
                        'notes' => $data['notes'] ?? $record->notes,
                    ])),
                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('exportCsv')
                        ->label('Export CSV Pilihan')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(fn (Collection $records) => static::downloadCsv($records)),
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => self::currentUser()?->canApproveRegistrations() ?? false)
                        ->requiresConfirmation(),
                ]),
            ])
            ->headerActions([
                Action::make('exportAllCsv')
                    ->label('Export Semua CSV')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(fn () => static::downloadCsv(Registration::query()->with('division')->latest('created_at')->get())),
            ])
            ->defaultSort('created_at', 'desc');
    }

    /**
     * @param Collection<int, Registration> $records
     */
    protected static function downloadCsv(Collection $records)
    {
        $csvHeader = [
            'Nomor Pendaftaran',
            'Nama Lengkap',
            'Email',
            'WhatsApp',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'Asal Sekolah/Kampus',
            'Alamat',
            'Divisi',
            'Alasan Bergabung',
            'Pengalaman Organisasi',
            'Jawaban Pertanyaan Dasar',
            'Instagram',
            'Skor Tes',
            'Waktu Selesai Tes',
            'Status',
            'Catatan Admin',
            'Tanggal Daftar',
        ];

        $callback = function () use ($records, $csvHeader): void {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM
            fputcsv($file, $csvHeader);

            foreach ($records as $record) {
                fputcsv($file, [
                    $record->registration_number,
                    $record->name,
                    $record->email,
                    $record->phone,
                    match ($record->gender) {
                        'male' => 'Laki-laki',
                        'female' => 'Perempuan',
                        'other' => 'Lainnya',
                        default => '-',
                    },
                    $record->birth_date ? $record->birth_date->format('Y-m-d') : '-',
                    $record->school ?? '-',
                    $record->address ?? '-',
                    $record->division?->name ?? '-',
                    $record->reason,
                    $record->organization_experience ?? '-',
                    $record->basic_question_answer ?? '-',
                    $record->instagram ? '@'.$record->instagram : '-',
                    $record->score !== null ? $record->score : '-',
                    $record->test_completed_at ? $record->test_completed_at->format('Y-m-d H:i:s') : '-',
                    $record->status?->getLabel() ?? $record->status,
                    $record->notes ?? '-',
                    $record->created_at ? $record->created_at->format('Y-m-d H:i:s') : '-',
                ]);
            }

            fclose($file);
        };

        return response()->streamDownload(
            $callback,
            'pendaftar-'.now()->format('Y-m-d-His').'.csv',
            ['Content-Type' => 'text/csv; charset=UTF-8']
        );
    }

    private static function currentUser(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }
}
