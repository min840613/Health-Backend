<?php

namespace App\Repositories;

use App\Models\Encyclopedia\SicknessModel;
use App\Models\DailyViewCountModel;

/**
 * Class ArticlesRepository
 * @package App\Repositories
 */
class EncyclopediaRepository
{
    /** @var SicknessModel */
    private SicknessModel $SicknessModel;

    /**
     * EncyclopediaRepository constructor.
     * @param SicknessModel $SicknessModel
     */
    public function __construct(SicknessModel $SicknessModel)
    {
        $this->SicknessModel = $SicknessModel;
    }

    public function getMostFocusSickness($sickness_count, $article_count, $exceptedSicknessId = [])
    {
        return $this->SicknessModel::whereHas('organs', function($q) {
                        $q->where('status', 1);
                    })
                    ->whereHas('articles', function($q) {
                        $q->active();
                    })
                    ->with(['organs',
                            'articles'  => function ($query){
                                $query->withSum(['viewCount as click_total' => function ($query) {
                                        $query->where('date', '>=', now()->subDays(3));
                                    }], 'click_count')
                                    ->active()
                                    ->orderByDesc('click_total')
                                    ->orderByDesc('publish')
                                    ->whereHas('mainCategory', function($q){
                                        $q->filterAdvertorial();
                                    });
                            }, 'articles.mainCategory'])
                    ->when(!empty($exceptedSicknessId), function ($q) use ($exceptedSicknessId) {
                        $q->whereNotIn('id', $exceptedSicknessId);
                    })
                    ->active()
                    ->orderBy('sort', 'asc')
                    ->orderByDesc('id')
                    ->limit($sickness_count)
                    ->get()
                    ->each(function ($sickness) use ($article_count) {
                        $sickness->articles = $sickness->articles->take($article_count);
                    });
    }
}
