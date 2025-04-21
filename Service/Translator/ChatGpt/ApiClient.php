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
     */
    public function sendRequest(array $messages): array
    {
        try {
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
        } catch (GuzzleException $e) {
            $this->handleGuzzleException($e);
        }
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
        $errorMessage = $error['message'] ?? __('Unknown error occurred while calling ChatGPT API.');

        $this->logger->error('ChatGPT API Error: ' . json_encode($result));
        throw new LocalizedException(__($errorMessage));
    }

    /**
     * Handle Guzzle exceptions
     *
     * @param GuzzleException $e
     * @return never
     * @throws LocalizedException
     */
    private function handleGuzzleException(GuzzleException $e): never
    {
        $this->logger->error('ChatGPT API Error: ' . $e->getMessage());

        $errorMessage = 'ChatGPT API Error. Failed to get response from ChatGPT API.';

        // Extract error message from response if available
        if ($e instanceof RequestException && $e->hasResponse()) {
            try {
                $responseBody = json_decode($e->getResponse()->getBody()->getContents(), true);
                if (isset($responseBody['error']['message'])) {
                    $errorMessage .= ': '.$responseBody['error']['message'];
                }
            } catch (\Exception $jsonException) {
                // If we can't parse the response, use the default message
                $this->logger->error('Failed to parse error response: ' . $jsonException->getMessage());
            }
        }

        throw new LocalizedException(__($errorMessage), $e);
    }
}
