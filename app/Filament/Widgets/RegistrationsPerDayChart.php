<?php

namespace App\Filament\Widgets;

use App\Models\Registration;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RegistrationsPerDayChart extends ChartWidget
{
    protected ?string $heading = 'Tren Pendaftaran (14 Hari Terakhir)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $days = collect(range(13, 0))->map(fn (int $i): string => now()->subDays($i)->format('Y-m-d'));

        $counts = Registration::query()
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->get()
            ->groupBy(fn (Registration $registration): string => $registration->created_at->format('Y-m-d'))
            ->map(fn ($group): int => $group->count());

        $labels = [];
        $data = [];

        foreach ($days as $day) {
            $labels[] = Carbon::parse($day)->translatedFormat('d M');
            $data[] = $counts->get($day, 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pendaftar',
                    'data' => $data,
                    'fill' => 'start',
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
