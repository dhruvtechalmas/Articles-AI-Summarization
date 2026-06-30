<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ArticleExtractorService
{
    /**
     * Download a webpage and extract clean plain text.
     */
    public function extractRawContent(string $url): ?string
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ])->timeout(15)->get($url);

            if (!$response->successful()) {
                Log::error("Failed to fetch article webpage. Status: " . $response->status());
                return null;
            }

            $html = $response->body();

            // Clean up HTML tags and extra white spaces
            $content = strip_tags($html);
            $content = preg_replace('/\s+/', ' ', $content);
            $encoding = mb_detect_encoding(
                $content,['UTF-8', 'ISO-8859-1', 'Windows-1252'],true);

            if ($encoding !== false) {
                $content = mb_convert_encoding($content, 'UTF-8', $encoding);
            }

            return trim(mb_substr($content, 0, 8000, 'UTF-8'));

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
        try {
            $apiKey = config('services.gemini.key');

            $cacheKey = 'article_summary_' . md5($url);

            if (!$apiKey) {
                Log::error("Gemini API Key is not configured correctly in services.php.");
                return null;
            }

            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }
            $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey;

            if (!mb_check_encoding($text, 'UTF-8')) {
                Log::error('Invalid UTF-8 detected before Gemini request.');
                return null;
            }

            $aiResponse = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($apiUrl, [
                        'contents' => [
                            [
                                'parts' => [
                                    [
                                        'text' => "Analyze the following text. You must return your entire response as a valid JSON object matching this exact schema shape:
                                {
                                    \"summary\": \"A clean summary of approximately 100 words\",
                                    \"key_points\": [\"point 1\", \"point 2\", \"point 3\", \"point 4\", \"point 5\"]
                                }
                                
                                Here is the text to analyze:\n\n" . $text
                                    ]
                                ]
                            ]
                        ]
                    ]);

            if (!$aiResponse->successful()) {
                Log::error("Gemini API Error. Status: " . $aiResponse->status() . " | Response: " . $aiResponse->body());
                return null;
            }
            $data = $aiResponse->json();

            $jsonText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$jsonText) {
                return null;
            }

            // Remove ```json ... ``` if Gemini returns markdown
            $jsonText = preg_replace('/^```json\s*|```$/m', '', $jsonText);

            $result = json_decode($jsonText, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Gemini JSON parse error: ' . json_last_error_msg());
                Log::error($jsonText);
                return null;
            }

            // Save result in cache for 24 hours
            Cache::put($cacheKey, $result, now()->addHours(24));

            return $result;

        } catch (\Exception $e) {
            Log::error("Exception in getAiAnalysis: " . $e->getMessage());
            return null;
        }
    }
}