<?php

namespace App\Http\Resources\App;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class AIApiResource
 * @package App\Http\Resources\App
 */
class AIApiResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'questions' => $this->pluck('question'),
        ];
    }
}
