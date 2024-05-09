<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class InstitutionsIndex
 * @package App\Http\Requests
 */
class InstitutionsIndex extends FormRequest
{
    protected $stopOnFirstFailure = true;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'filter_name' => ['nullable', 'string'],
        ];
    }
}
