<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class MasterShowExperiencesCollection
 * @package App\Http\Resources
 */
class MasterShowExperiencesCollection extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'name' => $this->name,
            'is_current_job' => (bool)$this->is_current_job,
        ];
    }
}
