<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use PDO;

class YahooExtDataRule implements Rule
{
    protected $is_yahoo_rss;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($is_yahoo_rss)
    {
        $this->is_yahoo_rss = $is_yahoo_rss;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($this->is_yahoo_rss) {
            if(!$value) {
                return false;
            } else {
                $DataCount = count(explode(',',$value));
                if($DataCount != 3){
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '當『Yahoo供稿』為『是』時，『YAHOO供稿延伸閱讀』欄位必須為3則資料。';
    }
}
