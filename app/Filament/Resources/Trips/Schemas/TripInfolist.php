<?php

namespace App\Filament\Resources\Trips\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TripInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('status')
                            ->badge()
                            ->color(
                                fn (string $state): string => match ($state) {
                                    'scheduled' => 'warning',
                                    'in_progress' => 'info',
                                    'completed' => 'success',
                                }
                            )
                            ->formatStateUsing(fn (string $state) => ucfirst($state)),
                        TextEntry::make('start_location'),
                        TextEntry::make('scheduled_start_time')
                            ->dateTime(),
                        TextEntry::make('company.name')
                            ->label('Company'),
                        TextEntry::make('driver.id')
                            ->label('Driver'),
                        TextEntry::make('vehicle.id')
                            ->label('Vehicle'),
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
