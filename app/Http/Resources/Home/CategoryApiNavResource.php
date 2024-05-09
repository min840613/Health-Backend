<?php

namespace App\Http\Resources\Home;

use App\Http\Resources\ArticleApiHotCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CategoryApiNavResource
 * @package App\Http\Resources\Home
 */
class CategoryApiNavResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $this->subCategories = $this->subCategories->where('status', '1');

        return [
            'main_category' => (string)$this->title,
            'main_category_en' => !empty($this->categories_id) ? (string)$this->url : null,
            'sub_categories' => SubCategoryApiNavResource::collection($this->subCategories),
            'is_blank' => (bool)$this->blank,
            'custom_url' => empty($this->categories_id) ? (string)$this->url : null,
            'hot_articles' => ArticleApiHotCollection::collection($this->articles),
        ];
    }
}
