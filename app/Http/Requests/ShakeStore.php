<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class ShakeStore
 * @package App\Http\Requests
 */
class ShakeStore extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'shake_type' => ['required', 'integer', Rule::in([2])],
            'shake_content_type' => ['required', 'integer', Rule::in([1, 2])],
            'shake_url' => [
                'required',
                Rule::when($this->shake_content_type === '2', ['integer']),
                Rule::when($this->shake_content_type === '1', ['url', 'max:100'])
            ],
            'is_ec_connect' => ['required', 'boolean'],
            'shake_title' => ['required', 'string'],
            'content' => ['required', 'string'],
            'shake_time_start' => ['required', 'before:shake_time_end', 'after:now'],
            'shake_time_end' => ['required', 'after:shake_time_start'],
            'shake_status' => ['required', 'integer', Rule::in([1, 0])],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'shake_url.integer' => '開啟類型為文章ID時，應輸入ID',
            'shake_url.url' => '開啟類型為網址時，應輸入網址',
            'shake_url.max' => '網址長度不得大於 :max',
            'shake_time_start.after' => '活動開始時間必須大於現在時間',
            'shake_time_start.before' => '活動開始時間必須小於活動結束時間',
            'shake_time_end.after' => '活動結束時間必須大於活動開始時間',
        ];
    }
}
