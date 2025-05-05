<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\DriverOrderRequest;
use App\Http\Requests\DriverRequest;
use App\Http\Requests\HistoryRequest;
use App\Http\Requests\LocationRequest;
use App\Http\Services\DriverService;
use App\Models\Car;
use App\Models\Driver;

class DriverController extends Controller {
    /**
     * @var DriverService
     */
    private DriverService $service;

    public function __construct(DriverService $service) {
        $this->service = $service;
    }

    public function index() {
        return $this->service->get();
    }

    public function store(DriverRequest $request) {
        return $this->service->store($request->validated());
    }

    public function show($driver) {
        $driver = Driver::with('car')->find($driver);
        return $this->success(['driver' => $driver]);
    }

    public function getTariff($driver) {
        return Car::query()->where('driver_id', $driver)->firstOrFail()->tariff;
    }

    public function addSum(Request $request, Driver $driver) {
        return $this->service->addSum($request, $driver);
    }

    public function update(Request $request, Driver $driver) {
        return $this->service->update($driver, $this->validated($request));
    }

    public function destroy(Driver $driver) {
        return $this->service->destroy($driver);
    }

    public function activate(Request $request, Driver $driver) {
        return $this->service->activate($request->get('status'), $driver);
    }

    public function check(LocationRequest $request) {
        return $this->service->check($request);
    }

    public function mergeOrder(DriverOrderRequest $request) {
        return $this->service->mergeOrder($request);
    }

    public function history(HistoryRequest $request, Driver $driver) {
        return $this->service->writeHistory($request, $driver);
    }

    public function orders() {
        return $this->service->ordersInAir();
    }

    public function daily($driver) {
        return $this->service->daily($driver);
    }

    /* Full history of clients */
    public function clients($driver) {
        return $this->service->clients($driver);
    }

    public function profit($driver) {
        return $this->service->profit($driver);
    }

//    public function offer() {
//        return Storage::get(public_path('taxi.pdf'));
//    }
}
