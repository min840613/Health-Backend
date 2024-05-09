<?php

namespace App\Services;

use App\Models\Encyclopedia\BodyModel;
use App\Repositories\BodyRepository;
use App\Repositories\OrgansRepository;
use Illuminate\Support\Collection;


class OrgansService
{
    /** @var OrgansRepository */
    private OrgansRepository $repository;

    /** @var BodyRepository */
    private BodyRepository $bodyRepository;

    /**
     * OrgansService constructor.
     * @param OrgansRepository $repository
     * @param BodyRepository $bodyRepository
     */
    public function __construct(OrgansRepository $repository, BodyRepository $bodyRepository)
    {
        $this->repository = $repository;
        $this->bodyRepository = $bodyRepository;
    }

    /**
     * @param array $data
     * @return BodyModel
     */
    public function findOrgans(array $data): BodyModel
    {
        return $this->bodyRepository->findOrgans($data['body_id']);
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
     */
    public function saveSort(array $data): void
    {
        $index = 0;
        collect($data)->pluck('id')->each(function($organsId) use(&$index){
            $index++;
            $this->repository->saveSort($organsId, $index);
        });
    }

    /**
     * @param array $data
     */
    public function create($data): void
    {
        $this->repository->create($data);
    }

    /**
     * @param array $data
     */
    public function update($data): void
    {
        $this->repository->update($data);
    }
}
