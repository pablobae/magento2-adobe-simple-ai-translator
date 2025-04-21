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

namespace Pablobae\SimpleAiTranslator\Service\Translator\Deepl;

use Exception;
use Pablobae\SimpleAiTranslator\Service\ConfigProvider;

class ApiParametersBuilder
{

    /**
     * Constructor
     *
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        private ConfigProvider $configProvider)
    {
    }

    /**
     * Build parameters with custom source and target languages
     *
     * @param string $sourceLanguage
     * @param string $targetLanguage
     * @param null|int|string $storeId
     * @return array
     */
    public function buildParametersByTargetLanguage(string $targetLanguage): array
    {
        $params = [];

        // Add target language (required)
        $params['target_lang'] = $targetLanguage;

        // Add source language if specified
        $sourceLanguage = $this->configProvider->getDeeplDefaultSourceLang();
        if (!empty($sourceLanguage)) {
            $params['source_lang'] = $sourceLanguage;
        }

        // Add model type if specified
        $modelType = $this->configProvider->getDeeplModelType();
        if (!empty($modelType)) {
            $params['model'] = $modelType;
        }

        // Add split sentences setting
        $splitSentences = $this->configProvider->getDeeplSplitSentences();
        if (!empty($splitSentences)) {
            $params['split_sentences'] = $splitSentences;
        }

        // Add preserve formatting if enabled
        if ($this->configProvider->isDeeplPreserveFormattingEnabled()) {
            $params['preserve_formatting'] = 1;
        }

        // Add formality if specified
        $formality = $this->configProvider->getDeeplFormality();
        if (!empty($formality) && $formality !== 'default') {
            $params['formality'] = $formality;
        }

        // Add tag handling if specified
        $tagHandling = $this->configProvider->getDeeplTagHandling();
        if (!empty($tagHandling)) {
            $params['tag_handling'] = $tagHandling;

            // Add XML-specific parameters if tag handling is XML
            if ($tagHandling === 'xml') {
                $this->addXmlParameters($params);
            }
        }

        // Add show billed characters if enabled
        if ($this->configProvider->isDeeplShowBilledCharactersEnabled()) {
            $params['show_billed_characters'] = 1;
        }

        return $params;
    }

    /**
     * Build DeepL API parameters based on store Id and configuration
     *
     * @param string|null $storeId
     * @return array
     * @throws Exception
     */
    public function buildParametersByStoreId(?string $storeId = null): array
    {
        $params = [];

        $apiKey = $this->configProvider->getDeeplApiKey($storeId);
        if (empty($apiKey)) {
            throw new Exception('Missing DeepL API key');
        }
        $params['auth_key'] = $apiKey;

        // Add source language if specified
        $sourceLanguage = $this->configProvider->getDeeplDefaultSourceLang($storeId);
        if (!empty($sourceLanguage)) {
            $params['source_lang'] = $sourceLanguage;
        }

        // Add target language (required)
        $targetLanguage = $this->configProvider->getDeeplDefaultTargetLang($storeId);
        if (!empty($targetLanguage)) {
            $params['target_lang'] = $targetLanguage;
        }

        // Add model type if specified
        $modelType = $this->configProvider->getDeeplModelType($storeId);
        if (!empty($modelType)) {
            $params['model'] = $modelType;
        }

        // Add split sentences setting
        $splitSentences = $this->configProvider->getDeeplSplitSentences($storeId);
        if (!empty($splitSentences)) {
            $params['split_sentences'] = $splitSentences;
        }

        // Add preserve formatting if enabled
        if ($this->configProvider->isDeeplPreserveFormattingEnabled($storeId)) {
            $params['preserve_formatting'] = 1;
        }

        // Add formality if specified
        $formality = $this->configProvider->getDeeplFormality($storeId);
        if (!empty($formality) && $formality !== 'default') {
            $params['formality'] = $formality;
        }

        // Add tag handling if specified
        $tagHandling = $this->configProvider->getDeeplTagHandling($storeId);
        if (!empty($tagHandling)) {
            $params['tag_handling'] = $tagHandling;

            // Add XML-specific parameters if tag handling is XML
            if ($tagHandling === 'xml') {
                $this->addXmlParameters($params, $storeId);
            }
        }

        // Add show billed characters if enabled
        if ($this->configProvider->isDeeplShowBilledCharactersEnabled($storeId)) {
            $params['show_billed_characters'] = 1;
        }

        return $params;
    }

    /**
     * Add XML-specific parameters
     *
     * @param array $params
     * @param null|int|string $storeId
     * @return void
     */
    private function addXmlParameters(array &$params, $storeId = null): void
    {
        // Add outline detection if enabled
        if ($this->configProvider->isDeeplOutlineDetectionEnabled($storeId)) {
            $params['outline_detection'] = 1;
        }

        // Add non-splitting tags if specified
        $nonSplittingTags = $this->configProvider->getDeeplNonSplittingTags($storeId);
        if (!empty($nonSplittingTags)) {
            $params['non_splitting_tags'] = $nonSplittingTags;
        }

        // Add splitting tags if specified
        $splittingTags = $this->configProvider->getDeeplSplittingTags($storeId);
        if (!empty($splittingTags)) {
            $params['splitting_tags'] = $splittingTags;
        }

        // Add ignore tags if specified
        $ignoreTags = $this->configProvider->getDeeplIgnoreTags($storeId);
        if (!empty($ignoreTags)) {
            $params['ignore_tags'] = $ignoreTags;
        }
    }
}
