<?php

namespace App\Filament\Pages;

use App\Models\Driver;
use App\Models\Trip;
use App\Models\Vehicle;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class Availability extends Page
{
    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.availability';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Clock;

    public ?array $data = [];

    public Collection $availableDrivers;

    public Collection $availableVehicles;

    public function mount(): void
    {
        $this->form->fill();
        $this->availableDrivers = collect();
        $this->availableVehicles = collect();
    }

    public static function canAccess(): bool
    {
        return Auth::user()->isAdmin();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DateTimePicker::make('start_time')
                    ->label('Start Time')
                    ->default(now()->startOfDay())
                    ->seconds(false)
                    ->live(onBlur: true)
                    ->required(),
                DateTimePicker::make('end_time')
                    ->label('End Time')
                    ->default(now()->endOfDay())
                    ->seconds(false)
                    ->live(onBlur: true)
                    ->required(),
            ])
            ->statePath('data')
            ->columns(2);
    }

    public function find()
    {
        $data = $this->form->getState();
        $start = $data['start_time'] ?? null;
        $end = $data['end_time'] ?? null;

        if (! $start || ! $end) {
            $this->availableDrivers = collect();
            $this->availableVehicles = collect();

            return;
        }

        $conflictingTrips = Trip::whereIn('status', ['scheduled', 'in_progress'])
            ->where(
                fn ($q) => $q->where('scheduled_start_time', '<', $end)
                    ->where('scheduled_end_time', '>', $start)
            )
            ->select('driver_id', 'vehicle_id')
            ->get();

        $bookedDriverIds = $conflictingTrips->pluck('driver_id')->unique()->filter();
        $bookedVehicleIds = $conflictingTrips->pluck('vehicle_id')->unique()->filter();

        $this->availableDrivers = Driver::whereNotIn('id', $bookedDriverIds)->get();
        $this->availableVehicles = Vehicle::whereNotIn('id', $bookedVehicleIds)->get();
    }

    public function clear()
    {
        $this->form->fill();
        $this->availableDrivers = collect();
        $this->availableVehicles = collect();
    }
}
