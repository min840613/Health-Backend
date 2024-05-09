<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class DivisionsSaveSort
 * @package App\Http\Requests
 */
class DivisionsSaveSort extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'masters_divisions' => ['required', 'array'],
        ];
    }
}
