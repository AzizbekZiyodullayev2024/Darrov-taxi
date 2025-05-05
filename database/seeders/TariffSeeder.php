<?php

namespace Database\Seeders;

use App\Models\Tariff;
use Illuminate\Database\Seeder;

class TariffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tariff::query()->firstOrCreate(['name' => 'Oddiy', 'client' => 750, 'minute' => 200, 'km' => 1000, 'vip' => 200000, 'min_pay' => 3000, 'out_city' => 3000, 'min_km' => 3000]);
        Tariff::query()->firstOrCreate(['name' => 'Comfort', 'client' => 1000, 'minute' => 200, 'km' => 1000, 'vip' => 300000, 'min_pay' => 3000, 'out_city' => 3000, 'min_km' => 3000]);
        Tariff::query()->firstOrCreate(['name' => 'Comfort+', 'client' => 1200, 'minute' => 200, 'km' => 1000, 'vip' => 500000, 'min_pay' => 3000, 'out_city' => 3000, 'min_km' => 3000]);
    }
}
