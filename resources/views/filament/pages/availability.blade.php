<x-filament-panels::page>
    <div style="display: flex; justify-content: flex-end; align-items: center; gap: 25px;">
        <div>
            {{ $this->form }}
        </div>
        <div style="margin-top: 31px;">
            <x-filament::button icon="heroicon-o-magnifying-glass" style="width: 100px;" wire:click.prevent="find">
                Search
            </x-filament::button>
            <x-filament::button icon="heroicon-o-x-circle" color="danger" wire:click.prevent="clear">

            </x-filament::button>
        </div>
    </div>

    <div wire:loading.delay.long wire:target="find">
        <div style="display: flex; justify-content: center; align-items: center; gap: 25px; margin-top:10px;">
            <img src="{{ asset('images/undraw_speed-test_wdyh.svg') }}" alt="No Availability" style="width: 600px;" />
            <span class="text-lg font-medium text-gray-500 dark:text-gray-400">Searching for availability...</span>
        </div>
    </div>
    <div wire:loading.remove wire:target="find">
        @php
            $hasDrivers = (bool) $this->availableDrivers?->isNotEmpty();
            $hasVehicles = (bool) $this->availableVehicles?->isNotEmpty();
        @endphp

        @if ($hasDrivers || $hasVehicles)
            <div class="grid">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Available Drivers</h2>
                        <span class="badge badge-blue">{{ $this->availableDrivers->count() }}</span>
                    </div>
                    <div class="list">
                        @forelse ($this->availableDrivers as $driver)
                            <div class="list-item">
                                {{ $driver->user->name ?? 'N/A' }}
                            </div>
                        @empty
                            <p class="empty">No available drivers found in this time range.</p>
                        @endforelse
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Available Vehicles</h2>
                        <span class="badge badge-green">{{ $this->availableVehicles->count() }}</span>
                    </div>
                    <div class="list">
                        @forelse ($this->availableVehicles as $vehicle)
                            <div class="list-item">
                                {{ $vehicle->make }} {{ $vehicle->model }} —
                                <strong>{{ $vehicle->license_plate }}</strong>
                            </div>
                        @empty
                            <p class="empty">No available vehicles found in this time range.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @else
            <div style="display: flex; justify-content: center; align-items: center; gap: 25px; margin-top:10px;">
                <img src="{{ asset('images/undraw_multiple-choice_9n00.svg') }}" alt="No Availability"
                    style="width: 600px;" />
            </div>
        @endif
    </div>

    <style>
        .card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .card.dark {
            background: #1f2937;
            border-color: #374151;
            color: #f9fafb;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .card-title {
            font-size: 16px;
            font-weight: 600;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            border-radius: 9999px;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-blue {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-green {
            background: #dcfce7;
            color: #166534;
        }

        .list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .list-item {
            background: #f9fafb;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 14px;
        }

        .empty {
            font-size: 14px;
            color: #6b7280;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
        }

        @media (min-width: 768px) {
            .grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</x-filament-panels::page>
