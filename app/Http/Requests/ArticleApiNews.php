<?php

namespace App\Http\Requests;

/**
 * Class ArticleApiNews
 * @package App\Http\Requests
 */
class ArticleApiNews extends ApiRequest
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
