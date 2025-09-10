<?php

namespace App\Filament\Resources\Trips\Pages;

use App\Filament\Resources\Trips\TripResource;
use App\Models\Driver;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTrip extends CreateRecord
{
    protected static string $resource = TripResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data['status'] = 'scheduled';

        $created = $this->getModel()::create($data);

        Notification::make()
            ->title('New Trip')
            ->icon('heroicon-o-check-circle')
            ->body('A new trip trip has been assigned to you.')
            ->sendToDatabase(Driver::find($data['driver_id'])->user);

        return $created;
    }
}
