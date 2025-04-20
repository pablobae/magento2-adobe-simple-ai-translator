<?php
declare(strict_types=1);

namespace Pablobae\SimpleAiTranslator\Test\Unit\Service\Translator;


use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Pablobae\SimpleAiTranslator\Service\Translator\Deepl\ApiClient;
use Pablobae\SimpleAiTranslator\Service\Translator\DeeplTranslator;
use RuntimeException;

class DeeplTranslatorTest extends TestCase
{
    /**
     * @var DeeplTranslator
     */
    private DeeplTranslator $adapter;

    /**
     * @var ApiClient|MockObject
     */
    private $apiClient;

    protected function setUp(): void
    {
        $this->apiClient = $this->createMock(ApiClient::class);

        $this->adapter = new DeeplTranslator(
            $this->apiClient
        );
    }

    public function testTranslateSuccess(): void
    {
        $storeId = 'store1';
        $text = 'Hello world';
        $expectedResponse = 'Hola mundo';

        // Mock API Client
        $this->apiClient->expects($this->once())
            ->method('translateByStoreId')
            ->with($text, $storeId)
            ->willReturn([
                'translations' => [
                    [
                        'text' => $expectedResponse
                    ]
                ]
            ]);

        // Execute the method
        $result = $this->adapter->translate($text, $storeId);

        // Assert the result
        $this->assertEquals($expectedResponse, $result);
    }

    public function testTranslateWithApiError(): void
    {
        $storeId = 'store2';
        $text = 'Hello world';

        // Mock API Client to throw an exception
        $this->apiClient->expects($this->once())
            ->method('translateByStoreId')
            ->with($text, $storeId)
            ->willThrowException(new RuntimeException('API Error'));

        // Expectation
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('API Error');

        // Execute the method
        $this->adapter->translate($text, $storeId);
    }

    public function testTranslateToLanguageSuccess(): void
    {
        $text = 'Hello world';
        $targetLang = 'FR';
        $expectedResponse = 'Bonjour le monde';

        // Mock API Client
        $this->apiClient->expects($this->once())
            ->method('translateToTargetLanguage')
            ->with($text, $targetLang)
            ->willReturn([
                'translations' => [
                    [
                        'text' => $expectedResponse
                    ]
                ]
            ]);

        // Execute the method
        $result = $this->adapter->translateToLanguage($text, $targetLang);

        // Assert the result
        $this->assertEquals($expectedResponse, $result);
    }

    public function testTranslateToLanguageWithApiError(): void
    {
        $text = 'Hello world';
        $targetLang = 'DE';

        // Mock API Client to throw an exception
        $this->apiClient->expects($this->once())
            ->method('translateToTargetLanguage')
            ->with($text, $targetLang)
            ->willThrowException(new RuntimeException('Translation failed'));

        // Expectation
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Translation failed');

        // Execute the method
        $this->adapter->translateToLanguage($text, $targetLang);
    }
}
