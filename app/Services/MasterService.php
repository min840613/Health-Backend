<?php

namespace App\Services;

use App\Models\Masters\DivisionsModel;
use App\Models\Masters\InstitutionsModel;
use App\Models\Masters\MastersBannerModel;
use App\Models\Masters\MastersModel;
use App\Repositories\MasterRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Master;

/**
 * Class MasterService
 * @package App\Services
 */
class MasterService
{
    /** @var MasterRepository */
    private MasterRepository $repository;

    /**
     * MasterService constructor.
     * @param MasterRepository $repository
     */
    public function __construct(MasterRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string $expertEnName
     * @return MastersModel|null
     */
    public function show(string $expertEnName): ?MastersModel
    {
        return $this->repository->getByEnName($expertEnName);
    }

    /**
     * @param int $count
     * @return Illuminate\Database\Eloquent\Collection|MastersBannerModel[]
     */
    public function banner($count): Collection
    {
        return $this->repository->getBanners($count);
    }

    /**
     * @param int $count
     * @return Array
     */
    public function expertiseKeywordRandom($count): array
    {
        return $this->repository->getExpertiseKeywordRandom($count);
    }

    /**
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function conditions(): Collection
    {
        $allDivisionWithMasters = DivisionsModel::active()->where('type', 2)->first();
        $allDivisionWithMasters->masters = MastersModel::active()->isDoctor()->get();
        $allDivisionWithMasters->institutions = $this->repository->getInstitutionsWithMasterCountByDivisionId();

        $divisionsWithMasters = $this->repository->getDivisionsWithMasters();
        foreach ($divisionsWithMasters as $key => $val) {
            $institutionsByDivisionId = $this->repository->getInstitutionsWithMasterCountByDivisionId($val->id);
            $divisionsWithMasters[$key]->institutions = $institutionsByDivisionId;
        }
        $divisionsWithMasters->prepend($allDivisionWithMasters);

        return $divisionsWithMasters;
    }

    /**
     * @return Illuminate\Database\Eloquent\Collection|DivisionsModel[]
     */
    public function divisions(): Collection
    {
        return $this->repository->getDivisions();
    }

    /**
     * @param bool $isCentre
     * @return Collection
     */
    public function institutions(bool $isCentre = false): Collection
    {
        return $isCentre ? $this->repository->getInstitutionsIsCentre() : $this->repository->getInstitutions();
    }

    /**
     * @return Illuminate\Database\Eloquent\Collection|MastersModel[]
     */
    public function masters(): Collection
    {
        return $this->repository->getMasters();
    }

    /**
     * @param array $search
     * @return Illuminate\Pagination\LengthAwarePaginator|MastersModel[]
     */
    public function experts($search): LengthAwarePaginator
    {
        return $this->repository->getExpertsByConditions($search);
    }

    /**
     * @param array $search
     * @return LengthAwarePaginator
     */
    public function newArticles($count): LengthAwarePaginator
    {
        return $this->repository->getNewArticles($count);
    }
}
