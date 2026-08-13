<?php

namespace App\Filament\Resources\Registrations\Schemas;

use App\Enums\RegistrationStatus;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RegistrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Pendaftar')
                    ->schema([
                        TextInput::make('registration_number')
                            ->label('Nomor Pendaftaran')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Dibuat otomatis'),
                        Select::make('status')
                            ->label('Status')
                            ->options(RegistrationStatus::options())
                            ->default(RegistrationStatus::Pending->value)
                            ->required()
                            ->disabled(fn (): bool => ! (self::currentUser()?->canApproveRegistrations() ?? false))
                            ->dehydrated(fn (): bool => self::currentUser()?->canApproveRegistrations() ?? false)
                            ->native(false),
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(120),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Nomor WhatsApp')
                            ->tel()
                            ->required()
                            ->maxLength(30),
                        Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options([
                                'male' => 'Laki-laki',
                                'female' => 'Perempuan',
                                'other' => 'Lainnya',
                            ])
                            ->native(false),
                        DatePicker::make('birth_date')
                            ->label('Tanggal Lahir')
                            ->native(false)
                            ->maxDate(now()),
                        TextInput::make('school')
                            ->label('Asal Sekolah/Kampus')
                            ->maxLength(150),
                        TextInput::make('instagram')
                            ->label('Instagram')
                            ->prefix('@')
                            ->maxLength(100),
                    ])
                    ->columns(2),
                Section::make('Pilihan dan Motivasi')
                    ->schema([
                        Select::make('division_id')
                            ->label('Divisi')
                            ->relationship('division', 'name', fn ($query) => $query->where('is_active', true)->orderBy('name'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),
                        Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                        Textarea::make('reason')
                            ->label('Alasan Bergabung')
                            ->required()
                            ->rows(5)
                            ->maxLength(2000)
                            ->columnSpanFull(),
                        Textarea::make('organization_experience')
                            ->label('Pengalaman Organisasi')
                            ->rows(4)
                            ->maxLength(2000)
                            ->columnSpanFull(),
                        Textarea::make('basic_question_answer')
                            ->label('Jawaban Pertanyaan Dasar')
                            ->disabled()
                            ->dehydrated(false)
                            ->rows(4)
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->label('Catatan Admin')
                            ->rows(4)
                            ->maxLength(2000)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    private static function currentUser(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }
}
