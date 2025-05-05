<?php

namespace App\Http\Requests;

class ProductRequest extends BaseRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name' => 'required',
            'category_id' => 'required|numeric',
            'img' => '',
            'price' => 'required|min:1',
            'quantity' => 'required|min:1',
            'description' => '',
            'discount' => '',
        ];
    }
}
