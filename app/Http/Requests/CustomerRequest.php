<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class CustomerRequest extends BaseRequest {
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
            'surname' => 'required',
            'phone' => ['required', Rule::unique('customers')
                ->ignore($this->route('customer'))],
            'password' => '',
            'card_number' => 'required|digits:16',
            'birth_date' => '',
            'img' => '',
            'lang' => ''
        ];
    }
}
