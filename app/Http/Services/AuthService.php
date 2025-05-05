<?php

namespace App\Http\Services;

use App\Models\Partner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use App\Http\Interfaces\IAuthService;

class AuthService implements IAuthService {
    use Response;

    public function login(Model $model, $data) {
        if ($model instanceof Partner) {
            $model = $model::query()
                ->where('username', $data['username'])
                ->firstOrFail();

        }else {
            $model = $model::query()
                ->where('username', $data['username'])
                ->where('role', $data['role'])->firstOrFail();

        }


        $token = null;
        if ($model && $this->check($data['password'], $model->password))
            $token = $model->createToken('auth_token')->plainTextToken;

        return [$token, $model];
    }

    public function sendCode(Model $model, $data) {

    }

    private function check($val, $hashed) {
        return Hash::check($val, $hashed);
    }
}
