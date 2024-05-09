<?php

namespace App\Repositories;

use App\Models\HomeArea\RightBarModel;
use Illuminate\Support\Collection;

/**
 * Class TaxonRepository
 * @package App\Repositories
 */
class RightBarRepository
{
    /** @var RightBarModel */
    private RightBarModel $model;

    /**
     * RightBarRepository constructor.
     * @param RightBarModel $model
     */
    public function __construct(RightBarModel $model)
    {
        $this->model = $model;
    }

    /**
     * @param int $blockNum
     * @param int $count
     * @return RightBarModel|null
     */
    public function right_block(int $blockNum, int $count): ?RightBarModel
    {

        return $this->model::with('mainCategory')
                ->with('subCategory')
                ->with(['detail' => function($query) use($count) {
                    $query->with(['article' => function($query) {
                        $query->with(['mainCategories', 'masters'])
                                ->active();
                    }])
                    ->active()
                    ->orderBy('sort', 'asc')
                    ->limit($count);
                }])
                ->active()
                ->orderBy('sort')
                ->offset($blockNum)
                ->first();
    }
}
