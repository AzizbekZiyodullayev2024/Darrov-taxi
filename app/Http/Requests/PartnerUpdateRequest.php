<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class PartnerUpdateRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => '',
            'username' => '',
            'password' => '',
            'start_time' => '',
            'end_time' => '',
            'address' => '',
            'phone' => '',
            'img' => 'nullable|file',
            'longitude' => '',
            'latitude' => '',
            'open' => 'nullable|boolean',
            'position' => 'nullable'
        ];
    }
}
