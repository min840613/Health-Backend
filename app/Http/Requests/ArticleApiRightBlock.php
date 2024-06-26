<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleApiRightBlock extends FormRequest
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
