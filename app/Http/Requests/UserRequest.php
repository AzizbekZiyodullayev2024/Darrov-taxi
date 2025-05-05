<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UserRequest extends BaseRequest {
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
        $unique = Rule::unique('users')->ignore($this->route('user'));
        return [
            'name' => 'required|string',
            'surname' => '',
            'username' => ['required', 'string', 'max:255', $unique],
            'password' => 'string|min:5',
            'phone' => ['required', $unique],
            'phone2' => '',
            'img' => '',
            'role' => ''
        ];
    }
}
