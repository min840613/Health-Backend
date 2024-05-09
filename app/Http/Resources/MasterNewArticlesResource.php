<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class MasterNewArticlesResource
 * @package App\Http\Resources
 */
class MasterNewArticlesResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $subCategory = $this->subCategories()->where('parent', $this->mainCategory->categories_id)->active()->first() ?? null;
        $tags = [];
        foreach ($this->tags as $v) {
            $tags[] = $v->tag;
            if (count($tags) >= 2) {
                break;
            }
        }

        $request->merge(['need_division' => true]);

        return [
            'article_id' => (int)$this->articles_id,
            'image_url' => (string)$this->image,
            'main_category' => (string)$this->mainCategory->name,
            'main_category_en' => (string)$this->mainCategory->en_name,
            'sub_category' => $subCategory->name ?? null,
            'sub_category_id' => $subCategory->sub_categories_id ?? null,
            'is_video' => empty($this->video_id) ? false : true,
            'title' => (string)$this->title,
            'content' => mb_substr(strip_tags($this->article_content), 0, 60),
            'tags' => $tags,
            'experts' => ExpertInfoCollection::collection($this->masters),
        ];
    }
}
