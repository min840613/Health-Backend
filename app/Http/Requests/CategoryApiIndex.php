<?php

namespace App\Http\Requests;

/**
 * Class CategoryApiIndex
 * @package App\Http\Requests
 */
class CategoryApiIndex extends ApiRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'count' => ['nullable', 'integer'],
            'main_category_en' => ['required', 'string'],
            'sub_category_en' => ['nullable', 'string'],
        ];
    }
}
