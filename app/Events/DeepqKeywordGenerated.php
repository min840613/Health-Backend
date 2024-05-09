<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class DeepqKeywordGenerated
 * @package App\Events
 */
class DeepqKeywordGenerated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var User */
    private User $user;

    /** @var array */
    private array $questions;

    /** @var int|null */
    private ?int $id;

    /** @var string */
    private string $uuid;

    /**
     * @param User $user
     * @param int|null $id
     * @param array $questions
     * @param string $uuid
     */
    public function __construct(User $user, ?int $id, array $questions, string $uuid)
    {
        $this->user = $user;
        $this->id = $id;
        $this->questions = $questions;
        $this->uuid = $uuid;
    }

    /**
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        return new Channel("deepq-keyword.{$this->user->id}.{$this->uuid}");
    }

    /**
     * @return string
     */
    public static function broadcastAs(): string
    {
        return 'DeepqKeywordGenerated';
    }

    /**
     * @return array[]
     */
    public function broadcastWith(): array
    {
        return ['questions' => $this->questions];
    }
}
