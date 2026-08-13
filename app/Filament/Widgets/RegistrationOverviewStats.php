<?php

namespace App\Filament\Widgets;

use App\Enums\RegistrationStatus;
use App\Models\Registration;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class RegistrationOverviewStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $total = Registration::query()->count();
        $today = Registration::query()->whereBetween('created_at', [
            Carbon::today(),
            Carbon::tomorrow(),
        ])->count();

        $pendingAndReview = Registration::query()
            ->whereIn('status', [RegistrationStatus::Pending, RegistrationStatus::Review])
            ->count();

        $accepted = Registration::query()
            ->where('status', RegistrationStatus::Accepted)
            ->count();

        $rejected = Registration::query()
            ->where('status', RegistrationStatus::Rejected)
            ->count();

        return [
            Stat::make('Total Pendaftar', number_format($total))
                ->description('Keseluruhan pendaftaran')
                ->descriptionIcon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Pendaftar Hari Ini', number_format($today))
                ->description('Masuk hari ini')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('info'),

            Stat::make('Menunggu Review', number_format($pendingAndReview))
                ->description('Status pending & review')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Diterima', number_format($accepted))
                ->description('Lolos seleksi')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Ditolak', number_format($rejected))
                ->description('Tidak lolos')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }
}
