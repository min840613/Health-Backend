<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Helpers\FilterHelper;

use Carbon\Carbon;

class LineArticlesCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
    * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array
    {
        $article_content = FilterHelper::removeReadMoreInContent($this->article->article_content);
        $article_content = FilterHelper::removeLink($article_content);
        $article_content = FilterHelper::removeEndtext($article_content);
        $article_content = FilterHelper::removeReadMore($article_content);

        $extended_articles = $this->article->recommendations;
        $article_content .= view('third_party_feed.line_articles.extended', compact('extended_articles'))->render();

        return [
            'id' => (int)$this->id,
            'release_date' => (int)Carbon::parse($this->release_date)->format('Y-m-d'),
            'rss_created' => (int)Carbon::parse($this->release_date)->getTimestamp(). '000',
            'rss_updated' => (int)Carbon::parse($this->article->updated_at)->getTimestamp(). '000',
            'article_id' => (int)$this->article->articles_id,
            'category_name' => $this->article->mainCategory->en_name,
            'title' => $this->article->title,
            'content' => $article_content,
            'image' => $this->article->image ? $this->article->image : '',
            'image_alt' => $this->article->image_alt ? $this->article->image_alt : '',
            'url' => config('constants.frontend_url') . '/' . $this->article->mainCategory->en_name . '/' . $this->article_id . '?utm_source=linetoday&utm_medium=line_original',
        ];
    }
}
