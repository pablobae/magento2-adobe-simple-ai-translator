<?php
declare(strict_types=1);

namespace Pablobae\SimpleAiTranslator\Service\Translator;

use Pablobae\SimpleAiTranslator\Service\Translator\Deepl\ApiParametersBuilder;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Pablobae\SimpleAiTranslator\Api\TranslatorAdapterInterface;
use Pablobae\SimpleAiTranslator\Service\ConfigProvider;

class DeeplTranslator implements TranslatorAdapterInterface
{
    /**
     * @param ConfigProvider $configProvider
     * @param ApiParametersBuilder $apiParametersBuilder
     * @param Client $guzzleClient
     */
    public function __construct(
        private readonly ConfigProvider $configProvider,
        private readonly ApiParametersBuilder $apiParametersBuilder,
        private readonly Client $guzzleClient,
    ) {
    }

    /**
     * Translate the provided text
     *
     * @param string $text
     * @param string $storeId
     * @return string
     * @throws Exception
     */
    public function translate(string $text, string $storeId): string
    {
        $apiKey = $this->configProvider->getDeeplApiKey($storeId);
        if (empty($apiKey)) {
            throw new Exception('Missing DeepL API key');
        }

        $apiDomain = $this->configProvider->getDeeplApiDomain($storeId);
        if (empty($apiDomain)) {
            throw new Exception('Missing DeepL API domain configuration');
        }

        // Get API parameters using the builder
        $parameters = $this->apiParametersBuilder->buildParametersByStoreId($storeId);

        // Add the text to translate and auth key
        $parameters['auth_key'] = $apiKey;
        $parameters['text'] = $text;

        // Ensure target_lang is set
        if (!isset($parameters['target_lang'])) {
            // Get target language from store configuration
            $targetLanguage = $this->getTargetLanguageForStore($storeId);
            $parameters['target_lang'] = $targetLanguage;
        }

        try {
            $response = $this->guzzleClient->post("https://{$apiDomain}/v2/translate", [
                'form_params' => $parameters,
                'timeout' => $this->configProvider->getDeeplRequestTimeout($storeId),
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);
            return $responseBody['translations'][0]['text'] ?? '';
        } catch (GuzzleException $e) {
            throw new \RuntimeException('DeepL API error: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get target language for the specified store
     *
     * @param string $storeId
     * @return string
     */
    private function getTargetLanguageForStore(string $storeId): string
    {
        // First try to get the configured default target language
        $configuredLanguage = $this->configProvider->getDeeplDefaultTargetLang($storeId);
        if (!empty($configuredLanguage)) {
            return $configuredLanguage;
        }

        // If not configured, derive from store locale
        $locale = $this->configProvider->getStoreLocale($storeId);
        if (empty($locale)) {
            // Default to English if no locale is found
            return 'EN-US';
        }

        return $this->getTargetLanguageFromLocale($locale);
    }

    /**
     * Convert Magento locale code to DeepL language code
     *
     * @param string $locale
     * @return string
     */
    private function getTargetLanguageFromLocale(string $locale): string
    {
        // Extract language part from locale (e.g., 'en_US' becomes 'en')
        $language = strtolower(substr($locale, 0, 2));

        // Map of special cases where locale doesn't directly map to DeepL language code
        $localeMap = [
            'en' => 'EN-US', // Default English to American English
            'pt' => 'PT-BR', // Default Portuguese to Brazilian Portuguese
            // Add more mappings as needed
        ];

        // Return mapped language or uppercase the language code
        return $localeMap[$language] ?? strtoupper($language);
    }

    public function translateToLanguage(string $text, string $targetLang): string
    {
        $apiKey = $this->configProvider->getDeeplApiKey();
        if (empty($apiKey)) {
            throw new Exception('Missing DeepL API key');
        }

        $apiDomain = $this->configProvider->getDeeplApiDomain();
        if (empty($apiDomain)) {
            throw new Exception('Missing DeepL API domain configuration');
        }

        // Get API parameters using the builder
        $parameters = $this->apiParametersBuilder->buildParametersByTargetLanguage($targetLang);

        // Add the text to translate and auth key
        $parameters['auth_key'] = $apiKey;
        $parameters['text'] = $text;
        $parameters['target_lang'] = $targetLang;

        try {
            $response = $this->guzzleClient->post("https://{$apiDomain}/v2/translate", [
                'form_params' => $parameters,
                'timeout' => $this->configProvider->getDeeplRequestTimeout(),
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);
            return $responseBody['translations'][0]['text'] ?? '';
        } catch (GuzzleException $e) {
            throw new \RuntimeException('DeepL API error: ' . $e->getMessage(), 0, $e);
        }
    }
}
