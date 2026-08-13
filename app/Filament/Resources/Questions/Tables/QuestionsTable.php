<?php

namespace App\Filament\Resources\Questions\Tables;

use App\Models\Question;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;

class QuestionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('division.name')
                    ->label('Divisi')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('question_text')
                    ->label('Soal')
                    ->limit(60)
                    ->searchable(),
                TextColumn::make('correct_option')
                    ->label('Kunci')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),
                TextColumn::make('points')
                    ->label('Poin')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('division_id')
                    ->label('Divisi')
                    ->relationship('division', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                \Filament\Tables\Actions\Action::make('importWord')
                    ->label('Import Word')
                    ->icon('heroicon-o-document-arrow-up')
                    ->color('success')
                    ->visible(fn (): bool => self::currentUser()?->canManageQuestions() ?? false)
                    ->form([
                        Select::make('division_id')
                            ->label('Pilih Divisi')
                            ->relationship('division', 'name')
                            ->required(),
                        FileUpload::make('file')
                            ->label('File Word (.docx)')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/msword'])
                            ->disk('local')
                            ->directory('imports')
                            ->required()
                            ->helperText('Format: Tiap soal diawali dengan "Q: [soal]". Pilihan ganda: "A: [pilihan]", "B: [pilihan]", dll. Kunci jawaban: "Ans: [a/b/c/d]". Poin: "Points: [angka]". Kosongkan poin untuk default 20.'),
                    ])
                    ->action(function (array $data) {
                        $filePath = Storage::disk('local')->path($data['file']);
                        
                        try {
                            $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
                            $text = '';
                            foreach ($phpWord->getSections() as $section) {
                                foreach ($section->getElements() as $element) {
                                    if (method_exists($element, 'getText')) {
                                        $text .= $element->getText() . "\n";
                                    } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                                        foreach ($element->getElements() as $e) {
                                            if (method_exists($e, 'getText')) {
                                                $text .= $e->getText();
                                            }
                                        }
                                        $text .= "\n";
                                    }
                                }
                            }
                            
                            $lines = explode("\n", $text);
                            $currentQuestion = null;
                            $questionsToInsert = [];
                            
                            foreach ($lines as $line) {
                                $line = trim($line);
                                if (empty($line)) continue;
                                
                                if (str_starts_with(strtoupper($line), 'Q:')) {
                                    if ($currentQuestion) {
                                        $questionsToInsert[] = $currentQuestion;
                                    }
                                    $currentQuestion = [
                                        'division_id' => $data['division_id'],
                                        'question_text' => trim(substr($line, 2)),
                                        'points' => 20,
                                        'option_a' => '',
                                        'option_b' => '',
                                        'option_c' => '',
                                        'option_d' => '',
                                        'correct_option' => 'a',
                                    ];
                                } elseif ($currentQuestion && str_starts_with(strtoupper($line), 'A:')) {
                                    $currentQuestion['option_a'] = trim(substr($line, 2));
                                } elseif ($currentQuestion && str_starts_with(strtoupper($line), 'B:')) {
                                    $currentQuestion['option_b'] = trim(substr($line, 2));
                                } elseif ($currentQuestion && str_starts_with(strtoupper($line), 'C:')) {
                                    $currentQuestion['option_c'] = trim(substr($line, 2));
                                } elseif ($currentQuestion && str_starts_with(strtoupper($line), 'D:')) {
                                    $currentQuestion['option_d'] = trim(substr($line, 2));
                                } elseif ($currentQuestion && str_starts_with(strtoupper($line), 'ANS:')) {
                                    $currentQuestion['correct_option'] = strtolower(trim(substr($line, 4)));
                                } elseif ($currentQuestion && str_starts_with(strtoupper($line), 'POINTS:')) {
                                    $currentQuestion['points'] = (int) trim(substr($line, 7));
                                }
                            }
                            
                            if ($currentQuestion) {
                                $questionsToInsert[] = $currentQuestion;
                            }
                            
                            $validCount = 0;
                            foreach ($questionsToInsert as $q) {
                                if (!empty($q['question_text']) && !empty($q['option_a']) && !empty($q['option_b'])) {
                                    Question::create($q);
                                    $validCount++;
                                }
                            }
                            
                            Notification::make()
                                ->title('Import Berhasil')
                                ->body("$validCount soal berhasil diimport.")
                                ->success()
                                ->send();
                                
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Import Gagal')
                                ->body('Terjadi kesalahan membaca file Word: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->defaultSort('division_id');
    }
}
