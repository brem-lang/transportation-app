<?php

namespace App\Filament\Resources\Trips\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TripForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('status')
                            ->required(),
                        TextInput::make('start_location')
                            ->required(),
                        DateTimePicker::make('scheduled_start_time')
                            ->required(),
                        Select::make('company_id')
                            ->relationship('company', 'name')
                            ->required(),
                        Select::make('driver_id')
                            ->relationship('driver.user', 'name')
                            ->required(),
                        Select::make('vehicle_id')
                            ->relationship('vehicle', 'make')
                            ->required(),
                    ]),
            ]);
    }
}
