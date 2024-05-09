<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ArticleApiBlockMetaResource
 * @package App\Http\Resources
 */
class ArticleApiBlockMetaResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'headlines' => empty($this->resource['headline']) ? null : ArticleApiHotCollection::make($this->resource['headline']),
            'name' => $this->resource['taxon']['name'],
            'main_category' => optional($this->resource['taxon']->mainCategory)->name,
            'main_category_en' => optional($this->resource['taxon']->mainCategory)->en_name,
        ];
    }
}
