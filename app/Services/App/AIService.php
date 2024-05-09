<?php

namespace App\Services\App;

use App\Repositories\App\AIRepository;
use App\Models\Deepq\KeywordModel;
use Illuminate\Support\Collection;

/**
 * Class AIService
 * @package App\Services\App
 */
class AIService
{
    /** @var AIRepository */
    private AIRepository $repository;

    /**
     * AIService constructor.
     * @param AIRepository $repository
     */
    public function __construct(
        AIRepository $repository,
    ) {
        $this->repository = $repository;
    }

    /**
     * @return Collection
     */
    public function getSendPrompt(): Collection
    {
        $keywords = $this->repository->getSendPrompt();

        return $keywords->pluck('questions')->flatten();
    }
}
