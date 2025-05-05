<?php

namespace App\Http\Controllers;

use App\Http\Requests\TariffRequest;
use App\Models\Tariff;

class TariffController extends Controller {

    public function index() {
        return $this->success(['tariffs' => Tariff::all()]);
    }

    public function store(TariffRequest $request) {
        $tariff = Tariff::query()->create($request->validated());
        return $this->success(['tariff' => $tariff]);
    }

    public function update(TariffRequest $request, Tariff $tariff) {
        $tariff->fill($request->validated());
        $tariff->update();
        return $this->success(['tariff' => $tariff]);
    }

    public function destroy(Tariff $tariff) {
        $tariff->delete();
        return $this->success([]);
    }
}
