<?php

namespace App\Filament\Resources\Trips\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
                            'cancelled' => 'danger',
                        }
                    )
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->searchable(),
                TextColumn::make('scheduled_start_time')
                    ->dateTime('F j, Y, g:i A')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                TextColumn::make('scheduled_end_time')
                    ->dateTime('F j, Y, g:i A')
                    ->toggleable(isToggledHiddenByDefault: true)
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
                SelectFilter::make('company')
                    ->searchable()
                    ->preload()
                    ->relationship('company', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('update_status')
                    ->label('Status')
                    ->icon('heroicon-o-pencil-square')
                    ->modalWidth('md')
                    ->schema([
                        Select::make('status')
                            ->hiddenOn('create')
                            ->options([
                                'scheduled' => 'Scheduled',
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required(),
                    ])
                    ->action(function ($record, array $data): void {
                        $record->update([
                            'status' => $data['status'],
                        ]);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                    BulkAction::make('update_status')
                        ->label('Status')
                        ->icon('heroicon-o-pencil-square')
                        ->modalWidth('md')
                        ->schema([
                            Select::make('status')
                                ->options([
                                    'scheduled' => 'Scheduled',
                                    'in_progress' => 'In Progress',
                                    'completed' => 'Completed',
                                    'cancelled' => 'Cancelled',
                                ])
                                ->required(),
                        ])
                        ->action(function ($records, array $data): void {
                            foreach ($records as $record) {
                                $record->update([
                                    'status' => $data['status'],
                                ]);
                            }
                        }),
                ]),
            ])
            ->modifyQueryUsing(function ($query) {
                return $query->orderBy('created_at', 'desc');
            });
    }
}
