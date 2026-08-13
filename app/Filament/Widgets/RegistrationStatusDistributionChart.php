<?php

namespace App\Filament\Widgets;

use App\Enums\RegistrationStatus;
use App\Models\Registration;
use Filament\Widgets\ChartWidget;

class RegistrationStatusDistributionChart extends ChartWidget
{
    protected ?string $heading = 'Distribusi Status Pendaftaran';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $counts = Registration::query()
            ->get()
            ->groupBy(fn (Registration $registration): string => $registration->status->value)
            ->map(fn ($group): int => $group->count());

        $labels = [];
        $data = [];
        $colors = [
            RegistrationStatus::Pending->value => '#9ca3af',
            RegistrationStatus::Review->value => '#f59e0b',
            RegistrationStatus::Accepted->value => '#10b981',
            RegistrationStatus::Rejected->value => '#ef4444',
        ];

        $backgroundColors = [];

        foreach (RegistrationStatus::cases() as $status) {
            $labels[] = $status->getLabel();
            $data[] = $counts->get($status->value, 0);
            $backgroundColors[] = $colors[$status->value];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Status Pendaftar',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
