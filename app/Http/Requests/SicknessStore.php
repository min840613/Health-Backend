<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class SicknessStore
 * @package App\Http\Requests
 */
class SicknessStore extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:10'],
            'status' => ['required', 'integer'],
            'create_organ_id_string' => ['required', 'string'],
        ];
    }

   public function messages()
    {
        return [
            'name.max' => '疾病的名稱最多不得超過:max個字數',
        ];
    }
}
