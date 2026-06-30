<?php

namespace App\Jobs;

use App\Models\Article;
use App\Services\ArticleExtractorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

class ProcessArticleJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;
    public $backoff = 15;

    /**
     * Create a new job instance.
     */
    public function __construct(public Article $article)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 1. Mark status as processing
        $this->article->update([
            'status' => 'processing',
        ]);

        $extractor = new ArticleExtractorService();

        // 2. Step A: Get Raw Text
        $rawContent = $extractor->extractRawContent($this->article->url);

        if (!$rawContent) {
            $this->article->update(['status' => 'failed']);
            return;
        }

        // 3. Step B: Get AI JSON Data
        $aiData = $extractor->getAiAnalysis(
            $rawContent,
            $this->article->url
        );

        if (!$aiData || empty($aiData['summary']) || empty($aiData['key_points'])) {
            // Retry if Google's API transiently failed (503)
            if ($this->attempts() < $this->tries) {
                $this->release($this->backoff);
                return;
            }

            $this->article->update(['status' => 'failed']);
            return;
        }

        // 4. Save everything to the database cleanly
        $this->article->update([
            'content' => $rawContent,          // Stores your original longText content
            'summary' => $aiData['summary'],    // Stores the 100-word summary
            'key_points' => $aiData['key_points'], // Stores the array of 5 points
            'status' => 'completed',
        ]);
    }
}