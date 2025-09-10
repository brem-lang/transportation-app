<?php

namespace App\Filament\Resources\Companies\RelationManagers;

use App\Models\Driver;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DriversRelationManager extends RelationManager
{
    protected static string $relationship = 'drivers';

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->action(function (RelationManager $livewire, Schema $schema): void {
                        try {
                            DB::beginTransaction();

                            $user = User::create([
                                'name' => $schema->getRawState()['name'],
                                'email' => $schema->getRawState()['email'],
                                'license_number' => $schema->getRawState()['license_number'],
                                'password' => Hash::make('password'),
                                'role' => 'driver',
                            ]);

                            Driver::create([
                                'company_id' => $livewire->getOwnerRecord()['id'],
                                'user_id' => $user->id,
                            ]);

                            DB::commit();
                        } catch (\Exception $e) {
                            DB::rollBack();
                            logger($e->getMessage());
                        }
                    })
                    ->createAnother(false),
            ])
            ->columns([
                TextColumn::make('user.name')->label('Name')->searchable()->weight(FontWeight::Bold)->sortable(),
                TextColumn::make('user.email')->label('Email')->searchable()->sortable(),
                TextColumn::make('user.license_number')->label('License Number')->searchable()->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->mutateRecordDataUsing(function (array $data, $record) {
                        if (isset($data['user'])) {
                            $record->user->update($data['user']);
                        }
                        unset($data['user']);

                        return $data;
                    })
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->formatStateUsing(fn ($record) => $record?->user?->name)
                            ->required(),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->formatStateUsing(fn ($record) => $record?->user?->email)
                            ->required(),
                        TextInput::make('license_number')
                            ->label('License Number')
                            ->formatStateUsing(fn ($record) => $record?->user?->license_number)
                            ->required(),
                    ])
                    ->action(function ($record, Schema $schema): void {
                        $record->user()->update([
                            'name' => $schema->getRawState()['name'],
                            'email' => $schema->getRawState()['email'],
                            'license_number' => $schema->getRawState()['license_number'],
                        ]);
                    }),
                DeleteAction::make()
                    ->action(function ($record): void {
                        $record->user->delete();
                        $record->delete();
                    }),
            ])
            ->modifyQueryUsing(function ($query) {
                return $query->orderBy('created_at', 'desc');
            });
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name'),
                TextEntry::make('user.email'),
                TextEntry::make('user.license_number'),
                TextEntry::make('created_at')
                    ->dateTime('M d, Y H:i')
                    ->placeholder('-'),
                TextEntry::make('user.role')
                    ->badge(),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->required()
                    ->email()
                    ->maxLength(255),
                TextInput::make('license_number')
                    ->required()
                    ->maxLength(255),
            ])
            ->columns(1);
    }
}
