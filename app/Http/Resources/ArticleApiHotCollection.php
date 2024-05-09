<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ArticleApiHotCollection
 * @package App\Http\Resources
 */
class ArticleApiHotCollection extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $currentCategoryEnName = $request->has('main_category_en') ? $request->main_category_en : null;
        $currentCategoryName = $request->has('main_category_en') ? $this->mainCategories->where('en_name', $currentCategoryEnName)->first()->name : null;
        $currentCategoryId = $request->has('main_category_en') ? $this->mainCategories->where('en_name', $currentCategoryEnName)->first()->categories_id : null;

        $subCategory = $this->subCategories()
                            ->when($currentCategoryId, function ($query, $currentCategoryId) {
                                $query->where('parent', $currentCategoryId);
                            })
                            ->when(!$currentCategoryId, function ($query) {
                                $query->where('parent', $this->mainCategory->categories_id);
                            })
                            ->active()->first() ?? null;

        return [
            'article_id' => $this->articles_id,
            'image_url' => $this->image,
            'main_category' => $currentCategoryName ? $currentCategoryName : $this->mainCategory->name,
            'main_category_en' => $currentCategoryEnName ? $currentCategoryEnName : $this->mainCategory->en_name,
            'sub_category' => $subCategory->name ?? null,
            'sub_category_id' => $subCategory->sub_categories_id ?? null,
            'sub_category_en' => $subCategory->en_name ?? null,
            'is_video' => !empty($this->video_id),
            'title' => $this->title,
            'content' => $this->when($request->type !== 'nav', mb_substr(strip_tags($this->article_content), 0, 60)),
            'tags' => optional($this->tags)->take(2)->pluck('tag')->implode(','),
            'view_count' => $this->viewCountClick,
            'publish' => $this->publish,
            'video_type' => $this->video_type,
            'video_id' => $this->video_id,
            'adult_flag' => $this->adult_flag,
        ];
    }
}
