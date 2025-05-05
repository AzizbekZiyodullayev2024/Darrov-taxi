<?php


namespace App\Http\Services;

use App\Models\Car;

class CarService extends CRUDService
{
    public function __construct(Car $car = null)
    {
        parent::__construct($car ?? new Car());
    }

    public function all() {
        return $this->success(Car::all());
    }

    public function save($data, $driver)
    {
        if ($driver->car)
            return $this->fail([]);

        $data['driver_id'] = $driver->id;
        return $this->store($data);
    }
}
