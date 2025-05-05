<?php

namespace App\Http\Controllers;

use App\Http\Services\CategoryService;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;

class CategoryController extends Controller {

    /**
     * @var CategoryService
     */
    private CategoryService $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request) {
        return $this->service->get($request);
    }

    public function all() {
        return $this->service->all();
    }

    public function store(CategoryRequest $request) {
        return $this->service->save($request);
    }

    public function show(Category $category) {
        return $this->success(['category' => $category]);
    }

    public function update(CategoryRequest $request, Category $category) {
        return $this->service->update($category, $request->validated());
    }

    public function destroy(Category $category) {
        return $this->service->destroy($category);
    }
}
