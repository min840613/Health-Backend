<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SvgSizeRule implements Rule
{

    public $svgFilePath;
    public $width;
    public $height;
    public $fileWidth;
    public $fileHeight;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($svgFilePath, $width, $height)
    {
        $this->svgFilePath = $svgFilePath;
        $this->width = $width;
        $this->height = $height;
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
        $parsed_xml     = simplexml_load_string( file_get_contents( $this->svgFilePath ) );
        $svg_attributes = $parsed_xml->attributes();
        $width          = (int) $svg_attributes->width;
        $height         = (int) $svg_attributes->height;

        $this->fileWidth  = $width;
        $this->fileHeight = $height;

        if ($width != $this->width || $height != $this->height) {
            return false;
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
        return 'svg檔案的寬高須為' . $this->width . 'x' . $this->height . ', 您上傳的寬高為' . $this->fileWidth . 'x' . $this->fileHeight;
    }
}
