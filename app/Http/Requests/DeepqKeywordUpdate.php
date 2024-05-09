<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class DeepqKeywordUpdate
 * @package App\Http\Requests
 */
class DeepqKeywordUpdate extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'keyword' => ['required', 'string', 'max:50'],
            'start_at' => ['required', 'string', 'before_or_equal:end_at'],
            'end_at' => ['required', 'string', 'after_or_equal:end_at'],
            'count' => ['required', 'integer', 'min:1',  'max:10'],
            'question' => ['required', 'array'],
            'question.*' => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'keyword.required' => '請輸入關鍵字',
            'keyword.max' => '關鍵字最多不得超過:max個字數',
            'start_at.required' => '請輸入開始時間',
            'start_at.before_or_equal' => '開始時間需小於結束時間',
            'end_at.required' => '請輸入結束時間',
            'end_at.after_or_equal' => '結束時間需大於開始時間',
            'count.integer' => '生成數量請輸入數字',
            'count.max' => '生成數量不得超過:max',
        ];
    }
}
