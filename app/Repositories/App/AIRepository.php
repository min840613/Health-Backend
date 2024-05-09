<?php

namespace App\Repositories\App;

use App\Models\Deepq\KeywordModel;
use Illuminate\Support\Collection;

/**
 * Class AIRepository
 * @package App\Repositories\App
 */
class AIRepository
{
    /** @var KeywordModel */
    private KeywordModel $model;

    /**
     * AIRepository constructor.
     * @param KeywordModel $model
     */
    public function __construct(KeywordModel $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function getSendPrompt(): Collection
    {
        return $this->model::with(['questions' => function ($query) {
            $query->orderBy('sort');
        }])->published()->orderBy('start_at')->get();
    }
}
