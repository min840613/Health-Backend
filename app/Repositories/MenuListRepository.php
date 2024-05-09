<?php

namespace App\Repositories;

use App\Models\Categories\MenuListModel;
use Illuminate\Support\Collection;

/**
 * Class MenuListRepository
 * @package App\Repositories
 */
class MenuListRepository
{
    /** @var MenuListModel */
    private MenuListModel $model;

    /**
     * MenuListRepository constructor.
     * @param MenuListModel $model
     */
    public function __construct(MenuListModel $model)
    {
        $this->model = $model;
    }

    /**
     * @param bool $isApp
     * @return Collection
     */
    public function nav(bool $isApp = false): Collection
    {
        return $this->model::with('subCategories')
            ->where('menu_list_status', 1)
            ->where('is_app', (int)$isApp)
            ->orderBy('sort', 'asc')
            ->get();
    }
}
