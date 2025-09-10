<?php

namespace App\Filament\Widgets;

use App\Models\Driver;
use App\Models\Trip;
use App\Models\Vehicle;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{
    protected ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        return [
            Stat::make(
                'Active Trips Right Now',
                Trip::where('status', 'in_progress')->count()
            )
                ->description('Live count of ongoing trips')
                ->descriptionIcon('heroicon-m-truck')
                ->color('warning'),

            Stat::make(
                'Drivers & Vehicles Available',
                function () {
                    $activeDriverIds = Trip::where('status', 'in_progress')->pluck('driver_id');
                    $activeVehicleIds = Trip::where('status', 'in_progress')->pluck('vehicle_id');

                    $availableDrivers = Driver::whereNotIn('id', $activeDriverIds)->count();
                    $availableVehicles = Vehicle::whereNotIn('id', $activeVehicleIds)->count();

                    return "{$availableDrivers} / {$availableVehicles}";
                }
            )
                ->description('Available Drivers / Vehicles')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make(
                'Trips Completed This Month',
                Trip::where('status', 'completed')
                    ->whereBetween('scheduled_end_time', [now()->startOfMonth(), now()->endOfMonth()])
                    ->count()
            )
                ->description('Total trips finished this month')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('primary'),
        ];
    }
}
