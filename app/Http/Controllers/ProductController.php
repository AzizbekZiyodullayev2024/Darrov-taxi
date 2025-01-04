<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;

class ProductController extends Controller {

    protected function success($data = [], $message = 'Success', $code = 200)
    {
        return response()->json([
            'status' => true,
            'data' => $data,
            'msg' => $message,
        ], $code);
    }

    public function index(Request $request) {
        $category = Category::query()->findOrFail($request->category_id);
        return $this->success(['products' => $category->products]);
    }

    public function categoryProducts($partner) {
        return $this->success(
            ['categories' => Category::query()->with('products')->where('partner_id', $partner)->get()],
            'Categories with products retrieved successfully'
        );
    }

    public function store(ProductRequest $request) {
        $data = $request->validated();
        if ($request->hasFile('img'))
            $data['img'] = $this->saveImage($request->file('img'), 'products');

        $product = Product::query()->create($data);
        return $this->success(['product' => $product, 'msg' => 'Product created successfully']);
    }

    public function update(ProductRequest $request, Product $product) {
        $data = $request->validated();
        if ($request->hasFile('img')) {
            $data['img'] = $this->saveImage($request->file('img'), 'products');
            $this->deleteFile($product->img, 'products');
        }
        if($data['quantity']>0){
            $data['status']=1;
        }
        $product->update($data);
        return $this->success(['product' => $product, 'msg' => 'Product updated successfully']);
    }

    public function destroy(Product $product) {
        $product->delete();
        return $this->success(['msg' => 'Product deleted successfully']);
    }
}