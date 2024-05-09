<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class MasterBannerResource
 * @package App\Http\Resources
 */
class MasterBannerResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'division_id' => $this->division_id ? (int)$this->division_id : null,
            'division_name' => (string)optional($this->division)->name,
            'institution_id' => $this->institution_id ? (int)$this->institution_id : null,
            'institution_name' => (string)optional($this->institution)->name,
            'expert_id' => $this->master_id ? (int)$this->master_id : null,
            'expert_name' => (string)optional($this->master)->name,
            'expert_en_name' => (string)optional($this->master)->en_name,
            'is_blank' => $this->type == 1 ? true : false,
            'url' => $this->url ? (string)$this->url : null,
            'image_url' => $this->image,
            'mobile_image_url' => $this->mobile_image,
        ];
    }
}
