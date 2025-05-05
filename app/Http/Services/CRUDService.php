<?php

namespace App\Http\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use App\Http\Interfaces\ICRUDService;
use App\Models\BaseModel;
use Illuminate\Support\Facades\File;

abstract class CRUDService implements ICRUDService {
    use Response;

    protected Model $model;
    public function __construct(Model $model = null) {
        $this->model = $model ?? new BaseModel();
    }

    public function store($data, Model $model = null) {
        $model = $model ?? $this->model;

        try {
            $model->fill($data);
            $model->save();
        } catch (QueryException $e) {
            return $this->fail($e);
        }

        return $this->success($model, "Muvaffaqiyatli saqlandi");
    }

    public function saveImage($img, $path) {
        $imageName = time().'.'. $img->extension();
        $img->move(public_path('storage/' . $path), $imageName);
        return $imageName;
    }

    public function update(Model $model, $data) {
        try {
            if(array_key_exists('password', $data)) {
                $data['password'] = bcrypt($data['password']);
            }
            $model->update($data);
            $model->refresh();
        } catch (QueryException $e) {
            return $this->fail($e);
        }

        return $this->success($model, "Muvaffaqiyatli yangilandi");
    }

    public function destroy(Model $model) {
        try {
            $model->delete();
        } catch (QueryException $e) {
            return $this->fail($e);
        }

        return $this->success([], "Muvaffaqiyatli o\u{2018}chirildi");
    }

    protected function deleteFile($name, $path) {
        File::delete(public_path("storage/$path/" . $name));
    }
}
