<?php

namespace App\Helpers;

use App\Helpers\Indexing\GoogleIndexingHelper;
use App\Helpers\Indexing\IndexingInterface;
use App\Helpers\Indexing\LogIndexingHelper;

/**
 * Class IndexingHelper
 * @package App\Helpers
 */
class IndexingHelper
{
    /** @var IndexingInterface */
    private IndexingInterface $driver;

    public function __construct()
    {
        $this->driver = $this->driverFactory(config('indexing.default'));
    }

    /**
     * @param string $driver
     * @return IndexingInterface
     */
    public function driver(string $driver): IndexingInterface
    {
        $this->driver = $this->driverFactory($driver);

        return $this->driver;
    }

    /**
     * @param string $driver
     * @return IndexingInterface
     */
    private function driverFactory(string $driver): IndexingInterface
    {
        return match ($driver) {
            'google' => app(GoogleIndexingHelper::class),
            'log' => app(LogIndexingHelper::class),
        };
    }

    /**
     * @param string $name
     * @param $arguments
     * @return mixed
     */
    public function __call(string $name, $arguments)
    {
        return $this->driver->$name(...$arguments);
    }
}
