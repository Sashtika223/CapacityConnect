<?php

namespace App\Services\Ai;

use App\Contracts\AiProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class HttpChatAiProvider implements AiProvider
{
    public function complete(string $systemPrompt, string $userMessage, array $history = []): string
    {
        $apiKey = config('services.ai.api_key');

        if (! is_string($apiKey) || $apiKey === '') {
            throw new RuntimeException('AI API key is not configured.');
        }

        $messages = array_merge(
            [['role' => 'system', 'content' => $systemPrompt]],
            $history,
            [['role' => 'user', 'content' => $userMessage]]
        );

        $response = Http::timeout((int) config('services.ai.timeout', 8))
            ->withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post((string) config('services.ai.endpoint'), [
                'model' => config('services.ai.model'),
                'messages' => $messages,
                'max_tokens' => 500,
            ]);

        if (! $response->successful()) {
            Log::warning('AI provider HTTP error', ['status' => $response->status()]);
            throw new RuntimeException('AI provider request failed.');
        }

        $answer = $response->json('choices.0.message.content');

        if (! is_string($answer) || trim($answer) === '') {
            throw new RuntimeException('AI provider returned an empty response.');
        }

        return trim($answer);
    }
}
