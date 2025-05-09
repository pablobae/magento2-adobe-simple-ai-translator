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

namespace Pablobae\SimpleAiTranslator\Test\Unit\Service\Translator;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Magento\Framework\Exception\LocalizedException;
use Pablobae\SimpleAiTranslator\Service\ConfigProvider;
use Pablobae\SimpleAiTranslator\Service\Translator\ChatGpt\ApiClient;
use Pablobae\SimpleAiTranslator\Service\Translator\ChatGpt\PromptBuilder;
use Pablobae\SimpleAiTranslator\Service\Translator\ChatGptTranslator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ChatGptTranslatorTest extends TestCase
{
    /**
     * @var ChatGptTranslator
     */
    private ChatGptTranslator $adapter;

    /**
     * @var ConfigProvider|MockObject
     */
    private $configProvider;

    /**
     * @var ApiClient|MockObject
     */
    private $apiClient;

    /**
     * @var PromptBuilder|MockObject
     */
    private $promptBuilder;

    /**
     * @var LoggerInterface|MockObject
     */
    private $logger;

    protected function setUp(): void
    {
        $this->configProvider = $this->getMockBuilder(ConfigProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->apiClient = $this->getMockBuilder(ApiClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->promptBuilder = $this->getMockBuilder(PromptBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->adapter = new ChatGptTranslator(
            $this->configProvider,
            $this->apiClient,
            $this->promptBuilder,
            $this->logger
        );
    }

    public function testTranslateSuccess(): void
    {
        $storeId = '0';
        $text = 'Hello world';
        $sourceLang = 'EN';
        $targetLang = 'ES';
        $expectedResponse = 'Hola mundo';

        // Mock configuration
        $this->configProvider->expects($this->once())
            ->method('getChatGptDefaultSourceLang')
            ->with($storeId)
            ->willReturn($sourceLang);

        $this->configProvider->expects($this->once())
            ->method('getChatGptDefaultTargetLang')
            ->with($storeId)
            ->willReturn($targetLang);

        // Mock prompt builder
        $this->promptBuilder->expects($this->once())
            ->method('buildTranslationPrompt')
            ->with($text, $targetLang, $sourceLang)
            ->willReturn([
                'role' => 'system',
                'content' => 'You are a professional translator. Translate the following text to ' . $targetLang . '. Only return the translation, nothing else.'
            ]);

        // Mock API response
        $responseData = [
            'choices' => [
                [
                    'message' => [
                        'content' => $expectedResponse
                    ]
                ]
            ]
        ];

        $this->apiClient->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseData);

        $this->apiClient->expects($this->once())
            ->method('extractTranslation')
            ->with($responseData)
            ->willReturn($expectedResponse);

        // Execute test
        $result = $this->adapter->translate($text, $storeId);

        // Verify result
        $this->assertEquals($expectedResponse, $result);
    }

    public function testTranslateWithMissingTargetLang(): void
    {
        $storeId = '0';
        $text = 'Hello world';

        // Mock configuration with empty target language
        $this->configProvider->expects($this->once())
            ->method('getChatGptDefaultTargetLang')
            ->with($storeId)
            ->willReturn('');

        // Expect exception
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Target language is required for translation.');

        // Execute test
        $this->adapter->translate($text, $storeId);
    }

    public function testTranslateWithApiError(): void
    {
        $storeId = '0';
        $text = 'Hello world';
        $sourceLang = 'EN';
        $targetLang = 'ES';

        // Mock configuration
        $this->configProvider->expects($this->once())
            ->method('getChatGptDefaultSourceLang')
            ->with($storeId)
            ->willReturn($sourceLang);

        $this->configProvider->expects($this->once())
            ->method('getChatGptDefaultTargetLang')
            ->with($storeId)
            ->willReturn($targetLang);

        // Mock prompt builder
        $this->promptBuilder->expects($this->once())
            ->method('buildTranslationPrompt')
            ->with($text, $targetLang, $sourceLang)
            ->willReturn([
                'role' => 'system',
                'content' => 'You are a professional translator. Translate the following text to ' . $targetLang . '. Only return the translation, nothing else.'
            ]);

        // Mock API error
        $this->apiClient->expects($this->once())
            ->method('sendRequest')
            ->willThrowException(new ClientException(
                'API Error',
                new Request('POST', 'https://api.openai.com/v1/chat/completions'),
                new Response(401)
            ));

        // Expect exception
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Failed to get translation from ChatGPT API.');

        // Execute test
        $this->adapter->translate($text, $storeId);
    }

    public function testTranslateToLanguageSuccess(): void
    {
        $text = 'Hello world';
        $targetLang = 'FR';
        $expectedResponse = 'Bonjour le monde';

        // Mock prompt builder
        $this->promptBuilder->expects($this->once())
            ->method('buildTranslationPrompt')
            ->with($text, $targetLang)
            ->willReturn([
                'role' => 'system',
                'content' => 'You are a professional translator. Translate the following text to ' . $targetLang . '. Only return the translation, nothing else.'
            ]);

        // Mock API response
        $responseData = [
            'choices' => [
                [
                    'message' => [
                        'content' => $expectedResponse
                    ]
                ]
            ]
        ];

        $this->apiClient->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseData);

        $this->apiClient->expects($this->once())
            ->method('extractTranslation')
            ->with($responseData)
            ->willReturn($expectedResponse);

        // Execute test
        $result = $this->adapter->translateToLanguage($text, $targetLang);

        // Verify result
        $this->assertEquals($expectedResponse, $result);
    }

    public function testTranslateToLanguageWithApiError(): void
    {
        $text = 'Hello world';
        $targetLang = 'FR';

        // Mock prompt builder
        $this->promptBuilder->expects($this->once())
            ->method('buildTranslationPrompt')
            ->with($text, $targetLang)
            ->willReturn([
                'role' => 'system',
                'content' => 'You are a professional translator. Translate the following text to ' . $targetLang . '. Only return the translation, nothing else.'
            ]);

        // Mock API error
        $this->apiClient->expects($this->once())
            ->method('sendRequest')
            ->willThrowException(new ClientException(
                'API Error',
                new Request('POST', 'https://api.openai.com/v1/chat/completions'),
                new Response(401)
            ));

        // Expect exception
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Failed to get translation from ChatGPT API.');

        // Execute test
        $this->adapter->translateToLanguage($text, $targetLang);
    }

    public function testTranslateWithEmptySourceLang(): void
    {
        $storeId = '0';
        $text = 'Hello world';
        $sourceLang = '';
        $targetLang = 'ES';
        $expectedResponse = 'Hola mundo';

        // Mock configuration
        $this->configProvider->expects($this->once())
            ->method('getChatGptDefaultSourceLang')
            ->with($storeId)
            ->willReturn($sourceLang);

        $this->configProvider->expects($this->once())
            ->method('getChatGptDefaultTargetLang')
            ->with($storeId)
            ->willReturn($targetLang);

        // Mock prompt builder - should not include source language when empty
        $this->promptBuilder->expects($this->once())
            ->method('buildTranslationPrompt')
            ->with($text, $targetLang, $sourceLang)
            ->willReturn([
                'role' => 'system',
                'content' => 'You are a professional translator. Translate the following text to ' . $targetLang . '. Only return the translation, nothing else.'
            ]);

        // Mock API response
        $responseData = [
            'choices' => [
                [
                    'message' => [
                        'content' => $expectedResponse
                    ]
                ]
            ]
        ];

        $this->apiClient->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseData);

        $this->apiClient->expects($this->once())
            ->method('extractTranslation')
            ->with($responseData)
            ->willReturn($expectedResponse);

        // Execute test
        $result = $this->adapter->translate($text, $storeId);

        // Verify result
        $this->assertEquals($expectedResponse, $result);
    }

    public function testTranslateWithHtmlContent(): void
    {
        $storeId = '0';
        $text = '<p>Hello <strong>world</strong></p>';
        $sourceLang = 'EN';
        $targetLang = 'ES';
        $expectedResponse = '<p>Hola <strong>mundo</strong></p>';

        // Mock configuration
        $this->configProvider->expects($this->once())
            ->method('getChatGptDefaultSourceLang')
            ->with($storeId)
            ->willReturn($sourceLang);

        $this->configProvider->expects($this->once())
            ->method('getChatGptDefaultTargetLang')
            ->with($storeId)
            ->willReturn($targetLang);

        // Mock prompt builder
        $this->promptBuilder->expects($this->once())
            ->method('buildTranslationPrompt')
            ->with($text, $targetLang, $sourceLang)
            ->willReturn([
                'role' => 'system',
                'content' => 'You are a professional translator. Translate the following text to ' . $targetLang . '. Only return the translation, nothing else.'
            ]);

        // Mock API response
        $responseData = [
            'choices' => [
                [
                    'message' => [
                        'content' => $expectedResponse
                    ]
                ]
            ]
        ];

        $this->apiClient->expects($this->once())
            ->method('sendRequest')
            ->willReturn($responseData);

        $this->apiClient->expects($this->once())
            ->method('extractTranslation')
            ->with($responseData)
            ->willReturn($expectedResponse);

        // Execute test
        $result = $this->adapter->translate($text, $storeId);

        // Verify result
        $this->assertEquals($expectedResponse, $result);
    }
}
