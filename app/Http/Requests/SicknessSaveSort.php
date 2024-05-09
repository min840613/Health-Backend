<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class SicknessSaveSort
 * @package App\Http\Requests
 */
class SicknessSaveSort extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'encyclopedia_sickness' => ['required', 'array'],
            'sortId' => ['required', 'string'],
        ];
    }
}
