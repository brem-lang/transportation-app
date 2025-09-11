<?php

use App\Models\Company;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Company Model', function () {
    beforeEach(function () {
        $this->company = Company::factory()->create();
    });

    test('a company can be created', function () {
        expect($this->company)->toBeInstanceOf(Company::class)
            ->and($this->company->name)->not->toBeEmpty();
    });

    test('a company can be updated', function () {
        $this->company->update(['name' => 'Updated Company Name']);
        expect($this->company->fresh()->name)->toBe('Updated Company Name');
    });

    test('a company can be deleted', function () {
        $this->company->delete();
        $this->assertModelMissing($this->company);
    });

    test('a company has many drivers and vehicles', function () {
        Driver::factory(2)->for($this->company)->create();
        Vehicle::factory(3)->for($this->company)->create();

        expect($this->company->drivers)->toHaveCount(2)
            ->and($this->company->vehicles)->toHaveCount(3);
    });
});

describe('Driver Model', function () {
    beforeEach(function () {
        $this->driver = Driver::factory()->create();
    });

    test('a driver can be created with its user and company', function () {
        expect($this->driver)->toBeInstanceOf(Driver::class)
            ->and($this->driver->user)->toBeInstanceOf(User::class)
            ->and($this->driver->company)->toBeInstanceOf(Company::class);
    });

    test('a driver can be updated', function () {
        $this->driver->user->update(['license_number' => 'NEW-LIC-123']);
        expect($this->driver->fresh()->user->license_number)->toBe('NEW-LIC-123');
    });

    test('a driver can be deleted', function () {
        $this->driver->delete();
        $this->assertModelMissing($this->driver);
    });
});

describe('Vehicle Model', function () {
    beforeEach(function () {
        $this->vehicle = Vehicle::factory()->create();
    });

    test('a vehicle can be created and belongs to a company', function () {
        expect($this->vehicle)->toBeInstanceOf(Vehicle::class)
            ->and($this->vehicle->company)->toBeInstanceOf(Company::class);
    });

    test('a vehicle can be updated', function () {
        $this->vehicle->update(['license_plate' => 'NEW-PLATE-123']);
        expect($this->vehicle->fresh()->license_plate)->toBe('NEW-PLATE-123');
    });

    test('a vehicle can be deleted', function () {
        $this->vehicle->delete();
        $this->assertModelMissing($this->vehicle);
    });
});

describe('Trip Model', function () {
    beforeEach(function () {
        $this->trip = Trip::factory()->create();
    });

    test('a trip can be created with all its relationships', function () {
        expect($this->trip)->toBeInstanceOf(Trip::class)
            ->and($this->trip->company)->toBeInstanceOf(Company::class)
            ->and($this->trip->driver)->toBeInstanceOf(Driver::class)
            ->and($this->trip->vehicle)->toBeInstanceOf(Vehicle::class);
    });

    test('the location data is cast to an array', function () {
        expect($this->trip->start_location)->toBeArray()
            ->and(array_key_exists('lat', $this->trip->start_location))->toBeTrue();
    });

    test('a trip can be updated', function () {
        $this->trip->update(['status' => 'completed']);
        expect($this->trip->fresh()->status)->toBe('completed');
    });

    test('a trip can be deleted', function () {
        $this->trip->delete();
        $this->assertModelMissing($this->trip);
    });
});
