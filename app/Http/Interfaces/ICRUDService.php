<?php

namespace App\Http\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface ICRUDService {

    public function store($data);
    public function update(Model $model, $data);
    public function destroy(Model $model);
    public function saveImage($img, $path);
}
