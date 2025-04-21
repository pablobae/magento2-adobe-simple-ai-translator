<?php
declare(strict_types=1);

namespace Pablobae\SimpleAiTranslator\Test\Unit\Service\Translator\ChatGpt;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Magento\Framework\Exception\LocalizedException;
use Pablobae\SimpleAiTranslator\Service\ConfigProvider;
use Pablobae\SimpleAiTranslator\Service\Translator\ChatGpt\ApiClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ApiClientTest extends TestCase
{
    /**
     * @var ApiClient
     */
    private $apiClient;

    /**
     * @var ConfigProvider|MockObject
     */
    private $configProviderMock;

    /**
     * @var LoggerInterface|MockObject
     */
    private $loggerMock;

    /**
     * @var MockHandler
     */
    private $mockHandler;

    /**
     * Set up test environment
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $httpClient = new Client(['handler' => $handlerStack]);

        $this->configProviderMock = $this->createMock(ConfigProvider::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->apiClient = new ApiClient(
            $httpClient,
            $this->configProviderMock,
            $this->loggerMock
        );
    }

    /**
     * Test successful API request and response
     *
     * @return void
     */
    public function testSendRequestSuccess(): void
    {
        // Configure mocks
        $this->configProviderMock->expects($this->once())
            ->method('getChatGptApiKey')
            ->willReturn('test-api-key');

        $this->configProviderMock->expects($this->once())
            ->method('getChatGptModel')
            ->willReturn('gpt-3.5-turbo');

        $this->configProviderMock->expects($this->once())
            ->method('getChatGptTemperature')
            ->willReturn(0.7);

        $this->configProviderMock->expects($this->once())
            ->method('getChatGptRequestTimeout')
            ->willReturn(30);

        // Mock successful API response
        $responseBody = json_encode([
            'choices' => [
                [
                    'message' => [
                        'content' => 'Hola mundo'
                    ]
                ]
            ]
        ]);
        $this->mockHandler->append(new Response(200, [], $responseBody));

        // Test messages
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are a professional translator.'
            ],
            [
                'role' => 'user',
                'content' => 'Translate the following text to es: "Hello world"'
            ]
        ];

        // Execute the method
        $result = $this->apiClient->sendRequest($messages);

        // Assert the result
        $this->assertIsArray($result);
        $this->assertArrayHasKey('choices', $result);
        $this->assertArrayHasKey(0, $result['choices']);
        $this->assertArrayHasKey('message', $result['choices'][0]);
        $this->assertArrayHasKey('content', $result['choices'][0]['message']);
        $this->assertEquals('Hola mundo', $result['choices'][0]['message']['content']);
    }

    /**
     * Test error handling when API returns an error
     *
     * @return void
     */
    public function testSendRequestApiError(): void
    {
        // Configure mocks
        $this->configProviderMock->expects($this->once())
            ->method('getChatGptApiKey')
            ->willReturn('test-api-key');

        $this->configProviderMock->expects($this->once())
            ->method('getChatGptModel')
            ->willReturn('gpt-3.5-turbo');

        $this->configProviderMock->expects($this->once())
            ->method('getChatGptTemperature')
            ->willReturn(0.7);

        $this->configProviderMock->expects($this->once())
            ->method('getChatGptRequestTimeout')
            ->willReturn(30);

        // Mock error API response
        $responseBody = json_encode([
            'error' => [
                'message' => 'Failed to get response from ChatGPT API.',
                'type' => 'invalid_request_error'
            ]
        ]);
        $this->mockHandler->append(new Response(401, [], $responseBody));

        // Expect logger to be called
        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with($this->stringContains('ChatGPT API Error:'));

        // Test messages
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are a professional translator.'
            ],
            [
                'role' => 'user',
                'content' => 'Translate the following text to es: "Hello world"'
            ]
        ];

        // Execute the method and expect exception
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('ChatGPT API Error');
        $this->apiClient->sendRequest($messages);
    }

    /**
     * Test error handling when API returns unexpected response format
     *
     * @return void
     */
    public function testSendRequestUnexpectedResponse(): void
    {
        // Configure mocks
        $this->configProviderMock->expects($this->once())
            ->method('getChatGptApiKey')
            ->willReturn('test-api-key');

        $this->configProviderMock->expects($this->once())
            ->method('getChatGptModel')
            ->willReturn('gpt-3.5-turbo');

        $this->configProviderMock->expects($this->once())
            ->method('getChatGptTemperature')
            ->willReturn(0.7);

        $this->configProviderMock->expects($this->once())
            ->method('getChatGptRequestTimeout')
            ->willReturn(30);

        // Mock unexpected API response
        $responseBody = json_encode([
            'some_unexpected_key' => 'some_value'
        ]);
        $this->mockHandler->append(new Response(200, [], $responseBody));

        // Expect logger to be called
        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with($this->stringContains('ChatGPT API Error:'));

        // Test messages
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are a professional translator.'
            ],
            [
                'role' => 'user',
                'content' => 'Translate the following text to es: "Hello world"'
            ]
        ];

        // Execute the method and expect exception
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Unknown error occurred while calling ChatGPT API.');
        $this->apiClient->sendRequest($messages);
    }

    /**
     * Test GuzzleException handling
     *
     * @return void
     */
    public function testSendRequestGuzzleException(): void
    {
        // Configure mocks
        $this->configProviderMock->expects($this->once())
            ->method('getChatGptApiKey')
            ->willReturn('test-api-key');

        $this->configProviderMock->expects($this->once())
            ->method('getChatGptModel')
            ->willReturn('gpt-3.5-turbo');

        $this->configProviderMock->expects($this->once())
            ->method('getChatGptTemperature')
            ->willReturn(0.7);

        $this->configProviderMock->expects($this->once())
            ->method('getChatGptRequestTimeout')
            ->willReturn(30);

        // Mock network error
        $this->mockHandler->append(new ConnectException(
            'Connection timed out',
            new Request('POST', 'https://api.openai.com/v1/chat/completions')
        ));

        // Test messages
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are a professional translator.'
            ],
            [
                'role' => 'user',
                'content' => 'Translate the following text to es: "Hello world"'
            ]
        ];

        // Execute the method and expect exception
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Failed to get response from ChatGPT API.');
        $this->apiClient->sendRequest($messages);
    }

    /**
     * Test extractTranslation method
     *
     * @return void
     */
    public function testExtractTranslation(): void
    {
        $response = [
            'choices' => [
                [
                    'message' => [
                        'content' => '  Hola mundo  '
                    ]
                ]
            ]
        ];

        $result = $this->apiClient->extractTranslation($response);
        $this->assertEquals('Hola mundo', $result);
    }
}
