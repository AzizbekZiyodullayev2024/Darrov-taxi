<?php


namespace App\Http\Services;

use App\Models\Delivery;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use App\Jobs\VipLimitJob;
use App\Models\Driver;
use App\Models\History;
use App\Models\Order;
use App\Models\TaxiOrder;

class DeliveryService extends CRUDService {
    public function __construct(Delivery $model = null) {
        parent::__construct($model ?? new Delivery());
    }

    public function get() {
        return $this->indexResponse(Delivery::query()->paginate(100));
    }

//    public function addSum($request, $driver) {
//        $driver->sum += $request->sum;
//        $tariff = $driver->car->tariff;
//        if ($driver->sum >= $tariff->vip && $driver->vip == 0) {
//            $driver->vip = 1;
//            $driver->sum -= $tariff->vip;
//
//            dispatch((new VipLimitJob($driver->id))->delay(now()->addMonth()));
//        }
//
//        $driver->update();
//        return $this->success();
//    }

    public function check($request) {
        list($latitude, $longitude, $scope) = array_values($request->validated());

        return $this->success([
            'delivery' => Delivery::query()
//                ->online()
                ->statusActive()
//                ->active()
                ->whereRaw("haversine(latitude, longitude, $latitude, $longitude) <= $scope;")
                ->get()
        ]);
    }

    public function activate($status, $driver) {
        $driver->car->update(['status' => $status]);
        return $this->success($driver);
    }

    public function ordersInAir() {
        return TaxiOrder::query()
            ->where('driver_id', Driver::FREE)
            ->where('status', Driver::FREE)
            ->get();
    }

    public function mergeOrder($request) {
        $data = $request->validated();
        if ($data['type'] == 1) {
            $order = TaxiOrder::query();
            $key = "client";
        } else {
            $order = Order::query();
            $key = "customer";
        }

        $order = $order
            ->where('id', $data['order_id'])
            ->where('driver_id', 0)
            ->firstOrFail();
        $order->fill($data);
        $order->update();
        $order['phone'] = $order->$key->phone;
        return $this->success(['order' => $order]);
    }

    public function writeHistory($request, $driver) {
        $car = $driver->car;
        $residue = $driver->sum - ($driver->vip ? 0 : $car->tariff->client);

        $driver->update([
            'status' => config('constants.driver.free'),
            'sum' => $residue
        ]);
        $data = $request->validated();
        $data['driver_id'] = $driver->id;
        if ($data['order_type'] == config('constants.delivery_order_type')) {
            $data['order_type'] = Order::class;
        } else {
            $data['order_type'] = TaxiOrder::class;
        }

        return $this->success($driver->writeHistory($data));
    }

    public function daily($driver) {
        $history = History::query()->where('driver_id', $driver)->whereDay('created_at', '=', now()->day)->orderByDesc('created_at')->count();

        return $this->success($history);
    }

    public function clients($driver) {
        $clients = History::query()->where('driver_id', $driver)->orderByDesc('created_at')->get();

        return $this->success($clients);
    }

    public function profit($driver) {
        return History::query()->where('driver_id', $driver)->select(DB::raw('DATE(created_at) as date'), DB::raw('sum(fare) as benefit'))->groupBy('date')->orderBy('date', 'desc')->get();
    }
}
