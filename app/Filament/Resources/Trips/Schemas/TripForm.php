<?php

namespace App\Filament\Resources\Trips\Schemas;

use App\Filament\Forms\Components\LocationPicker;
use App\Models\Trip;
use App\Rules\DoesNotOverlap;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class TripForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->columnspan(3)
                    ->schema([
                        Section::make()
                            ->schema([
                                DateTimePicker::make('scheduled_start_time')
                                    ->rules(['after_or_equal:now'])
                                    ->seconds(false)
                                    ->live(onBlur: true)
                                    ->required(),
                                DateTimePicker::make('scheduled_end_time')
                                    ->rules(['after_or_equal:now'])
                                    ->seconds(false)
                                    ->live(onBlur: true)
                                    ->required(),
                                Select::make('status')
                                    ->hiddenOn('create')
                                    ->options([
                                        'scheduled' => 'Scheduled',
                                        'in_progress' => 'In Progress',
                                        'completed' => 'Completed',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->required(),
                                LocationPicker::make('start_location')
                                    ->label('Location')
                                    ->default([])
                                    ->columnSpanFull()
                                    ->required(),
                            ])
                            ->columns(2),
                    ]),

                Group::make()
                    ->columnspan(1)
                    ->schema([
                        Section::make()
                            ->schema([
                                Select::make('company_id')
                                    ->relationship('company', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->required(),
                                Select::make('driver_id')
                                    ->label('Driver')
                                    ->options(function (callable $get) {
                                        $companyId = $get('company_id');

                                        if (! $companyId) {
                                            return [];
                                        }

                                        return \App\Models\Driver::where('company_id', $companyId)
                                            ->with('user')
                                            ->get()
                                            ->pluck('user.name', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->rules([
                                        fn (Get $get, ?Trip $record): DoesNotOverlap => new DoesNotOverlap(
                                            startTime: $get('scheduled_start_time'),
                                            endTime: $get('scheduled_end_time'),
                                            modelType: 'driver',
                                            ignoreTripId: $record?->id
                                        ),
                                    ]),

                                Select::make('vehicle_id')
                                    ->label('Vehicle')
                                    ->options(function (callable $get) {
                                        $companyId = $get('company_id');

                                        if (! $companyId) {
                                            return [];
                                        }

                                        return \App\Models\Vehicle::where('company_id', $companyId)
                                            ->get()
                                            ->pluck('make', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->rules([
                                        fn (Get $get, ?Trip $record): DoesNotOverlap => new DoesNotOverlap(
                                            startTime: $get('scheduled_start_time'),
                                            endTime: $get('scheduled_end_time'),
                                            modelType: 'vehicle',
                                            ignoreTripId: $record?->id
                                        ),
                                    ]),
                                // Select::make('company_id')
                                //     ->relationship('company', 'name')
                                //     ->preload()
                                //     ->searchable()
                                //     ->reactive()
                                //     ->required(),
                                // Select::make('driver_id')
                                //     ->label('Driver')
                                // ->options(function (callable $get) {
                                //     $companyId = $get('company_id');

                                //     if (! $companyId) {
                                //         return [];
                                //     }

                                //     return \App\Models\Driver::where('company_id', $companyId)
                                //         ->with('user')
                                //         ->get()
                                //         ->pluck('user.name', 'id');
                                // })
                                //     ->required()
                                //     ->searchable()
                                //     ->preload()
                                //     ->reactive(),
                                // Select::make('vehicle_id')
                                //     ->label('Vehicle')
                                // ->options(function (callable $get) {
                                //     $companyId = $get('company_id');

                                //     if (! $companyId) {
                                //         return [];
                                //     }

                                //     return \App\Models\Vehicle::where('company_id', $companyId)
                                //         ->get()
                                //         ->pluck('make', 'id');
                                // })
                                //     ->required()
                                //     ->searchable()
                                //     ->preload()
                                //     ->reactive(),
                            ])
                            ->columns(1),
                    ]),
            ])
            ->columns(4);
    }
}
