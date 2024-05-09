<?php

namespace App\Helpers\Indexing;

class LogIndexingHelper implements IndexingInterface
{
    /**
     * @param string $url
     * @return array
     */
    public function get(string $url): array
    {
        \Log::debug('indexing get log.', ['url' => $url]);

        return [];
    }

    /**
     * @param string $url
     * @return array
     */
    public function create(string $url): array
    {
        \Log::debug('indexing create log.', ['url' => $url]);

        return [];
    }

    /**
     * @param string $url
     * @return array
     */
    public function update(string $url): array
    {
        \Log::debug('indexing update log.', ['url' => $url]);

        return [];
    }

    /**
     * @param string $url
     * @return array
     */
    public function delete(string $url): array
    {
        \Log::debug('indexing delete log.', ['url' => $url]);

        return [];
    }
}
