<?php

namespace App\Http\Resources;

//use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Articles\ArticleModel;

class YahooRssCollection extends JsonResource
{
    /**
    * Transform the resource collection into an array.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
    */
    public function toArray($request)
    {
        $article_url = config('constants.frontend_url') . '/' . $this->article->mainCategory->en_name . '/' . $this->article->articles_id;
        $content = $this->parseContent($this->article->article_content);
        // 加上延伸閱讀
        $further_reading = '<p class="read-more-vendor"><span>更多健康2.0報導</span><br />';
        foreach ($this->furtherReading as $extend) {
            $further_reading .= '<a href="' . config('constants.frontend_url') . '/' . $extend->furtherArticle->mainCategory->en_name . '/' . $extend->recommendation_article_id . '?utm_source=Yahoo&utm_medium=Yahoo_news&utm_campaign=articleid_' . $this->article->articles_id . '">' . $extend->furtherArticleTitle->title . '</a><br />';
        }
        $further_reading .= '</p>';
        $content = $content . $further_reading;
        // 文章後加入文章來源
        $content = $content . '<p>本文由健康2.0授權報導，未經同意禁止轉載，點此查看<a href="' . $article_url . '?utm_source=Yahoo&utm_medium=Yahoo_news">原始文章</a></p>';

        return [
            'title' => $this->article->title,
            'link' => $article_url,
            'description' => html_entity_decode(mb_substr(str_replace('&nbsp;', '', strip_tags($this->article->article_content)), 0, 100, 'UTF-8')),
            'publish_date' => Carbon::now()->format('D, d M Y 08:00:00 +0800'),
            'guid' => $article_url,
            'category_name' => $this->article->mainCategory->name,
            'image_url' => $this->article->image,
            'content' => $content,
        ];
    }

    /**
    * @param string|null $content
    * @return string
    */
    private function parseContent(?string $content): string
    {
        if ($content === null) {
            return $content;
        }
        //移除延伸閱讀
        $content = preg_replace('(<p[^>]+class="endtext">([\s\S]*?)<\/p>)', '', $content);
        $content = preg_replace('(<div[^>]+class="endtext">([\s\S]*?)<\/div>)', '', $content);
        $content = preg_replace('(<p><span[^>]+class="endtext">([\s\S]*?)<\/span><\/p>)','',$content);
        $content = preg_replace('(<p[^>]+\><span[^>]+class="endtext">([\s\S]*?)<\/span><\/p>)','',$content);
        $content = preg_replace('(<h1><span[^>]+class="endtext">([\s\S]*?)<\/span><\/h1>)','',$content);
        $content = preg_replace('(<h2><span[^>]+class="endtext">([\s\S]*?)<\/span><\/h2>)','',$content);
        $content = preg_replace('(<h3><span[^>]+class="endtext">([\s\S]*?)<\/span><\/h3>)','',$content);
        $content = preg_replace('/<span[^>]+\>|<\/span>/i', '', $content);
        $content = preg_replace('#<strong.*?>(.*?)</strong>#i', '\1', $content);

        return $content;
    }
}
