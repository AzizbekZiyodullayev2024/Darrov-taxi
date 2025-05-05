<?php

namespace App\Http\Requests;

class LocationRequest extends BaseRequest {
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
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'scope' => 'required|numeric',
            'tariff_id' => 'required|numeric'
        ];
    }
}
