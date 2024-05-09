<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class InstitutionsSaveSort
 * @package App\Http\Requests
 */
class InstitutionsSaveSort extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'masters_institutions' => ['required', 'array'],
        ];
    }
}
