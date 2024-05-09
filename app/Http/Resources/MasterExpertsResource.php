<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class MasterExpertsResource
 * @package App\Http\Resources
 */
class MasterExpertsResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'expert_en_name' => (string)$this->en_name,
            'expert_name' => (string)$this->name,
            'image_url' => (string)$this->image,
            'institution' => (string)optional($this->institution)->nick_name,
            'title' => (string)$this->title,
            'divisions' => MasterExpertsDivisionsResource::collection($this->divisions),
            'expertise' => MasterExpertsExpertiseResource::collection($this->expertise),
            'has_article' => $this->articles_count > 0,
        ];
    }
}
