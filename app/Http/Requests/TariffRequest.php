<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class TariffRequest extends BaseRequest {
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
            'name' => ['required', Rule::unique('tariffs')
                ->ignore($this->route('tariff'))],
            'client' => 'required|numeric',
            'minute' => 'required',
            'km' => 'required',
            'min_pay' => 'required',
            'min_km' => 'required',
            'out_city' => 'required',
            'vip' => 'required'
        ];
    }
}
