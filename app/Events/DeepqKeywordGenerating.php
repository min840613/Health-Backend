<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class DeepqKeywordGenerating
 * @package App\Events
 */
class DeepqKeywordGenerating
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var User */
    private User $user;

    /** @var string  */
    private string $keyword;

    /** @var int */
    private int $count;

    /** @var int|null */
    private ?int $id;

    /** @var string */
    private $uuid;

    /**
     * @param User $user
     * @param array $data
     */
    public function __construct(User $user, array $data)
    {
        $this->user = $user;
        $this->keyword = $data['keyword'];
        $this->count = $data['count'];
        $this->id = $data['id'] ?? null;
        $this->uuid = $data['uuid'];
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getKeyword(): string
    {
        return $this->keyword;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }
}
