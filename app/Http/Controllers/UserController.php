<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;

class UserController extends Controller {

    public function index() {
        return $this->success(
            User::query()->where('role',  2)->get()
        );
    }

    public function show(User $user){
        return $user;
    }

    public function store(UserRequest $request) {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);
        $user = User::query()->create($data);

        return $this->success($user);
    }

    public function update(UserRequest $request, User $user) {
        $user->fill($request->validated());
        $user->update();
        return $this->success($user);
    }

    public function destroy(User $user) {
        $user->delete();
        return $this->success();
    }
}
