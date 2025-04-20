<?php
declare(strict_types=1);

namespace Pablobae\SimpleAiTranslator\Test\Unit\Service\Translator\Deepl;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Pablobae\SimpleAiTranslator\Service\ConfigProvider;
use Pablobae\SimpleAiTranslator\Service\Translator\Deepl\ApiClient;
use Pablobae\SimpleAiTranslator\Service\Translator\Deepl\ApiParametersBuilder;

class ApiClientTest extends TestCase
{
    private ConfigProvider $mockConfigProvider;
    private ApiParametersBuilder $mockApiParametersBuilder;
    private Client $mockGuzzleClient;
    private ApiClient $apiClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockConfigProvider = $this->createMock(ConfigProvider::class);
        $this->mockApiParametersBuilder = $this->createMock(ApiParametersBuilder::class);
        $this->mockGuzzleClient = $this->createMock(Client::class);

        $this->apiClient = new ApiClient(
            $this->mockConfigProvider,
            $this->mockApiParametersBuilder,
            $this->mockGuzzleClient
        );
    }

    /**
     * Test translateByStoreId method success.
     *
     * @throws GuzzleException
     */
    public function testTranslateByStoreId(): void
    {
        $storeId = '1';
        $text = 'Hello';
        $apiDomain = 'api.deepl.com';
        $timeout = 10;
        $parameters = ['auth_key' => 'dummy-key', 'text' => $text, 'target_lang' => 'DE'];
        $responseBody = json_encode(['translations' => [['text' => 'Hallo']]]);
        $expectedResult = ['translations' => [['text' => 'Hallo']]];

        // Mock ConfigProvider
        $this->mockConfigProvider->method('getDeeplApiDomain')->with($storeId)->willReturn($apiDomain);
        $this->mockConfigProvider->method('getDeeplRequestTimeout')->with($storeId)->willReturn($timeout);

        // Mock ApiParametersBuilder
        $this->mockApiParametersBuilder->method('buildParametersByStoreId')->with($storeId)->willReturn(['auth_key' => 'dummy-key', 'target_lang' => 'DE']);

        // Mock Guzzle Response
        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')->willReturn($this->createConfiguredMock('Psr\Http\Message\StreamInterface', ['getContents' => $responseBody]));
        $this->mockGuzzleClient->method('post')->with("https://$apiDomain/v2/translate", [
            'form_params' => $parameters,
            'timeout' => $timeout,
        ])->willReturn($mockResponse);

        // Act
        $result = $this->apiClient->translateByStoreId($text, $storeId);

        // Assert
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test translateToTargetLanguage method success.
     *
     * @throws GuzzleException
     */
    public function testTranslateToTargetLanguage(): void
    {
        $text = 'Goodbye';
        $targetLang = 'FR';
        $apiDomain = 'api.deepl.com';
        $timeout = 10;
        $parameters = ['auth_key' => 'dummy-key', 'text' => $text, 'target_lang' => $targetLang];
        $responseBody = json_encode(['translations' => [['text' => 'Au revoir']]]);
        $expectedResult = ['translations' => [['text' => 'Au revoir']]];

        // Mock ConfigProvider
        $this->mockConfigProvider->method('getDeeplApiDomain')->willReturn($apiDomain);
        $this->mockConfigProvider->method('getDeeplRequestTimeout')->willReturn($timeout);

        // Mock ApiParametersBuilder
        $this->mockApiParametersBuilder->method('buildParametersByTargetLanguage')->with($targetLang)->willReturn(['auth_key' => 'dummy-key', 'target_lang' => $targetLang]);

        // Mock Guzzle Response
        $mockResponse = $this->createMock(Response::class);
        $mockResponse->method('getBody')->willReturn($this->createConfiguredMock('Psr\Http\Message\StreamInterface', ['getContents' => $responseBody]));
        $this->mockGuzzleClient->method('post')->with("https://$apiDomain/v2/translate", [
            'form_params' => $parameters,
            'timeout' => $timeout,
        ])->willReturn($mockResponse);

        // Act
        $result = $this->apiClient->translateToTargetLanguage($text, $targetLang);

        // Assert
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test that translateByStoreId throws an exception when the API domain is missing.
     */
    public function testTranslateByStoreIdThrowsExceptionForMissingApiDomain(): void
    {
        $text = 'Hola';
        $storeId = '1';

        $this->mockConfigProvider->method('getDeeplApiDomain')->with($storeId)->willReturn('');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Missing DeepL API domain configuration');

        $this->apiClient->translateByStoreId($text, $storeId);
    }
}
