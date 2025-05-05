<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;

class ClientController extends Controller {

    public function index() {
        return $this->success(Client::all());
    }

    public function show(Client $client) {
        return $this->success($client);
    }

    public function store(ClientRequest $request) {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);

        return $this->success(Client::query()->create($data));
    }

    public function update(ClientRequest $request, Client $client) {
        $client->fill($request->validated());
        $client->update();
        return $this->success($client);
    }

    public function destroy(Client $client) {
        $client->delete();
        return $this->success();
    }
}
