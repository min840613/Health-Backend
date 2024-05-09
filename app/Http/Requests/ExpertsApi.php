<?php

namespace App\Http\Requests;

/**
 * Class ExpertsApi
 * @package App\Http\Requests
 */
class ExpertsApi extends ApiRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'count' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer'],
            'division' => ['nullable', 'string'],
            'institution' => ['nullable', 'string'],
            'expertise_keyword' => ['nullable', 'string'],
        ];
    }
}
