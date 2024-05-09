<?php

namespace App\Http\Resources;

use App\Models\Articles\ArticleModel;
use App\Models\Articles\KeyvisualModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ArticleApiNewsCollection
 * @package App\Http\Resources
 */
class ArticleApiNewsCollection extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        switch (true) {
            case $this->resource instanceof KeyvisualModel:
                $isArticle = $this->article !== null;
                return [
                    'article_id' => $this->source_id,
                    'url' => $this->link,
                    'main_category' => $isArticle ? $this->article->mainCategory->name : null,
                    'main_category_en' => $this->type,
                    'image_url' => $this->image,
                    'is_video' => $isArticle ? !empty($this->artcile->fb_ia_video) : false,
                    'title' => $this->title,
                ];
                break;
            case $this->resource instanceof ArticleModel:
                return [
                    'article_id' => $this->articles_id,
                    'url' => null,  // 文章 url 由前端組
                    'main_category' => $this->mainCategory->name,
                    'main_category_en' => $this->mainCategory->en_name,
                    'image_url' => $this->image,
                    'is_video' => !empty($this->fb_ia_video),
                    'title' => $this->title,
                ];
                break;
        }
    }
}
