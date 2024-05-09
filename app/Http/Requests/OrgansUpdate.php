<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class OrgansStore
 * @package App\Http\Requests
 */
class OrgansUpdate extends FormRequest
{
    protected $stopOnFirstFailure = true;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string' ,'max:10' ], 
            'icon' => ['image', 'mimes:svg' ], 
            'icon_android' => ['sometimes', 'image', 'mimes:png', 'dimensions:width=90,height=90'], 
            'icon_ios' => ['mimetypes:application/pdf', ], 
            'status' => ['required', 'integer'],
            
        ];
    }

    public function messages()
    {
        return [
           'icon.image' => 'icon欄位請上傳正確的svg檔案',
           'icon.mimes' => 'icon的格式必須是svg',
           'icon_android.image' => 'Android PNG Icon欄位請上傳正確的png檔案',
           'icon_android.mimes' => 'Android PNG Icon的格式必須是png',
           'icon_android.dimensions' => 'Android PNG Icon的寬高須是 :widthx:height',
           'icon_ios.mimetypes' => 'IOS PDF Icon的檔案格式須是pdf',
           'name.required' => '請輸入器官與組織的名稱',
           'name.max' => '器官與組織的名稱最多不得超過:max個字數',
        ];
    }
}
