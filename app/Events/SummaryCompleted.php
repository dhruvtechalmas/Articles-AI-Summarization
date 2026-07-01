<?php

namespace App\Events;

use App\Models\Article;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class SummaryCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Article $article;

    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('articles'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'summary.completed';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->article->id,
            'status' => $this->article->status,
            'summary' => $this->article->summary,
            'key_points' => $this->article->key_points,
        ];
    }
}