<?php
declare(strict_types=1);

namespace Pablobae\SimpleAiTranslator\Service\Deepl;

use Pablobae\SimpleAiTranslator\Service\ConfigProvider;

class ApiParametersBuilder
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * Constructor
     *
     * @param ConfigProvider $configProvider
     */
    public function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    /**
     * Build DeepL API parameters based on configuration
     *
     * @param string|null $storeId
     * @return array
     */
    public function buildParameters(?string $storeId = null): array
    {
        $params = [];

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

    /**
     * Build parameters with custom source and target languages
     *
     * @param string $sourceLanguage
     * @param string $targetLanguage
     * @param null|int|string $storeId
     * @return array
     */
    public function buildParametersWithLanguages(string $sourceLanguage, string $targetLanguage, $storeId = null): array
    {
        $params = $this->buildParameters($storeId);

        // Override source language if provided
        if (!empty($sourceLanguage)) {
            $params['source_lang'] = $sourceLanguage;
        }

        // Override target language
        $params['target_lang'] = $targetLanguage;

        return $params;
    }
}
