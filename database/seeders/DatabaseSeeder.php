<?php

namespace Database\Seeders;

use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::query()->firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin',
                'surname' => 'Admin',
                'password' => bcrypt("admin"),
                'phone' => '991111111',
                'role' => 1
            ]);

        $this->call(CarTypeSeeder::class);
        $this->call(TariffSeeder::class);
        $this->call(UserSeeder::class);

        $this->haversine();
    }

    private function haversine()
    {
        # DELIMITER $$
        try {
            DB::statement("
            CREATE FUNCTION haversine(lat1 FLOAT, long1 FLOAT, lat2 FLOAT, long2 FLOAT)
                RETURNS FLOAT
            BEGIN
                DECLARE dist FLOAT;
                DECLARE r FLOAT DEFAULT 6371; # r - Earth radius (km)

                SET dist =
                      r * acos(
                            cos(radians(lat1))
                            * cos(radians(lat2))
                            * cos(radians(long2) - radians(long1))
                            + sin(radians(lat1))
                            * sin(radians(lat2))
                      );

                RETURN dist;
            END
        ");
        } catch (QueryException $e) {
        }
    }
}
