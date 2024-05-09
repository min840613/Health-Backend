<?php

namespace App\Services;

use App\Repositories\EncyclopediaRepository;

// use Illuminate\Support\Carbon;
// use Illuminate\Support\Collection;

/**
 * Class ArticlesService
 * @package App\Services
 */
class EncyclopediaService
{
    /** @var EncyclopediaRepository */
    private EncyclopediaRepository $repository;

    /**
     * EncyclopediaRepository constructor.
     * @param EncyclopediaRepository $repository
     */
    public function __construct(
        EncyclopediaRepository $repository
    )
    {
        $this->repository = $repository;
    }

    public function mostFocusSickness($sickness_count, $article_count)
    {
        // 不足文章數，會以最新對應疾病的文章補上，只是$sicknessArticles->articles->click_total會是null
        $sicknessArticles = $this->repository->getMostFocusSickness($sickness_count, $article_count);

        if($sicknessArticles->count() < $sickness_count){
            $exceptedSicknessId = [];
            $exceptedSicknessId = $sicknessArticles->pluck('id');
            $newSicknessArticles = $this->repository->getMostFocusSickness($sickness_count-$sicknessArticles->count(), $article_count, $exceptedSicknessId);
            if($newSicknessArticles->first()){
                $sicknessArticles = $sicknessArticles->concat($newSicknessArticles);
            }
        }
        return $sicknessArticles;
    }
}
