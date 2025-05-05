<?php

namespace App\Http\Controllers;

use App\Http\Services\CarTypeService;
use App\Http\Requests\CarTypeRequest;
use App\Models\CarType;

class CarTypeController extends Controller {

    /**
     * @var CarTypeService
     */
    private CarTypeService $service;

    public function __construct(CarTypeService $service) {
        $this->service = $service;
    }
    public function index() {
        return $this->service->all();
    }

    public function store(CarTypeRequest $request) {
        return $this->service->save($request->validated());
    }

    public function show(CarType $car_type) {
        return $this->success($car_type);
    }

    public function update(CarTypeRequest $request, CarType $car_type) {
        return $this->service->update($car_type, $request->validated());
    }

    public function destroy(CarType $carType) {
        return $this->service->destroy($carType);
    }
}
