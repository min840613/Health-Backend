<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class MasterExpertsDivisionsResource
 * @package App\Http\Resources
 */
class MasterExpertsDivisionsResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'name' => (string)$this->division->name,
            'description' => (string)$this->description
        ];
    }
}
