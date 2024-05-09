<?php

namespace App\Http\Resources;

// use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class DeepqArticleCollection extends JsonResource
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
            'id' => $this->mainCategory->en_name . '/' . $this->articles_id,
            'title' => $this->title,
            'category' => $this->mainCategory->name,
            'raw_file' => 'https://static.tvbs.com.tw/health2.0/ArticlesListForDeep/content/health_' . $this->articles_id . '.xml',
            'url' => config('constants.frontend_url') . '/' . $this->mainCategory->en_name . '/' . $this->articles_id,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
