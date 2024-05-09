<?php

namespace App\Http\Requests;

/**
 * Class ArticlesAppendCategories
 * @package App\Http\Requests
 */
class ArticlesAppendCategories extends ApiRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'article_ids' => ['required', 'array'],
            'main_category' => ['required', 'integer'],
            'sub_category' => ['present', 'nullable', 'integer'],
        ];
    }
}
