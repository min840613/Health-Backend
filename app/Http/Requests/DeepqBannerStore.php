<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class DeepqBannerStore
 * @package App\Http\Requests
 */
class DeepqBannerStore extends FormRequest
{
    protected $stopOnFirstFailure = true;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'required|max:30',
            'image' => ['required','regex:/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?/i'],
            'start' => ['required', 'date_format:Y-m-d H:i', 'date'],
            'end' => ['required', 'date_format:Y-m-d H:i', 'date', 'after:start'],
            'status' => ['required']
        ];
    }

    public function attributes()
    {
        return [
            'title' => '活動名稱',
            'image' => '主視覺路徑',
            'start' => '活動上架時間',
            'end' => '活動下架時間',
            'status' => '活動狀態'
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute 為必填',
            'numeric' => ':attribute 必須為數字',
            'regex' => '請輸入正確:attribute',
            'max' => ':attribute 最多:max 個字',
            'date_format' => ':attribute 格式不符'
        ];
    }
}
