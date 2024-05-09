<?php

namespace App\Http\Requests;

/**
 * Class ArticleApiBlock
 * @package App\Http\Requests
 */
class ArticleApiBlock extends ApiRequest
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
