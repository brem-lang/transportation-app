<?php

namespace App\Filament\Pages;

use App\Models\Trip;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class MyTrips extends Page implements HasSchemas, HasTable
{
    use InteractsWithSchemas, InteractsWithTable;

    protected string $view = 'filament.pages.my-trips';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Clock;

    public $activePage = 'list';

    public $selectedTrip;

    public function mount()
    {
        $this->selectedTrip = null;
    }

    public static function canAccess(): bool
    {
        return Auth::user()->isDriver();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cancel')
                ->visible(fn () => $this->activePage === 'view')
                ->icon('heroicon-o-x-circle')
                ->label('Cancel')
                ->action(function () {
                    $this->selectedTrip;
                    $this->activePage = 'list';
                }),
        ];
    }

    public function productInfolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->selectedTrip)
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
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Trip::query()->where('driver_id', Auth::user()->driver->id))
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
                    ->sortable(),
                TextColumn::make('scheduled_end_time')
                    ->dateTime('F j, Y, g:i A')
                    ->sortable(),
            ])
            ->recordActions(
                [
                    Action::make('view')
                        ->icon('heroicon-o-eye')
                        ->label('View')->action(function ($record) {
                            $this->selectedTrip = $record;
                            $this->activePage = 'view';
                        }),
                ]
            );
    }
}
