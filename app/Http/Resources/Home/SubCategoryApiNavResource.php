<?php

namespace App\Http\Resources\Home;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SubCategoryApiNavResource
 * @package App\Http\Resources\Home
 */
class SubCategoryApiNavResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => (int)$this->sub_categories_id,
            'name' => (string)$this->name,
            'en_name' => $this->en_name,
        ];
    }
}
