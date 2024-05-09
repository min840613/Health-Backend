<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class InstitutionsStore
 * @package App\Http\Requests
 */
class InstitutionsStore extends FormRequest
{
    protected $stopOnFirstFailure = true;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'en_name' => ['required', 'string', 'max:15', 'regex:/[a-z]+/', 'lowercase', 'alpha:ascii'],
            'nick_name' => ['required', 'string', 'max:10'],
            'is_centre' => ['nullable', 'boolean'],
            'status' => ['required', 'integer'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '請輸入醫療院所的名稱',
            'name.max' => '醫療院所的名稱最多不得超過:max個字數',
            'en_name.max' => '英文名稱最多不得超過:max個字數',
            'en_name.alpha' => '英文名稱格式錯誤，僅能輸入小寫英文',
            'en_name.lowercase' => '英文名稱格式錯誤，僅能輸入小寫英文',
            'en_name.regex' => '英文名稱格式錯誤，僅能輸入小寫英文',
            'nick_name.required' => '請輸入簡稱的名稱',
            'nick_name.max' => '簡稱的名稱最多不得超過:max個字數',
        ];
    }
}
