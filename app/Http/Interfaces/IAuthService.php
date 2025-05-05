<?php

namespace App\Http\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface IAuthService {
    public function login(Model $model, array $data);
}
