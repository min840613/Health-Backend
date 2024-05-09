<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExhibitionApiResource extends JsonResource
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
            'exhibition_id' => (int)$this->id,
            'title' => (string)$this->title,
            'image_url' => !empty($this->image) ? (string)$this->image : null,
            'web_url' => (string)$this->web_url,
            'app_url' => (string)$this->app_url,
            'is_blank' => (bool)$this->blank,
            'start_at' => (string)$this->start_at,
            'end_at' => (string)$this->end_at,
            'main_category' => '專題',
            'main_category_en' => 'topic',
        ];

    }
}
