<?php

namespace App\Contracts;

interface AiProvider
{
    /**
     * @param  array<int, array{role: string, content: string}>  $history
     */
    public function complete(string $systemPrompt, string $userMessage, array $history = []): string;
}
