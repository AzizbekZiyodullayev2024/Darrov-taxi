<?php


namespace App\Http\Services;

use App\Models\CarType;

class CarTypeService extends CRUDService
{
    public function __construct(CarType $carType = null)
    {
        parent::__construct($carType ?? new CarType());
    }

    public function all() {
        return $this->success(CarType::all());
    }

    public function save($data) {
        $car = CarType::query()->firstOrCreate($data);
        if ($car->wasRecentlyCreated) return $this->success();
        return $this->fail();
    }
}
