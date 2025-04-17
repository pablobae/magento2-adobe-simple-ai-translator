<?php
declare(strict_types=1);

namespace Pablobae\SimpleAiTranslator\Test\Unit\Model\TranslatorAdapter;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Pablobae\SimpleAiTranslator\Model\TranslatorAdapter\DeeplAdapter;
use Pablobae\SimpleAiTranslator\Service\ConfigProvider;
use Pablobae\SimpleAiTranslator\Service\Deepl\ApiParametersBuilder;

class DeeplAdapterTest extends TestCase
{
    /**
     * @var DeeplAdapter
     */
    private DeeplAdapter $adapter;

    /**
     * @var ConfigProvider|\PHPUnit\Framework\MockObject\MockObject
     */
    private $configProvider;

    /**
     * @var ApiParametersBuilder|\PHPUnit\Framework\MockObject\MockObject
     */
    private $apiParametersBuilder;

    /**
     * @var Client
     */
    private Client $guzzleClient;

    /**
     * @var MockHandler
     */
    private MockHandler $mockHandler;

    protected function setUp(): void
    {
        $this->configProvider = $this->getMockBuilder(ConfigProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->apiParametersBuilder = $this->getMockBuilder(ApiParametersBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Set up Guzzle mock
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $this->guzzleClient = new Client(['handler' => $handlerStack]);

        $this->adapter = new DeeplAdapter(
            $this->configProvider,
            $this->apiParametersBuilder,
            $this->guzzleClient
        );
    }

    public function testTranslateSuccess(): void
    {
        $storeId = '0';
        $text = 'Hello world';
        $apiKey = 'test-api-key';
        $apiDomain = 'api-free.deepl.com';
        $requestTimeout = 30;
        $targetLang = 'ES';
        $expectedResponse = 'Hola mundo';

        // Mock configuration
        $this->configProvider->expects($this->once())
            ->method('getDeeplApiKey')
            ->with($storeId)
            ->willReturn($apiKey);

        $this->configProvider->expects($this->once())
            ->method('getDeeplApiDomain')
            ->with($storeId)
            ->willReturn($apiDomain);

        $this->configProvider->expects($this->once())
            ->method('getDeeplRequestTimeout')
            ->with($storeId)
            ->willReturn($requestTimeout);

        // Mock API parameters
        $parameters = [
            'target_lang' => $targetLang,
            'split_sentences' => '1',
            'preserve_formatting' => '1'
        ];

        $this->apiParametersBuilder->expects($this->once())
            ->method('buildParametersByStoreId')
            ->with($storeId)
            ->willReturn($parameters);

        // Mock API response
        $responseBody = json_encode([
            'translations' => [
                [
                    'text' => $expectedResponse
                ]
            ]
        ]);

        $this->mockHandler->append(
            new Response(200, [], $responseBody)
        );

        // Execute test
        $result = $this->adapter->translate($text, $storeId);

        // Verify result
        $this->assertEquals($expectedResponse, $result);
    }

    public function testTranslateWithMissingApiKey(): void
    {
        $storeId = '0';
        $text = 'Hello world';

        // Mock configuration with empty API key
        $this->configProvider->expects($this->once())
            ->method('getDeeplApiKey')
            ->with($storeId)
            ->willReturn('');

        // Expect exception
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Missing DeepL API key');

        // Execute test
        $this->adapter->translate($text, $storeId);
    }

    public function testTranslateWithMissingApiDomain(): void
    {
        $storeId = '0';
        $text = 'Hello world';
        $apiKey = 'test-api-key';

        // Mock configuration
        $this->configProvider->expects($this->once())
            ->method('getDeeplApiKey')
            ->with($storeId)
            ->willReturn($apiKey);

        $this->configProvider->expects($this->once())
            ->method('getDeeplApiDomain')
            ->with($storeId)
            ->willReturn('');

        // Expect exception
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Missing DeepL API domain configuration');

        // Execute test
        $this->adapter->translate($text, $storeId);
    }

    public function testTranslateWithApiError(): void
    {
        $storeId = '0';
        $text = 'Hello world';
        $apiKey = 'test-api-key';
        $apiDomain = 'api-free.deepl.com';
        $requestTimeout = 30;
        $targetLang = 'ES';

        // Mock configuration
        $this->configProvider->expects($this->once())
            ->method('getDeeplApiKey')
            ->with($storeId)
            ->willReturn($apiKey);

        $this->configProvider->expects($this->once())
            ->method('getDeeplApiDomain')
            ->with($storeId)
            ->willReturn($apiDomain);

        $this->configProvider->expects($this->once())
            ->method('getDeeplRequestTimeout')
            ->with($storeId)
            ->willReturn($requestTimeout);

        // Mock API parameters
        $parameters = [
            'target_lang' => $targetLang,
            'split_sentences' => '1',
            'preserve_formatting' => '1'
        ];

        $this->apiParametersBuilder->expects($this->once())
            ->method('buildParametersByStoreId')
            ->with($storeId)
            ->willReturn($parameters);

        // Mock API error response
        $this->mockHandler->append(
            new \GuzzleHttp\Exception\ClientException(
                'Client error: 403 Forbidden',
                new \GuzzleHttp\Psr7\Request('POST', 'https://api-free.deepl.com/v2/translate'),
                new \GuzzleHttp\Psr7\Response(403)
            )
        );

        // Expect exception
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('DeepL API error: Client error: 403 Forbidden');

        // Execute test
        $this->adapter->translate($text, $storeId);
    }

    public function testTranslateToLanguageSuccess(): void
    {
        $text = 'Hello world';
        $targetLang = 'FR';
        $apiKey = 'test-api-key';
        $apiDomain = 'api-free.deepl.com';
        $requestTimeout = 30;
        $expectedResponse = 'Bonjour le monde';

        // Mock configuration for default store
        $this->configProvider->expects($this->once())
            ->method('getDeeplApiKey')
            ->willReturn($apiKey);

        $this->configProvider->expects($this->once())
            ->method('getDeeplApiDomain')
            ->willReturn($apiDomain);

        $this->configProvider->expects($this->once())
            ->method('getDeeplRequestTimeout')
            ->willReturn($requestTimeout);

        // Mock API parameters
        $parameters = [
            'split_sentences' => '1',
            'preserve_formatting' => '1'
        ];

        $this->apiParametersBuilder->expects($this->once())
            ->method('buildParametersByTargetLanguage')
            ->willReturn($parameters);

        // Mock API response
        $responseBody = json_encode([
            'translations' => [
                [
                    'text' => $expectedResponse
                ]
            ]
        ]);

        $this->mockHandler->append(
            new Response(200, [], $responseBody)
        );

        // Execute test
        $result = $this->adapter->translateToLanguage($text, $targetLang);

        // Verify result
        $this->assertEquals($expectedResponse, $result);
    }

    public function testGetTargetLanguageForStoreWithConfiguredLanguage(): void
    {
        $storeId = '0';
        $configuredLanguage = 'ES';

        // Mock configuration
        $this->configProvider->expects($this->once())
            ->method('getDeeplDefaultTargetLang')
            ->with($storeId)
            ->willReturn($configuredLanguage);

        // Use reflection to access private method
        $reflection = new \ReflectionClass($this->adapter);
        $method = $reflection->getMethod('getTargetLanguageForStore');
        $method->setAccessible(true);

        // Execute test
        $result = $method->invoke($this->adapter, $storeId);

        // Verify result
        $this->assertEquals($configuredLanguage, $result);
    }

    public function testGetTargetLanguageForStoreWithLocale(): void
    {
        $storeId = '0';
        $locale = 'en_US';

        // Mock configuration
        $this->configProvider->expects($this->once())
            ->method('getDeeplDefaultTargetLang')
            ->with($storeId)
            ->willReturn('');

        $this->configProvider->expects($this->once())
            ->method('getStoreLocale')
            ->with($storeId)
            ->willReturn($locale);

        // Use reflection to access private method
        $reflection = new \ReflectionClass($this->adapter);
        $method = $reflection->getMethod('getTargetLanguageForStore');
        $method->setAccessible(true);

        // Execute test
        $result = $method->invoke($this->adapter, $storeId);

        // Verify result
        $this->assertEquals('EN-US', $result);
    }

    public function testGetTargetLanguageFromLocale(): void
    {
        $locale = 'es_ES';

        // Use reflection to access private method
        $reflection = new \ReflectionClass($this->adapter);
        $method = $reflection->getMethod('getTargetLanguageFromLocale');
        $method->setAccessible(true);

        // Execute test
        $result = $method->invoke($this->adapter, $locale);

        // Verify result
        $this->assertEquals('ES', $result);
    }
}
