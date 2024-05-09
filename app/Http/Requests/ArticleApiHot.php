<?php

namespace App\Http\Requests;

/**
 * Class ArticleApiHot
 * @package App\Http\Requests
 */
class ArticleApiHot extends ApiRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'count' => ['nullable', 'integer'],
            'except_article_ids' => ['nullable', 'string'],
        ];
    }
}
