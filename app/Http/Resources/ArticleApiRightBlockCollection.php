<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleApiRightBlockCollection extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "article_id" => (int)$this->articles_id,
            "image_url" =>  (string)$this->image,
            "main_category" => (string)$this->mainCategories[0]->name,
            "main_category_en" => (string)$this->mainCategories[0]->en_name,
            "is_video" => empty($this->video_id) ? false : true,
            "title" => (string)$this->title,
            'experts' => ExpertInfoCollection::collection($this->masters ?? collect()),
        ];
    }
}
