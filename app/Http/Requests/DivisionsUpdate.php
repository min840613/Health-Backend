<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class DivisionsUpdate
 * @package App\Http\Requests
 */
class DivisionsUpdate extends FormRequest
{
    protected $stopOnFirstFailure = true;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:10'],
            'en_name' => ['required', 'string', 'max:15', 'regex:/[a-z]+/', 'lowercase', 'alpha:ascii'],
            'icon' => ['image', 'mimes:svg'],
            'icon_hover' => ['image', 'mimes:svg'],
            'icon_android' => ['required_with:icon_android_hover', 'image', 'mimes:png'],
            'icon_android_hover' => ['required_with:icon_android', 'image', 'mimes:png'],
            'icon_ios' => ['required_with:icon_ios_hover', 'mimetypes:application/pdf'],
            'icon_ios_hover' => ['required_with:icon_ios', 'mimetypes:application/pdf'],
            'status' => ['required', 'integer'],

        ];
    }

    public function messages()
    {
        return [
            'en_name.max' => '英文名稱最多不得超過:max個字數',
            'en_name.alpha' => '英文名稱格式錯誤，僅能輸入小寫英文',
            'en_name.lowercase' => '英文名稱格式錯誤，僅能輸入小寫英文',
            'en_name.regex' => '英文名稱格式錯誤，僅能輸入小寫英文',
            'icon.image' => 'icon欄位請上傳正確的svg檔案',
            'icon.mimes' => 'icon的格式必須是svg',
            'icon_hover.image' => 'icon欄位請上傳正確的svg檔案',
            'icon_hover.mimes' => 'icon的格式必須是svg',
            'icon_android.image' => 'Android PNG Icon欄位請上傳正確的png檔案',
            'icon_android.mimes' => 'Android PNG Icon的格式必須是png',
            'icon_android.required_with' => 'Android PNG Icon Hover有圖檔時，請上傳Android PNG Icon的檔案',
            'icon_android_hover.image' => 'Android PNG Icon欄位請上傳正確的png檔案',
            'icon_android_hover.mimes' => 'Android PNG Icon的格式必須是png',
            'icon_android_hover.required_with' => 'Android PNG Icon有圖檔時，請上傳Android PNG Icon的Hover檔案',
            'icon_ios.mimetypes' => 'IOS PDF Icon的檔案格式須是pdf',
            'icon_ios.required_with' => 'IOS PDF Icon Hover有圖檔時，請上傳IOS PDF Icon的檔案',
            'icon_ios_hover.mimetypes' => 'IOS PDF Icon的檔案格式須是pdf',
            'icon_ios_hover.required_with' => 'IOS PDF Icon 有圖檔時，請上傳IOS PDF Icon的Hover檔案',
            'name.required' => '請輸入科別的名稱',
            'name.max' => '科別的名稱最多不得超過:max個字數',
        ];
    }
}
