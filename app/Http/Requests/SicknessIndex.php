<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class SicknessIndex
 * @package App\Http\Requests
 */
class SicknessIndex extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'body_id' => ['required', 'integer'],
            'organ_id' => ['nullable', 'integer'],
        ];
    }
}
