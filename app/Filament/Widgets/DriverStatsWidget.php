<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class DriverStatsWidget extends BaseWidget
{
    protected ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        $driver = Auth::user()->driver()->withCount([
            'trips as todays_trips_count' => function ($query) {
                $query->whereDate('scheduled_start_time', today());
            },
            'trips as completed_trips_count' => function ($query) {
                $query->where('status', 'completed');
            },
        ])->first();

        if (! $driver) {
            return [];
        }

        $currentVehicle = $driver->trips()->where('status', 'in_progress')->first()?->vehicle->license_plate ?? 'None';

        return [
            Stat::make('Today\'s Trips', $driver->todays_trips_count)
                ->icon('heroicon-m-calendar'),
            Stat::make('Total Completed Trips', $driver->completed_trips_count)
                ->icon('heroicon-m-check-circle'),
            Stat::make('Vehicle Assigned', $currentVehicle)
                ->icon('heroicon-m-truck'),
        ];
    }
}
