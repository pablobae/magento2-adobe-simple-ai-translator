<?php
declare(strict_types=1);

namespace Pablobae\SimpleAiTranslator\Service\ChatGpt;

class PromptBuilder
{
    /**
     * Build translation prompt messages
     *
     * @param string $text
     * @param string|null $sourceLang
     * @param string $targetLang
     * @return array
     */
    public function buildTranslationPrompt(string $text, ?string $sourceLang, string $targetLang): array
    {
        $systemPrompt = 'You are a professional translator. Translate the text exactly as provided, maintaining any HTML or XML tags if present. Only return the translated text without any explanations or additional content.';
        
        $userPrompt = "Translate the following text to $targetLang";
        if ($sourceLang) {
            $userPrompt .= " from $sourceLang";
        }
        $userPrompt .= ":\n\n$text";

        return [
            [
                'role' => 'system',
                'content' => $systemPrompt
            ],
            [
                'role' => 'user',
                'content' => $userPrompt
            ]
        ];
    }
} 