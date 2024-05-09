<?php

namespace App\Http\Requests;

/**
 * Class ArticleApiNews
 * @package App\Http\Requests
 */
class EncyclopediaApiMostFocus extends ApiRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'sickness_count' => ['nullable', 'integer'],
            'article_count' => ['nullable', 'integer'],
        ];
    }
}
