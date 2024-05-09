<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ArticlesIndex
 * @package App\Http\Requests
 */
class ArticlesIndex extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'search_publish_start' => ['nullable', 'date', 'lte:search_publish_end'],
            'search_publish_end' => ['nullable', 'date', 'gte:search_publish_start'],
            'search_main_category_id' => ['nullable', 'integer'],
            'search_sub_category_id' => ['nullable', 'integer'],
            'search_articles_status' => ['nullable', 'integer'],
            'search_author' => ['nullable', 'integer'],
            'search_keyword' => ['nullable', 'string'],
            'search_articles_id' => ['nullable', 'integer'],
        ];
    }
}
