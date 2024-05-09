<?php

namespace App\Services;

use App\Repositories\DivisionsRepository;
use Illuminate\Support\Collection;

/**
 * Class DivisionsService
 * @package App\Services
 */
class DivisionsService
{
    /** @var DivisionsRepository */
    private DivisionsRepository $repository;

    /**
     * DivisionsService constructor.
     * @param DivisionsRepository $repository
     */
    public function __construct(DivisionsRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->repository->all();
    }

    /**
     * @param string $id
     * @param array $data
     * @return array|null
     */
    public function validate(string $id, array $data): ?array
    {
        $division = $this->repository->getById($id);

        if ($division !== null && $division->status != $data['status'] && $data['status'] == 0 && $division->masters->count() > 0) {
            return ['status' => '此科別已有專家使用，無法下架'];
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
     */
    public function create(array $data): void
    {
        $this->repository->create($data);
    }

    /**
     * @param array $data
     */
    public function update(array $data): void
    {
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
