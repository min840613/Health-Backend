<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class MasterExpertsExpertiseResource
 * @package App\Http\Resources
 */
class MasterExpertsExpertiseResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'name' => (string)$this->name
        ];
    }
}
