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

use Pablobae\SimpleAiTranslator\Service\Translator\ChatGpt\ApiClient;
use Pablobae\SimpleAiTranslator\Service\Translator\ChatGpt\PromptBuilder;
use GuzzleHttp\Exception\GuzzleException;
use Magento\Framework\Exception\LocalizedException;
use Pablobae\SimpleAiTranslator\Api\TranslatorAdapterInterface;
use Pablobae\SimpleAiTranslator\Service\ConfigProvider;
use Psr\Log\LoggerInterface;

class ChatGptTranslator implements TranslatorAdapterInterface
{
    public function __construct(
        private readonly ConfigProvider $configProvider,
        private readonly ApiClient $apiClient,
        private readonly PromptBuilder $promptBuilder,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @inheritDoc
     */
    public function translate(string $text, string $storeId): string
    {
        try {
            $sourceLang = $this->configProvider->getChatGptDefaultSourceLang($storeId);
            $targetLang = $this->configProvider->getChatGptDefaultTargetLang($storeId);

            if (empty($targetLang)) {
                throw new LocalizedException(__('Target language is required for translation.'));
            }

            $prompt = $this->promptBuilder->buildTranslationPrompt($text, $targetLang, $sourceLang);
            $response = $this->apiClient->sendRequest($prompt);

            return $this->apiClient->extractTranslation($response);
        } catch (GuzzleException $e) {
            $this->logger->error('ChatGPT translation error: ' . $e->getMessage());
            throw new LocalizedException(__('Failed to get translation from ChatGPT API.'), $e);
        }
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    public function translateToLanguage(string $text, string $targetLang): string
    {
        try {
            $prompt = $this->promptBuilder->buildTranslationPrompt($text, $targetLang);
            $response = $this->apiClient->sendRequest($prompt);

            return $this->apiClient->extractTranslation($response);
        } catch (GuzzleException|LocalizedException $e) {
            $this->logger->error('ChatGPT translation error: ' . $e->getMessage());
            throw new LocalizedException(__('Failed to get translation from ChatGPT API.'), $e);
        }
    }
}
