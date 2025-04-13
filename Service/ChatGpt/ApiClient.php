<?php
declare(strict_types=1);

namespace Pablobae\SimpleAiTranslator\Service\ChatGpt;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Magento\Framework\Exception\LocalizedException;
use Pablobae\SimpleAiTranslator\Service\ConfigProvider;
use Psr\Log\LoggerInterface;

class ApiClient
{
    private const API_ENDPOINT = 'https://api.openai.com/v1/chat/completions';

    public function __construct(
        private readonly Client $httpClient,
        private readonly ConfigProvider $configProvider,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * Send request to ChatGPT API
     *
     * @param array $messages
     * @return array
     * @throws LocalizedException
     * @throws GuzzleException
     */
    public function sendRequest(array $messages): array
    {
        $response = $this->httpClient->post(
            self::API_ENDPOINT,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->configProvider->getChatGptApiKey(),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $this->configProvider->getChatGptModel(),
                    'messages' => $messages,
                    'temperature' => $this->configProvider->getChatGptTemperature(),
                ],
                'timeout' => $this->configProvider->getChatGptRequestTimeout()
            ]
        );

        $result = json_decode($response->getBody()->getContents(), true);

        if (!isset($result['choices'][0]['message']['content'])) {
            $this->handleApiError($result);
        }

        return $result;
    }

    /**
     * Extract translation from API response
     *
     * @param array $response
     * @return string
     */
    public function extractTranslation(array $response): string
    {
        return trim($response['choices'][0]['message']['content']);
    }

    private function handleApiError(array $result): never
    {
        $error = $result['error'] ?? null;
        $errorMessage= $error['message'] ?? __('Unknown error occurred while calling ChatGPT API.');

        $this->logger->error('ChatGPT API Error: ' . json_encode($result));
        throw new LocalizedException(__($errorMessage));
    }
} 