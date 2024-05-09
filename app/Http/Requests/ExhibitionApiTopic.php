<?php

namespace App\Http\Requests;

/**
 * Class ExhibitionApiTopic
 * @package App\Http\Requests
 */
class ExhibitionApiTopic extends ApiRequest
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
