<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DateRangeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'sponsor_id' => 'required|integer',
            'start' => 'date|before:end',
            'end' => 'date|after:start|before_or_equal:' . now()->parse($this->start)->addDays(14),
        ];
    }

    public function attributes()
    {
        return [
            'start' => '開始時間',
            'end' => '結束時間',
        ];
    }
}
