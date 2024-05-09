<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class MixerboxArticlesCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'title' => $this->title,
            'image' => $this->image ?? '',
            'url' => $this->generateRssArticleUrl($this->mainCategory->en_name, $this->articles_id),
            'publish_date' => Carbon::parse($this->publish)->format('D, d M Y H:i:s O'),
            'category_name' => $this->mainCategory->en_name,
        ];
    }

    /**
     * @param string $enName
     * @param int $articleId
     * @return string
     */
    private function generateRssArticleUrl(string $enName, int $articleId): string
    {
        return config('constants.frontend_url') . "/{$enName}/{$articleId}?utm_source=Mixer&utm_medium=Mixer_health";
    }
}
