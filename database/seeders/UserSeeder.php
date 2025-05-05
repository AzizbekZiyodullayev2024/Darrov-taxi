<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::query()->firstOrCreate(
            ['username' => 'operator1'],
            [
                'name' => 'operator1', 'password' => bcrypt('operator1'),
                'role' => 2, 'phone' => '99 123 45 67'
            ]
        );

        User::query()->firstOrCreate(
            ['username' => 'suhrob'],
            [
                'name' => 'Suhrob', 'password' => bcrypt('suhrob'),
                'role' => 2, 'phone' => '99 111 45 67'
            ]
        );

        Driver::query()->firstOrCreate(
            ['phone' => '975007909'],
            [
                'name' => 'driver1', 'address' => 'Gipermarket',
                'latitude' => 41.552386, 'longitude' => 60.625399,
            ]
        );
        Car::query()->firstOrCreate(
            ['number' => '95 V 721 FA'],
            [
                'color' => 'Oq', 'count_seats' => 5, 'manufacture_date' => '2017',
                'driver_id' => 1, 'type_id' => 4, 'tariff_id' => 1, 'status' => 1
            ]
        );


        Driver::query()->firstOrCreate(
            ['phone' => '99 123 22 33'],
            [
                'name' => 'driver3', 'address' => 'TATU UF',
                'latitude' => 41.569975, 'longitude' => 60.631704,
            ]
        );
        Car::query()->firstOrCreate(
            ['number' => '90X222CC'],
            [
                'color' => 'Qora', 'count_seats' => 4, 'manufacture_date' => '2010',
                'driver_id' => 2, 'type_id' => 2, 'tariff_id' => 2, 'status' => 0
            ]
        );

        Driver::query()->firstOrCreate(
            ['phone' => '99 111 22 33'],
            [
                'name' => 'driver2', 'address' => 'UrDU',
                'latitude' => 41.556613, 'longitude' => 60.605714,
            ]
        );
        Car::query()->firstOrCreate(
            ['number' => '90X333BB'],
            [
                'color' => 'Sariq', 'count_seats' => 2, 'manufacture_date' => '2013',
                'driver_id' => 3, 'type_id' => 3, 'tariff_id' => 3, 'status' => 1
            ]
        );
    }
}
