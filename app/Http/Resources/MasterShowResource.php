<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class MasterShowResource
 * @package App\Http\Resources
 */
class MasterShowResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'name' => $this->name,
            'image_url' => $this->content_image,
            'institution' => optional($this->institution)->nick_name,
            'divisions' => DivisionCollection::collection($this->divisions),
            'title' => $this->title,
            'experiences' => MasterShowExperiencesCollection::collection($this->experiences),
            'expertise' => $this->expertise->pluck('name'),
            'is_contracted' => (bool)$this->is_contracted,
            'articles' => ArticleApiHotCollection::collection($this->articles),
        ];
    }
}
