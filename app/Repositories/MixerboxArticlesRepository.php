<?php

namespace App\Repositories;

use App\Models\ThirdPartyFeed\MixerboxArticlesModel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Class MixerboxArticlesRepository
 * @package App\Repositories
 */
class MixerboxArticlesRepository
{
    /** @var MixerboxArticlesModel */
    private MixerboxArticlesModel $model;

    /**
     * MixerboxArticlesRepository constructor.
     * @param MixerboxArticlesModel $model
     */
    public function __construct(MixerboxArticlesModel $model)
    {
        $this->model = $model;
    }

    /**
     * 這邊取得的資料要先進先出，後續呈現再轉為降冪
     * @param Carbon $date
     * @return Collection
     */
    public function rss(Carbon $date): Collection
    {
        return $this->model::with(['article', 'article.mainCategory'])
            ->whereHas('article', function ($query) {
                $query->active()->where('is_mixerbox_article', 1);
            })
            ->whereHas('article.mainCategory', function ($query) {
                $query->filterAdvertorial();
            })
            ->where(function ($query) use ($date) {
                $query->where('release_date', $date->toDateString())
                    ->orWhereNull('release_date');
            })
            ->active()
            ->orderBy('created_at')
            ->get();
    }

    /**
     * @param Collection $mixerboxArticles
     * @return void
     */
    public function release(Collection $mixerboxArticles): void
    {
        $mixerboxArticles->map(function ($mixerboxArticle) {
            $mixerboxArticle->update(['release_date' => Carbon::now()->toDateString()]);
        });
    }
}
