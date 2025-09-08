<?php

namespace App\Filament\Resources\Vehicles\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VehicleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('make')
                            ->required(),
                        TextInput::make('model')
                            ->required(),
                        TextInput::make('license_plate')
                            ->required(),
                        Select::make('company_id')
                            ->relationship('company', 'name')
                            ->required(),
                    ]),
            ]);
    }
}
