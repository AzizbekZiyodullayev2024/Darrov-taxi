<?php

namespace App\Http\Controllers;

use App\Http\Requests\DriverOrderRequest;
use App\Models\Client;
use App\Models\TaxiOrder;
use Illuminate\Http\Request;

class TaxiOrderController extends Controller
{

    public function getOrdersByStatus(Request $request) {
        return $this->success([
            'orders' => TaxiOrder::query()->with('client')
                ->where('status', $request->status)
                ->orderByDesc('id')->get()
        ]);
    }

    public function getNewOrders() {
        return $this->success(['orders' => TaxiOrder::query()->where('status', 0)->get()]);
    }

    public function store(DriverOrderRequest $request) {
        $client = Client::query()->create($request->only(['name', 'phone']));
        $data = $request->validated();
        $data['client_id'] = $client->id;
        return $this->success([
            'order' => TaxiOrder::query()->create($data)
        ]);
    }
}
