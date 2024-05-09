<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpertInfoCollection extends JsonResource
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
            'expert_name' => $this->name,
            'expert_description' => $this->description,
            'expert_image_url' => $this->image,
            'divisions' => $this->when($request->input('need_division'), MasterExpertsDivisionsResource::collection($this->divisions)),
        ];
    }
}
