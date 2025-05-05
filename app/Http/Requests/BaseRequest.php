<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Services\Response;

class BaseRequest extends FormRequest {
    use Response;

    public function failedValidation(Validator $validator) {
        throw new HttpResponseException(
            $this->fail($validator->errors(), "Ma\u{2019}lumotlar noto\u{2018}g\u{2018}ri kiritilgan yoki to\u{2018}ldirilmagan!"
        ));
    }
}
