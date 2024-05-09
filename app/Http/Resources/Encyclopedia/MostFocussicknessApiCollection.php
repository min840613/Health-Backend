<?php

namespace App\Http\Resources\Encyclopedia;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\ArticleApiNewsCollection;

class MostFocussicknessApiCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "organ_id" => (int)$this->organs->first()->id ?? null,
            "organ_name" => (string)$this->organs->first()->name ?? null,
            "organ_icon" => (string)$this->organs->first()->icon ?? null,
            "sickness_id" => (int)$this->id ?? null,
            "sickness_name" => (string)$this->name ?? null,
            "articles" => ArticleApiNewsCollection::collection($this->articles ?? [])
        ];
    }
}
