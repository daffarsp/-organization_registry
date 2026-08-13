<?php

namespace App\Services;

use App\Models\Question;
use DOMDocument;
use DOMXPath;
use InvalidArgumentException;
use ZipArchive;

class QuestionWordImporter
{
    /**
     * @return array{created: int, skipped: int}
     */
    public function import(string $path, int $divisionId, int $defaultPoints = 20): array
    {
        $questions = $this->parse($path, $defaultPoints);
        $created = 0;

        foreach ($questions as $question) {
            Question::query()->create($question + [
                'division_id' => $divisionId,
            ]);

            $created++;
        }

        return [
            'created' => $created,
            'skipped' => 0,
        ];
    }

    /**
     * @return array<int, array{question_text: string, option_a: string, option_b: string, option_c: string, option_d: string, correct_option: string, points: int}>
     */
    public function parse(string $path, int $defaultPoints = 20): array
    {
        $lines = $this->readParagraphs($path);
        $questions = [];
        $current = null;
        $lastOption = null;

        foreach ($lines as $line) {
            if (preg_match('/^(?:No\.?\s*)?\d+[\).:-]?\s+(.+)$/iu', $line, $matches)) {
                $this->pushQuestion($questions, $current);
                $current = $this->newQuestion($matches[1], $defaultPoints);
                $lastOption = null;

                continue;
            }

            if (preg_match('/^(?:Pertanyaan|Soal)\s*[:.-]\s*(.*)$/iu', $line, $matches)) {
                $this->pushQuestion($questions, $current);
                $current = $this->newQuestion($matches[1], $defaultPoints);
                $lastOption = null;

                continue;
            }

            if ($current === null) {
                $current = $this->newQuestion($line, $defaultPoints);

                continue;
            }

            if (preg_match('/^([A-D])[\).:-]\s*(.+)$/iu', $line, $matches)) {
                $key = strtolower($matches[1]);
                $current["option_{$key}"] = trim($matches[2]);
                $lastOption = $key;

                continue;
            }

            if (preg_match('/^(?:Jawaban|Kunci(?:\s+Jawaban)?)\s*[:.-]\s*([A-D])/iu', $line, $matches)) {
                $current['correct_option'] = strtolower($matches[1]);
                $lastOption = null;

                continue;
            }

            if (preg_match('/^(?:Poin|Point|Bobot)\s*[:.-]\s*(\d+)/iu', $line, $matches)) {
                $current['points'] = max(1, (int) $matches[1]);
                $lastOption = null;

                continue;
            }

            if ($lastOption !== null && blank($current["option_{$lastOption}"] ?? null)) {
                $current["option_{$lastOption}"] = trim($line);

                continue;
            }

            $current['question_text'] = trim($current['question_text'].' '.$line);
        }

        $this->pushQuestion($questions, $current);

        if ($questions === []) {
            throw new InvalidArgumentException('Tidak ada soal yang bisa dibaca dari dokumen Word.');
        }

        return $questions;
    }

    /**
     * @return array<int, string>
     */
    private function readParagraphs(string $path): array
    {
        $zip = new ZipArchive();

        if ($zip->open($path) !== true) {
            throw new InvalidArgumentException('File Word tidak bisa dibuka.');
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if (! is_string($xml) || $xml === '') {
            throw new InvalidArgumentException('Isi dokumen Word tidak valid.');
        }

        $document = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $loaded = $document->loadXML($xml);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $loaded) {
            throw new InvalidArgumentException('XML dokumen Word tidak bisa dibaca.');
        }

        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        $lines = [];

        foreach ($xpath->query('//w:body/w:p') ?: [] as $paragraph) {
            $text = '';

            foreach ($xpath->query('.//w:t', $paragraph) ?: [] as $node) {
                $text .= $node->textContent;
            }

            $line = trim((string) preg_replace('/\s+/u', ' ', $text));

            if ($line !== '') {
                $lines[] = $line;
            }
        }

        return $lines;
    }

    /**
     * @return array{question_text: string, option_a: string, option_b: string, option_c: string, option_d: string, correct_option: string, points: int}
     */
    private function newQuestion(string $questionText, int $defaultPoints): array
    {
        return [
            'question_text' => trim($questionText),
            'option_a' => '',
            'option_b' => '',
            'option_c' => '',
            'option_d' => '',
            'correct_option' => '',
            'points' => max(1, $defaultPoints),
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $questions
     * @param array<string, mixed>|null $current
     */
    private function pushQuestion(array &$questions, ?array $current): void
    {
        if ($current === null) {
            return;
        }

        $required = ['question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_option'];

        foreach ($required as $field) {
            if (blank($current[$field] ?? null)) {
                throw new InvalidArgumentException('Format soal belum lengkap. Pastikan ada Pertanyaan, A-D, Jawaban, dan opsional Poin.');
            }
        }

        $questions[] = [
            'question_text' => (string) $current['question_text'],
            'option_a' => (string) $current['option_a'],
            'option_b' => (string) $current['option_b'],
            'option_c' => (string) $current['option_c'],
            'option_d' => (string) $current['option_d'],
            'correct_option' => strtolower((string) $current['correct_option']),
            'points' => (int) $current['points'],
        ];
    }
}
