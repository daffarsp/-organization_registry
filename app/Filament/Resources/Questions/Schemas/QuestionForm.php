<?php

namespace App\Filament\Resources\Questions\Schemas;

use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Soal')
                    ->schema([
                        Select::make('division_id')
                            ->label('Divisi')
                            ->relationship('division', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false),
                        TextInput::make('points')
                            ->label('Poin Soal')
                            ->numeric()
                            ->default(20)
                            ->required(),
                        Textarea::make('question_text')
                            ->label('Pertanyaan / Soal')
                            ->required()
                            ->rows(3)
                            ->maxLength(2000)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Pilihan Jawaban (A / B / C / D)')
                    ->schema([
                        TextInput::make('option_a')
                            ->label('Pilihan A')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('option_b')
                            ->label('Pilihan B')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('option_c')
                            ->label('Pilihan C')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('option_d')
                            ->label('Pilihan D')
                            ->required()
                            ->maxLength(255),
                        Radio::make('correct_option')
                            ->label('Kunci Jawaban Benar')
                            ->options([
                                'a' => 'A',
                                'b' => 'B',
                                'c' => 'C',
                                'd' => 'D',
                            ])
                            ->required()
                            ->inline()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
