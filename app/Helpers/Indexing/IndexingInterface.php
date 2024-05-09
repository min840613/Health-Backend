<?php

namespace App\Helpers\Indexing;

interface IndexingInterface
{
    public function get(string $url): array;

    public function create(string $url): array;

    public function update(string $url): array;

    public function delete(string $url): array;
}
