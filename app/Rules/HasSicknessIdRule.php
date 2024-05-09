<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Categories\MainCategoriesModel;

class HasSicknessIdRule implements Rule
{
    public function passes($attribute, $value)
    {
        $EncyclopediaData = MainCategoriesModel::where('en_name','encyclopedia')
                                                    ->first();
        // 判斷 categories 陣列是否包含 醫學百科
        if (in_array($EncyclopediaData['categories_id'], $value)) {
            if(!request()->input('medicine_article_sickness_id')) {
                return false;
            } elseif(!request()->input('medicine_article_category')) {
                return false;
            }
        }
        
        return true;
    }

    public function message()
    {
        return '『醫學百科疾病』與『醫學百科分類』為必填';
    }
}
