<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ApiRequest
 * @package App\Http\Requests
 */
class ApiRequest extends FormRequest
{
    /**
     * @param Validator $validator
     * @throws \JsonException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new \JsonException($validator->errors()->first(), 422);
    }
}
