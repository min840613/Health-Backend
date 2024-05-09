<?php

namespace App\Http\Requests;

/**
 * Class ExpertsApiNewArticles
 * @package App\Http\Requests
 */
class ExpertsApiNewArticles extends ApiRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'count' => ['nullable', 'integer'],
        ];
    }
}
