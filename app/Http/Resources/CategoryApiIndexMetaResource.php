<?php

namespace App\Http\Resources;

use App\Enums\CloudFrontViewers;
use App\Helpers\DetectionHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CategoryApiIndexMetaResource
 * @package App\Http\Resources
 */
class CategoryApiIndexMetaResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $ad = stripos(env('APP_WEB_URL', null), 'health.com.tw') ? config('mobileAd.prd') : config('mobileAd.pre');

        return [
            'meta_title' => $this->meta_title ?? null,
            'meta_description' => $this->description ?? null,
            'mobile_ad' => DetectionHelper::isMobile() ? MobileAdCollection::collection(collect($ad)) : []
        ];
    }
}
