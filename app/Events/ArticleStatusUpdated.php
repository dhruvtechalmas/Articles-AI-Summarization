<?php

namespace App\Events;

use App\Models\Article;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ArticleStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Article $article;

    /**
     * Create a new event instance.
     */
    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    /**
     * Broadcast on a public channel.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('articles'),
        ];
    }

    /**
     * Custom event name.
     */
    public function broadcastAs(): string
    {
        return 'article.status.updated';
    }

    /**
     * Data sent to the browser.
     */
    public function broadcastWith(): array
    {
         \Log::info('Broadcast Event Fired');
         
        return [
            'id' => $this->article->id,
            'status' => $this->article->status,
            'summary' => $this->article->summary,
            'key_points' => $this->article->key_points,
        ];
    }
}