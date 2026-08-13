<?php

namespace App\Filament\Resources\Registrations\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RegistrationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ringkasan')
                    ->schema([
                        TextEntry::make('registration_number')
                            ->label('Nomor Pendaftaran')
                            ->copyable(),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge(),
                        TextEntry::make('division.name')
                            ->label('Divisi'),
                        TextEntry::make('score')
                            ->label('Skor Tes Divisi')
                            ->badge()
                            ->color(fn (?int $state): string => match (true) {
                                $state >= 70 => 'success',
                                $state >= 50 => 'warning',
                                $state !== null => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (?int $state): string => $state !== null ? "{$state} / 100" : 'Belum Melakukan Tes'),
                        TextEntry::make('created_at')
                            ->label('Tanggal Daftar')
                            ->dateTime('d M Y H:i'),
                        TextEntry::make('test_completed_at')
                            ->label('Waktu Selesai Tes')
                            ->dateTime('d M Y H:i')
                            ->placeholder('Belum Tes'),
                        TextEntry::make('basic_completed_at')
                            ->label('Waktu Jawab Dasar')
                            ->dateTime('d M Y H:i')
                            ->placeholder('Belum Dijawab'),
                    ])
                    ->columns(2),
                Section::make('Data Diri')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama Lengkap'),
                        TextEntry::make('email')
                            ->label('Email')
                            ->copyable(),
                        TextEntry::make('phone')
                            ->label('Nomor WhatsApp')
                            ->copyable(),
                        TextEntry::make('gender')
                            ->label('Jenis Kelamin')
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'male' => 'Laki-laki',
                                'female' => 'Perempuan',
                                'other' => 'Lainnya',
                                default => '-',
                            }),
                        TextEntry::make('birth_date')
                            ->label('Tanggal Lahir')
                            ->date('d M Y')
                            ->placeholder('-'),
                        TextEntry::make('school')
                            ->label('Asal Sekolah/Kampus')
                            ->placeholder('-'),
                        TextEntry::make('instagram')
                            ->label('Instagram')
                            ->prefix('@')
                            ->placeholder('-'),
                        TextEntry::make('address')
                            ->label('Alamat')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Motivasi dan Review')
                    ->schema([
                        TextEntry::make('reason')
                            ->label('Alasan Bergabung')
                            ->columnSpanFull(),
                        TextEntry::make('organization_experience')
                            ->label('Pengalaman Organisasi')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('basic_question_answer')
                            ->label('Jawaban Pertanyaan Dasar')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('notes')
                            ->label('Catatan Admin')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ]),
                Section::make('Jawaban Seleksi')
                    ->schema([
                        RepeatableEntry::make('screeningAnswers')
                            ->label('Jawaban Pertanyaan Dasar')
                            ->schema([
                                TextEntry::make('screeningQuestion.question_text')
                                    ->label('Pertanyaan')
                                    ->columnSpanFull(),
                                TextEntry::make('answer')
                                    ->label('Jawaban')
                                    ->columnSpanFull(),
                            ])
                            ->columns(1)
                            ->columnSpanFull(),
                        RepeatableEntry::make('answers')
                            ->label('Jawaban Pilihan Ganda')
                            ->schema([
                                TextEntry::make('question.question_text')
                                    ->label('Soal')
                                    ->columnSpanFull(),
                                TextEntry::make('selected_option')
                                    ->label('Jawaban')
                                    ->badge()
                                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),
                                TextEntry::make('question.correct_option')
                                    ->label('Kunci')
                                    ->badge()
                                    ->color('success')
                                    ->formatStateUsing(fn (?string $state): string => $state ? strtoupper($state) : '-'),
                                TextEntry::make('is_correct')
                                    ->label('Hasil')
                                    ->badge()
                                    ->formatStateUsing(fn (bool $state): string => $state ? 'Benar' : 'Salah')
                                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                                TextEntry::make('score_earned')
                                    ->label('Skor'),
                            ])
                            ->columns(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
