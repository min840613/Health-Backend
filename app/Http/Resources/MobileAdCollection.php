<?php

namespace App\Http\Resources;

use App\Helpers\DetectionHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class MobileAdCollection
 * @package App\Http\Resources
 */
class MobileAdCollection extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        if (DetectionHelper::isIos()) {
            $admob = $this->resource['admob_ios'];
        } elseif (DetectionHelper::isAndroid()) {
            $admob = $this->resource['admob_android'];
        }

        return [
            'position' => $this->resource['position'] ?? null,
            'index' => $this->resource['index'] ?? null,
            'admob' => $admob ?? null,
        ];
    }
}
