<?php

namespace App\Http\Requests;

class CarRequest extends BaseRequest {
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
            "number" => "required",
            "color" => "required",
            "count_seats" => "required|numeric",
            "manufacture_date" => "required|date_format:Y",
            "type_id" => "required|numeric",
            'tariff_id' => "required|numeric",
            'status' => ''
        ];
    }
}
