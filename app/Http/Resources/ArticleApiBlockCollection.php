<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ArticleApiBlockCollection
 * @package App\Http\Resources
 */
class ArticleApiBlockCollection extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $subCategory = $this->subCategories()->where('parent', $this->mainCategory->categories_id)->active()->first() ?? null;

        return [
            'article_id' => $this->articles_id,
            'image_url' => $this->image,
            'main_category' => $this->mainCategory->name,
            'main_category_en' => $this->mainCategory->en_name,
            'sub_category' => $subCategory->name ?? null,
            'sub_category_id' => $subCategory->sub_categories_id ?? null,
            'sub_category_en' => $subCategory->en_name ?? null,
            'is_video' => !empty($this->fb_ia_video),
            'title' => $this->title,
        ];
    }
}
