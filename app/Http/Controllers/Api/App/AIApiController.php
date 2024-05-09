<?php

namespace App\Http\Controllers\Api\App;

use App\Http\Controllers\Controller;
use App\Http\Resources\App\AIApiResource;
use App\Services\App\AIService;
use Symfony\Component\HttpFoundation\Response;

class AIApiController extends Controller
{
    /** @var AIService */
    private AIService $service;

    /**
     * AIController constructor.
     * @param AIService $service
     */
    public function __construct(AIService $service)
    {
        $this->service = $service;
    }

    /**
     * @return Response
     */
    public function aiPrompt(): Response
    {
        $questions = $this->service->getSendPrompt();

        return response()->success(AIApiResource::make($questions ?? collect()));
    }
}
