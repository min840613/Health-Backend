<?php

namespace App\Services;

use App\Repositories\InstitutionsRepository;
use Illuminate\Support\Collection;

/**
 * Class InstitutionsService
 * @package App\Services
 */
class InstitutionsService
{
    /** @var InstitutionsRepository */
    private InstitutionsRepository $repository;

    /**
     * InstitutionsService constructor.
     * @param InstitutionsRepository $repository
     */
    public function __construct(InstitutionsRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param array $data
     * @return Collection
     */
    public function lists(array $data): Collection
    {
        return $this->repository->lists($data);
    }

    /**
     * @param string $id
     * @param array $data
     * @return array|null
     */
    public function validate(string $id, array $data): ?array
    {
        $institution = $this->repository->getById($id);

        if ($institution === null) {
            return null;
        }

        if ($institution->status != $data['status'] && $data['status'] == 0) {
            if ($institution->masters !== null && $institution->masters->isNotEmpty()) {
                return ['status' => '此醫療院所已有專家使用，無法下架'];
            }
        }

        return $this->validateUniqueEnName($data, $id);
    }

    /**
     * @param array $data
     * @param string|null $id
     * @return string[]|null
     */
    public function validateUniqueEnName(array $data, ?string $id): ?array
    {
        if ($this->repository->getByEnName($data['en_name'], [$id])->isNotEmpty()) {
            return ['status' => '此英文名稱已被使用，請更換英文名稱'];
        }

        return null;
    }

    /**
     * @param array $data
     * @param string $username
     */
    public function create(array $data, string $username): void
    {
        $data['created_user'] = $username;
        $data['updated_user'] = $username;

        $this->repository->create($data);
    }

    /**
     * @param array $data
     * @param string $username
     */
    public function update(array $data, string $username): void
    {
        $data['updated_user'] = $username;

        $this->repository->update($data);
    }

    /**
     * @param array $data
     */
    public function saveSort(array $data): void
    {
        $index = 0;
        collect($data)->pluck('id')->each(function ($sortId) use (&$index) {
            $index++;
            $this->repository->saveSort($sortId, $index);
        });
    }
}
