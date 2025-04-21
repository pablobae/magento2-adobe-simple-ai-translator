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
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Pablobae\SimpleAiTranslator\Service\ConfigProvider;

class ApiClient
{
    const DEEPL_TRANSLATE_ENDPOINT = 'v2/translate';

    public function __construct(
        private readonly ConfigProvider $configProvider,
        private readonly ApiParametersBuilder $apiParametersBuilder,
        private readonly Client $guzzleClient,
    ) {
    }

    /**
     * Translate the text using the DeepL API by store ID.
     *
     * @param string $text
     * @param string $storeId
     * @return array
     * @throws GuzzleException
     * @throws Exception
     */
    public function translateByStoreId(string $text, string $storeId): array
    {
        $parameters = $this->apiParametersBuilder->buildParametersByStoreId($storeId);
        $parameters['text'] = $text;

        return $this->sendRequest(self::DEEPL_TRANSLATE_ENDPOINT, $parameters, $storeId);
    }

    /**
     * Translate the text to a specific target language using the DeepL API.
     *
     * @param string $text
     * @param string $targetLang
     * @return array
     * @throws GuzzleException
     */
    public function translateToTargetLanguage(string $text, string $targetLang): array
    {
        $parameters = $this->apiParametersBuilder->buildParametersByTargetLanguage($targetLang);
        $parameters['text'] = $text;
        $parameters['target_lang'] = $targetLang;

        return $this->sendRequest(self::DEEPL_TRANSLATE_ENDPOINT, $parameters);
    }

    /**
     * Internal method to send a POST request to the DeepL API.
     *
     * @param string $endpointBase
     * @param array $parameters
     * @param string|null $storeId
     * @return array
     * @throws GuzzleException
     */
    private function sendRequest(string $endpointBase, array $parameters, ?string $storeId = null): array
    {
        $apiDomain = $this->configProvider->getDeeplApiDomain($storeId);
        if (empty($apiDomain)) {
            throw new \RuntimeException('Missing DeepL API domain configuration');
        }

        $url = "https://{$apiDomain}/{$endpointBase}";

        $response = $this->guzzleClient->post($url, [
            'form_params' => $parameters,
            'timeout' => $this->configProvider->getDeeplRequestTimeout($storeId),
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
