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

namespace Pablobae\SimpleAiTranslator\Service\Translator;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Pablobae\SimpleAiTranslator\Api\TranslatorAdapterInterface;
use Pablobae\SimpleAiTranslator\Service\Translator\Deepl\ApiClient;
use RuntimeException;

class DeeplTranslator implements TranslatorAdapterInterface
{
    public function __construct(
        private readonly ApiClient $apiClient
    ) {
    }

    /**
     * Translate text using DeepL API for a specific store
     *
     * @param string $text
     * @param string $storeId
     * @return string
     * @throws Exception
     */
    public function translate(string $text, string $storeId): string
    {
        try {
            $response = $this->apiClient->translateByStoreId($text, $storeId);
            return $response['translations'][0]['text'] ?? '';
        } catch (Exception|GuzzleException $e) {
            throw new RuntimeException('DeepL translation failed: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Translate text to a specific target language using DeepL API
     *
     * @param string $text
     * @param string $targetLang
     * @return string
     * @throws Exception
     */
    public function translateToLanguage(string $text, string $targetLang): string
    {
        try {
            $response = $this->apiClient->translateToTargetLanguage($text, $targetLang);
            return $response['translations'][0]['text'] ?? '';
        } catch (Exception|GuzzleException $e) {
            throw new RuntimeException('DeepL translation failed: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
