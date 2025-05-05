<?php

namespace App\Http\Controllers;

use App\Http\Services\CarService;
use Illuminate\Http\Request;
use App\Http\Requests\CarRequest;
use App\Models\Driver;
use App\Models\Car;

class CarController extends Controller {

    /**
     * @var CarService
     */
    private CarService $service;

    public function __construct(CarService $service) {
        $this->service = $service;
    }

    public function index() {
        return$this->service->all();
    }

    public function store(CarRequest $request, Driver $driver) {
        return $this->service->save($request->validated(), $driver);
    }

    public function show(Car $car) {
        return $this->success($car);
    }

    public function update(Request $request, Car $car) {
        return $this->service->update($car, $this->validated($request));
    }

    public function destroy(Car $car) {
        return $this->service->destroy($car);
    }
}
