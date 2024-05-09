<?php

namespace App\Http\Requests;

/**
 * Class KeywordApiIndex
 * @package App\Http\Requests
 */
class KeywordApiIndex extends ApiRequest
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
