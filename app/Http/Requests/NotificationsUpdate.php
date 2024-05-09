<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class NotificationsUpdate
 * @package App\Http\Requests
 */
class NotificationsUpdate extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'prepush' => ['required'],
            'message' => ['required', 'string'],
            'message_body' => ['required', 'string'],
            'content_type' => ['required', Rule::in([1, 2])],
            'url' => ['required_if:content_type,1', 'nullable', 'url'],
            'content' => ['required_if:content_type,2', 'nullable', 'string'],
            'image' => ['required', 'url'],
            'content_type' => ['required_if:is_need_detail,0', Rule::in([1, 2])],
            'url' => ['required_if:is_need_detail,0','required_if:content_type,1', 'nullable', 'url'],
            'content' => ['required_if:is_need_detail,0','required_if:content_type,2', 'nullable', 'string'],
            'image' => ['required_if:is_need_detail,0', 'url'],
            'article_id' => ['required_if:is_article,1', 'integer'],
            'shake_id' => ['required_if:is_shake,1', 'integer'],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'url.url' => '輸入URL 格式錯誤',
            'image' => '圖片URL 格式錯誤',
        ];
    }
}
