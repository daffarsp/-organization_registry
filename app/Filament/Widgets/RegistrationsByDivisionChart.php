<?php

namespace App\Filament\Widgets;

use App\Models\Division;
use Filament\Widgets\ChartWidget;

class RegistrationsByDivisionChart extends ChartWidget
{
    protected ?string $heading = 'Pendaftar Berdasarkan Divisi';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $divisions = Division::query()
            ->withCount('registrations')
            ->orderBy('name')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pendaftar',
                    'data' => $divisions->pluck('registrations_count')->all(),
                    'backgroundColor' => [
                        '#10b981',
                        '#0284c7',
                        '#f59e0b',
                        '#8b5cf6',
                        '#ec4899',
                        '#64748b',
                        '#14b8a6',
                        '#f97316',
                    ],
                ],
            ],
            'labels' => $divisions->pluck('name')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
