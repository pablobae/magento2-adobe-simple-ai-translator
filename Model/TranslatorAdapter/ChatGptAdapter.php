<?php
declare(strict_types=1);

namespace Pablobae\SimpleAiTranslator\Model\TranslatorAdapter;

use GuzzleHttp\Exception\GuzzleException;
use Magento\Framework\Exception\LocalizedException;
use Pablobae\SimpleAiTranslator\Api\TranslatorAdapterInterface;
use Pablobae\SimpleAiTranslator\Service\ChatGpt\ApiClient;
use Pablobae\SimpleAiTranslator\Service\ChatGpt\PromptBuilder;
use Pablobae\SimpleAiTranslator\Service\ConfigProvider;
use Psr\Log\LoggerInterface;

class ChatGptAdapter implements TranslatorAdapterInterface
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
            $sourceLang = $this->configProvider->getChatGptDefaultSourceLang();
            $targetLang = $this->configProvider->getChatGptDefaultTargetLang($storeId);

            if (empty($targetLang)) {
                throw new LocalizedException(__('Target language is required for translation.'));
            }

            $prompt = $this->promptBuilder->buildTranslationPrompt($text, $sourceLang, $targetLang);
            $response = $this->apiClient->sendRequest($prompt);

            return $this->apiClient->extractTranslation($response);
        } catch (GuzzleException $e) {
            $this->logger->error('ChatGPT translation error: ' . $e->getMessage());
            throw new LocalizedException(__('Failed to get translation from ChatGPT API.'), $e);
        }
    }


    /**
     * @inheritDoc
     */
    public function translateToLanguage(string $text, string $targetLang): string
    {
        try {
            $sourceLang = $this->configProvider->getChatGptDefaultSourceLang();
            $prompt = $this->promptBuilder->buildTranslationPrompt($text, $sourceLang, $targetLang);
            $response = $this->apiClient->sendRequest($prompt);

            return $this->apiClient->extractTranslation($response);
        } catch (GuzzleException|LocalizedException $e) {
            $this->logger->error('ChatGPT translation error: ' . $e->getMessage());
            throw new LocalizedException(__('Failed to get translation from ChatGPT API.'), $e);
        }
    }
}
