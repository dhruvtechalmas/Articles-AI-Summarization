<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\DomCrawler\Crawler;

class ArticleExtractorService
{
    /**
     * Download a webpage and extract clean plain text.
     */
    public function extractRawContent(string $url): ?string
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'ArticleSummarizer/1.0 (contact: dhruvtechalmas@gmail.com)',
            ])->timeout(15)->get($url);

            if (!$response->successful()) {
                Log::error("Failed to fetch article webpage. Status: " . $response->status());
                return null;
            }

            $html = $response->body();

            $crawler = new Crawler($html);

            $content = '';

            if ($crawler->filter('#mw-content-text p')->count()) {

                foreach ($crawler->filter('#mw-content-text p') as $node) {

                    $content .= ' ' . $node->textContent;

                }

            } elseif ($crawler->filter('article p')->count()) {

                foreach ($crawler->filter('article p') as $node) {

                    $content .= ' ' . $node->textContent;

                }

            } elseif ($crawler->filter('main p')->count()) {

                foreach ($crawler->filter('main p') as $node) {

                    $content .= ' ' . $node->textContent;

                }

            } else {

                $content = strip_tags($html);

            }

            $content = preg_replace('/\s+/', ' ', $content);

            $content = trim($content);

            return trim(mb_substr($content, 0, 8000));

        } catch (\Exception $e) {
            Log::error("Exception in extractRawContent: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Send raw text to Gemini and return a structured array with summary and key points.
     */
    public function getAiAnalysis(string $text, string $url): ?array
    {
        $cacheKey = 'article_summary_' . md5($url);

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Try Gemini first
        $result = $this->getGeminiAnalysis($text);

        // If Gemini fails, try Groq
        if (!$result) {

            Log::info('Gemini failed. Trying Groq...');

            $result = $this->getGroqAnalysis($text);

        }

        if ($result) {
            Cache::put($cacheKey, $result, now()->addHours(24));
        }

        return $result;
    }

    private function getGeminiAnalysis(string $text): ?array
    {
        try {

            $apiKey = config('services.gemini.key');

            $response = Http::post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey,
                [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => 'Return ONLY valid JSON.

                                    {
                                    "summary":"100-word summary",
                                    "key_points":[
                                    "Point 1",
                                    "Point 2",
                                    "Point 3",
                                    "Point 4",
                                    "Point 5"
                                    ]
                                    }

                                    Article:

                                    ' . $text
                                ]
                            ]
                        ]
                    ]
                ]
            );

            if (!$response->successful()) {

                Log::error('Gemini Error: ' . $response->body());

                return null;

            }

            $data = $response->json();

            $json = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$json) {
                return null;
            }

            $json = preg_replace('/^```json\s*|```$/m', '', $json);

            return json_decode($json, true);

        } catch (\Exception $e) {

            Log::error($e->getMessage());

            return null;

        }
    }

    private function getGroqAnalysis(string $text): ?array
    {
        try {

            $response = Http::withToken(config('services.groq.key'))

                ->post(
                    'https://api.groq.com/openai/v1/chat/completions',
                    [

                        'model' => 'llama-3.3-70b-versatile',

                        'messages' => [

                            [
                                'role' => 'system',
                                'content' => 'You are an expert article summarizer.'
                            ],

                            [
                                'role' => 'user',
                                'content' =>
                                    'Return ONLY valid JSON.

                                {
                                "summary":"100-word summary",
                                "key_points":[
                                "Point 1",
                                "Point 2",
                                "Point 3",
                                "Point 4",
                                "Point 5"
                                ]
                                }

                                Article:

                                ' . $text
                            ]

                        ],

                        'temperature' => 0.3

                    ]
                );

            if (!$response->successful()) {

                Log::error('Groq Error: ' . $response->body());

                return null;

            }

            $data = $response->json();

            $json = $data['choices'][0]['message']['content'] ?? null;

            if (!$json) {

                return null;

            }

            $json = preg_replace('/^```json\s*|```$/m', '', $json);

            $result = json_decode($json, true);

            if (json_last_error() !== JSON_ERROR_NONE) {

                Log::error(json_last_error_msg());

                return null;

            }

            return $result;

        } catch (\Exception $e) {

            Log::error($e->getMessage());

            return null;

        }
    }
}