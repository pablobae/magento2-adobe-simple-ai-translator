<?php
declare(strict_types=1);

namespace Pablobae\SimpleAiTranslator\Test\Unit\Service\Translator;

use Pablobae\SimpleAiTranslator\Service\Translator\Deepl\ApiParametersBuilder;
use Pablobae\SimpleAiTranslator\Service\Translator\DeeplTranslator;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Pablobae\SimpleAiTranslator\Service\ConfigProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DeeplTranslatorTest extends TestCase
{
    /**
     * @var DeeplTranslator
     */
    private DeeplTranslator $adapter;

    /**
     * @var ConfigProvider|MockObject
     */
    private $configProvider;

    /**
     * @var ApiParametersBuilder|MockObject
     */
    private $apiParametersBuilder;

    /**
     * @var Client|MockObject
     */
    private $guzzleClient;

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

        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $this->guzzleClient = new Client(['handler' => $handlerStack]);

        $this->adapter = new DeeplTranslator(
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
        $apiDomain = 'api.deepl.com';
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
            ->willReturn(30);

        // Mock API parameters
        $this->apiParametersBuilder->expects($this->once())
            ->method('buildParametersByStoreId')
            ->with($storeId)
            ->willReturn([
                'target_lang' => $targetLang,
                'formality' => 'more'
            ]);

        // Mock API response
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'translations' => [
                    [
                        'text' => $expectedResponse
                    ]
                ]
            ]))
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
        $apiDomain = 'api.deepl.com';

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
            ->willReturn(30);

        // Mock API parameters
        $this->apiParametersBuilder->expects($this->once())
            ->method('buildParametersByStoreId')
            ->with($storeId)
            ->willReturn([
                'target_lang' => 'ES',
                'formality' => 'more'
            ]);

        // Mock API error
        $this->mockHandler->append(
            new ClientException(
                'API Error',
                new Request('POST', 'https://api.deepl.com/v2/translate'),
                new Response(401)
            )
        );

        // Expect exception
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('DeepL API error: API Error');

        // Execute test
        $this->adapter->translate($text, $storeId);
    }

    public function testTranslateToLanguageSuccess(): void
    {
        $text = 'Hello world';
        $targetLang = 'FR';
        $apiKey = 'test-api-key';
        $apiDomain = 'api.deepl.com';
        $expectedResponse = 'Bonjour le monde';

        // Mock configuration
        $this->configProvider->expects($this->once())
            ->method('getDeeplApiKey')
            ->willReturn($apiKey);

        $this->configProvider->expects($this->once())
            ->method('getDeeplApiDomain')
            ->willReturn($apiDomain);

        $this->configProvider->expects($this->once())
            ->method('getDeeplRequestTimeout')
            ->willReturn(30);

        // Mock API parameters
        $this->apiParametersBuilder->expects($this->once())
            ->method('buildParametersByTargetLanguage')
            ->with($targetLang)
            ->willReturn([
                'formality' => 'more'
            ]);

        // Mock API response
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'translations' => [
                    [
                        'text' => $expectedResponse
                    ]
                ]
            ]))
        );

        // Execute test
        $result = $this->adapter->translateToLanguage($text, $targetLang);

        // Verify result
        $this->assertEquals($expectedResponse, $result);
    }

    public function testTranslateWithUnknownLanguage(): void
    {
        $storeId = '0';
        $text = 'Hello world';
        $apiKey = 'test-api-key';
        $apiDomain = 'api.deepl.com';

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
            ->willReturn(30);

        // Mock API parameters with unknown language
        $this->apiParametersBuilder->expects($this->once())
            ->method('buildParametersByStoreId')
            ->with($storeId)
            ->willReturn([
                'target_lang' => 'XX',
                'formality' => 'more'
            ]);

        // Mock API error for unknown language
        $this->mockHandler->append(
            new ClientException(
                'Target language not supported',
                new Request('POST', 'https://api.deepl.com/v2/translate'),
                new Response(400)
            )
        );

        // Expect exception
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('DeepL API error: Target language not supported');

        // Execute test
        $this->adapter->translate($text, $storeId);
    }

    public function testTranslateWithInvalidResponse(): void
    {
        $storeId = '0';
        $text = 'Hello world';
        $apiKey = 'test-api-key';
        $apiDomain = 'api.deepl.com';

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
            ->willReturn(30);

        // Mock API parameters
        $this->apiParametersBuilder->expects($this->once())
            ->method('buildParametersByStoreId')
            ->with($storeId)
            ->willReturn([
                'target_lang' => 'ES',
                'formality' => 'more'
            ]);

        // Mock invalid API response
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'translations' => []
            ]))
        );

        // Execute test
        $result = $this->adapter->translate($text, $storeId);

        // Verify result is empty string when no translations are returned
        $this->assertEquals('', $result);
    }

    public function testTranslateWithDefaultTargetLanguage(): void
    {
        $storeId = '0';
        $text = 'Hello world';
        $apiKey = 'test-api-key';
        $apiDomain = 'api.deepl.com';
        $defaultTargetLang = 'ES';
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
            ->willReturn(30);

        // Mock API parameters without target language
        $this->apiParametersBuilder->expects($this->once())
            ->method('buildParametersByStoreId')
            ->with($storeId)
            ->willReturn([
                'formality' => 'more'
            ]);

        // Mock default target language
        $this->configProvider->expects($this->once())
            ->method('getDeeplDefaultTargetLang')
            ->with($storeId)
            ->willReturn($defaultTargetLang);

        // Mock API response
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'translations' => [
                    [
                        'text' => $expectedResponse
                    ]
                ]
            ]))
        );

        // Execute test
        $result = $this->adapter->translate($text, $storeId);

        // Verify result
        $this->assertEquals($expectedResponse, $result);
    }

    public function testTranslateWithStoreLocale(): void
    {
        $storeId = '0';
        $text = 'Hello world';
        $apiKey = 'test-api-key';
        $apiDomain = 'api.deepl.com';
        $locale = 'es_ES';
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
            ->willReturn(30);

        // Mock API parameters without target language
        $this->apiParametersBuilder->expects($this->once())
            ->method('buildParametersByStoreId')
            ->with($storeId)
            ->willReturn([
                'formality' => 'more'
            ]);

        // Mock default target language as empty
        $this->configProvider->expects($this->once())
            ->method('getDeeplDefaultTargetLang')
            ->with($storeId)
            ->willReturn('');

        // Mock store locale
        $this->configProvider->expects($this->once())
            ->method('getStoreLocale')
            ->with($storeId)
            ->willReturn($locale);

        // Mock API response
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'translations' => [
                    [
                        'text' => $expectedResponse
                    ]
                ]
            ]))
        );

        // Execute test
        $result = $this->adapter->translate($text, $storeId);

        // Verify result
        $this->assertEquals($expectedResponse, $result);
    }

    public function testTranslateToLanguageWithMissingApiKey(): void
    {
        $text = 'Hello world';
        $targetLang = 'FR';

        // Mock configuration with empty API key
        $this->configProvider->expects($this->once())
            ->method('getDeeplApiKey')
            ->willReturn('');

        // Expect exception
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Missing DeepL API key');

        // Execute test
        $this->adapter->translateToLanguage($text, $targetLang);
    }

    public function testTranslateToLanguageWithMissingApiDomain(): void
    {
        $text = 'Hello world';
        $targetLang = 'FR';
        $apiKey = 'test-api-key';

        // Mock configuration
        $this->configProvider->expects($this->once())
            ->method('getDeeplApiKey')
            ->willReturn($apiKey);

        $this->configProvider->expects($this->once())
            ->method('getDeeplApiDomain')
            ->willReturn('');

        // Expect exception
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Missing DeepL API domain configuration');

        // Execute test
        $this->adapter->translateToLanguage($text, $targetLang);
    }

    public function testTranslateToLanguageWithApiError(): void
    {
        $text = 'Hello world';
        $targetLang = 'FR';
        $apiKey = 'test-api-key';
        $apiDomain = 'api.deepl.com';

        // Mock configuration
        $this->configProvider->expects($this->once())
            ->method('getDeeplApiKey')
            ->willReturn($apiKey);

        $this->configProvider->expects($this->once())
            ->method('getDeeplApiDomain')
            ->willReturn($apiDomain);

        $this->configProvider->expects($this->once())
            ->method('getDeeplRequestTimeout')
            ->willReturn(30);

        // Mock API parameters
        $this->apiParametersBuilder->expects($this->once())
            ->method('buildParametersByTargetLanguage')
            ->with($targetLang)
            ->willReturn([
                'formality' => 'more'
            ]);

        // Mock API error
        $this->mockHandler->append(
            new ClientException(
                'API Error',
                new Request('POST', 'https://api.deepl.com/v2/translate'),
                new Response(401)
            )
        );

        // Expect exception
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('DeepL API error: API Error');

        // Execute test
        $this->adapter->translateToLanguage($text, $targetLang);
    }

    public function testTranslateWithHtmlContent(): void
    {
        $storeId = '0';
        $text = '<p>Hello <strong>world</strong></p>';
        $apiKey = 'test-api-key';
        $apiDomain = 'api.deepl.com';
        $targetLang = 'ES';
        $expectedResponse = '<p>Hola <strong>mundo</strong></p>';

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
            ->willReturn(30);

        // Mock API parameters with HTML tag handling
        $this->apiParametersBuilder->expects($this->once())
            ->method('buildParametersByStoreId')
            ->with($storeId)
            ->willReturn([
                'target_lang' => $targetLang,
                'formality' => 'more',
                'tag_handling' => 'html'
            ]);

        // Mock API response
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'translations' => [
                    [
                        'text' => $expectedResponse
                    ]
                ]
            ]))
        );

        // Execute test
        $result = $this->adapter->translate($text, $storeId);

        // Verify result
        $this->assertEquals($expectedResponse, $result);
    }

    public function testTranslateWithEmptySourceLang(): void
    {
        $storeId = '0';
        $text = 'Hello world';
        $apiKey = 'test-api-key';
        $apiDomain = 'api.deepl.com';
        $sourceLang = '';
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
            ->willReturn(30);

        // Mock API parameters with empty source language
        $this->apiParametersBuilder->expects($this->once())
            ->method('buildParametersByStoreId')
            ->with($storeId)
            ->willReturn([
                'target_lang' => $targetLang,
                'formality' => 'more'
                // No source_lang parameter
            ]);

        // Mock API response
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'translations' => [
                    [
                        'text' => $expectedResponse
                    ]
                ]
            ]))
        );

        // Execute test
        $result = $this->adapter->translate($text, $storeId);

        // Verify result
        $this->assertEquals($expectedResponse, $result);
    }

    public function testTranslateWithNoLocaleAndNoDefaultTargetLang(): void
    {
        $storeId = '0';
        $text = 'Hello world';
        $apiKey = 'test-api-key';
        $apiDomain = 'api.deepl.com';
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
            ->willReturn(30);

        // Mock API parameters without target language
        $this->apiParametersBuilder->expects($this->once())
            ->method('buildParametersByStoreId')
            ->with($storeId)
            ->willReturn([
                'formality' => 'more'
            ]);

        // Mock default target language as empty
        $this->configProvider->expects($this->once())
            ->method('getDeeplDefaultTargetLang')
            ->with($storeId)
            ->willReturn('');

        // Mock store locale as empty
        $this->configProvider->expects($this->once())
            ->method('getStoreLocale')
            ->with($storeId)
            ->willReturn('');

        // Mock API response - should use default EN-US
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'translations' => [
                    [
                        'text' => $expectedResponse
                    ]
                ]
            ]))
        );

        // Execute test
        $result = $this->adapter->translate($text, $storeId);

        // Verify result
        $this->assertEquals($expectedResponse, $result);
    }
}
