<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class DeepqKeywordGenerate
 * @package App\Http\Requests
 */
class DeepqKeywordGenerate extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'keyword' => ['required', 'string', 'max:50'],
            'count' => ['required', 'integer', 'min:1',  'max:10'],
            'uuid' => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'keyword.required' => '請輸入關鍵字',
            'keyword.max' => '關鍵字最多不得超過:max個字數',
            'count.integer' => '生成數量請輸入數字',
            'count.max' => '生成數量不得超過:max',
        ];
    }
}
