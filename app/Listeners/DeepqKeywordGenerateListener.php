<?php

namespace App\Listeners;

use App\Events\DeepqKeywordGenerated;
use App\Events\DeepqKeywordGenerating;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Http;

/**
 * Class DeepqKeywordGenerateListener
 * @package App\Listeners
 */
class DeepqKeywordGenerateListener implements ShouldQueue
{
    /** @var int */
    public $timeout = 120;

    /**
     * @param DeepqKeywordGenerating $event
     * @return void
     * @throws \Throwable
     */
    public function handle(DeepqKeywordGenerating $event): void
    {
        $data = [
            'source' => 'health2.0',
            'max_num_questions' => $event->getCount(),
            'content' => collect($event->getKeyword())->map(fn($item) => array_map('trim', explode(',', $item)))->flatten()->toArray(),
        ];

        $response = Http::withToken(config('deepq.api.token'), 'Basic')
            ->timeout(120)
            ->post(config('deepq.api.article_recommend_url'), $data);

        if ($response->failed()) {
            \Log::alert('DeepQ 取得資料失敗');
        }

        event(new DeepqKeywordGenerated($event->getUser(), $event->getId(), $response->json('questions') ?? [], $event->getUuid()));
    }
}
