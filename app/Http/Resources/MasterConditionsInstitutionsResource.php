<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class MasterConditionsInstitutionsResource
 * @package App\Http\Resources
 */
class MasterConditionsInstitutionsResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'institution_id' => (int)$this->id,
            'name' => (string)$this->nick_name,
            'en_name' => (string)$this->en_name,
            'count' => (int)$this->master_count
        ];
    }
}
