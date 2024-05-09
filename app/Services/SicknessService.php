<?php

namespace App\Services;

use App\Models\Encyclopedia\BodyModel;
use App\Repositories\ArticlesRepository;
use App\Repositories\BodyRepository;
use App\Repositories\SicknessRepository;
use Illuminate\Support\Collection;

/**
 * Class SicknessService
 * @package App\Services
 */
class SicknessService
{
    /** @var SicknessRepository */
    private SicknessRepository $repository;

    /** @var BodyRepository */
    private BodyRepository $bodyRepository;

    /**
     * SicknessService constructor.
     * @param SicknessRepository $repository
     * @param BodyRepository $bodyRepository
     */
    public function __construct(SicknessRepository $repository, BodyRepository $bodyRepository)
    {
        $this->repository = $repository;
        $this->bodyRepository = $bodyRepository;
    }

    /**
     * @return Collection
     */
    public function filterByBodies(): Collection
    {
        return $this->bodyRepository->filterWithOrgans();
    }

    /**
     * @param array $data
     * @return BodyModel
     */
    public function findByBodyAndOrgan(array $data): BodyModel
    {
        return $this->bodyRepository->findByBodyAndOrgan($data['body_id'], $data['organ_id'] ?? null);
    }

    /**
     * @param array $sickness
     */
    public function saveSort(array $sickness): void
    {
        $index = 0;
        collect($sickness)->pluck('id')->each(function($sicknessId) use(&$index){
            $index++;
            $this->repository->saveSort($sicknessId, $index);
        });
    }

    /**
     * @param array $sickness
     */
    public function create($data): void
    {
        $this->repository->create($data);
    }

    /**
     * @param array $sickness
     */
    public function update($data): void
    {
        $this->repository->update($data);
    }
}
