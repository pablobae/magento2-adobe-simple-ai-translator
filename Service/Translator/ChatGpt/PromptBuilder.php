<?php
declare(strict_types=1);

namespace Pablobae\SimpleAiTranslator\Service\Translator\ChatGpt;

class PromptBuilder
{
    /**
     * Build translation prompt messages
     *
     * @param string $text
     * @param string $targetLang
     * @param string|null $sourceLang
 * @return array
     */
    public function buildTranslationPrompt(string $text, string $targetLang, string $sourceLang = null): array
    {
        $systemPrompt = 'You are a professional translator. Translate the text exactly as provided, maintaining any HTML or XML tags if present. Only return the translated text without any explanations or additional content. The text to be translated should be added between /// and ///. Do not include the /// in the translation.';

        $userPrompt = "Translate the following text to $targetLang";
        if ($sourceLang) {
            $userPrompt .= " from $sourceLang";
        }
        $userPrompt .= ": ///$text///";

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
