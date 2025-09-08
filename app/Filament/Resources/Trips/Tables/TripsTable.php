<?php

namespace App\Filament\Resources\Trips\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TripsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')
                    ->badge()
                    ->color(
                        fn (string $state): string => match ($state) {
                            'scheduled' => 'warning',
                            'in_progress' => 'info',
                            'completed' => 'success',
                        }
                    )
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->searchable(),
                TextColumn::make('start_location')
                    ->searchable(),
                TextColumn::make('scheduled_start_time')
                    ->dateTime('F j, Y, g:i A')
                    ->sortable(),
                TextColumn::make('company.name')
                    ->searchable(),
                TextColumn::make('driver.user.name')
                    ->label('Driver')
                    ->searchable(),
                TextColumn::make('vehicle.make')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
