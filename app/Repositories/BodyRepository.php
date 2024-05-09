<?php

namespace App\Repositories;

use App\Models\Encyclopedia\BodyModel;
use Illuminate\Support\Collection;

/**
 * Class BodyRepository
 * @package App\Repositories
 */
class BodyRepository
{
    /** @var BodyModel */
    private BodyModel $model;

    /**
     * BodyRepository constructor.
     * @param BodyModel $model
     */
    public function __construct(BodyModel $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection
     */
    public function filterWithOrgans(): Collection
    {
        return $this->model::with(['organs'])->get()->keyBy('id')->map(function ($body) {
            $body->organs->only(['id', 'name']);
            return $body;
        });
    }

    /**
     * @param int $bodyId
     * @param int|null $organId
     * @return BodyModel
     */
    public function findByBodyAndOrgan(int $bodyId, ?int $organId): BodyModel
    {
        return $this->model::with(['organs' => function ($query) use ($bodyId, $organId) {
            $query->when($organId !== null && $organId !== -1, function ($query) use ($bodyId, $organId) {
                $query->where('id', $organId);
            });
        }, 'organs.sickness'])->findOrFail($bodyId);
    }

    /**
     * @param int $bodyId
     * @return BodyModel
     */
    public function findOrgans(int $bodyId): BodyModel
    {
        return $this->model::with(['organs' => function ($query)  {
            $query->orderBy('sort');
        }])->findOrFail($bodyId);
    }
}
