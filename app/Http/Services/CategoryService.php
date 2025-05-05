<?php


namespace App\Http\Services;

use App\Models\Category;

class CategoryService extends CRUDService
{
    public function __construct(Category $model = null) {
        parent::__construct($model ?? new Category());
    }

    public function get($request) {
        return $this->success([
            'categories' => Category::query()->where('partner_id', $request->partner_id)->get()
        ]);
    }

    public function all() {
        return $this->success(['categories' => Category::all()]);
    }

    public function save($request) {
        if ($request->hasFile('img'))
            $data['img'] = $this->saveImage($request->file('img'), 'categories');

        return parent::store($request->validated());
    }
}
