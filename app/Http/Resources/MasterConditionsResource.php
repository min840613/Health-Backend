<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class MasterConditionsResource
 * @package App\Http\Resources
 */
class MasterConditionsResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'division_id' => (int)$this->id,
            'name' => (string)$this->name,
            'en_name' => (string)$this->en_name,
            'icon' => (string)$this->icon,
            'icon_hover' => (string)$this->icon_hover,
            'total_expert_count' => (int)$this->masters->count(),
            'institutions' => MasterConditionsInstitutionsResource::collection($this->institutions)
        ];
    }
}
