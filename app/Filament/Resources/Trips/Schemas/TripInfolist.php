<?php

namespace App\Filament\Resources\Trips\Schemas;

use App\Filament\Infolists\Components\LocationEntry;
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
                                    'cancelled' => 'danger',
                                }
                            )
                            ->formatStateUsing(fn (string $state) => ucfirst($state)),
                        TextEntry::make('scheduled_start_time')
                            ->dateTime('F j, Y, g:i A'),
                        TextEntry::make('scheduled_end_time')
                            ->dateTime('F j, Y, g:i A'),
                        TextEntry::make('company.name')
                            ->label('Company'),
                        TextEntry::make('driver.user.name')
                            ->label('Driver'),
                        TextEntry::make('vehicle.make')
                            ->label('Vehicle'),
                        LocationEntry::make('start_location')
                            ->label('Location')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
