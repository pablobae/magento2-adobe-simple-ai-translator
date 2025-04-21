<?php
/**
 * SimpleAiTranslator
 *
 * Copyright (C) 2025 Pablo César Baenas Castelló - https://www.pablobaenas.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

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
