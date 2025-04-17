<?php
declare(strict_types=1);

namespace Pablobae\SimpleAiTranslator\Test\Unit\Model\TranslatorAdapter;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;
use Pablobae\SimpleAiTranslator\Model\TranslatorAdapter\ChatGptAdapter;
use Pablobae\SimpleAiTranslator\Service\ConfigProvider;
use Pablobae\SimpleAiTranslator\Service\ChatGpt\ApiClient;
use Pablobae\SimpleAiTranslator\Service\ChatGpt\PromptBuilder;
use Psr\Log\LoggerInterface;

class ChatGptAdapterTest extends TestCase
{
    /**
     * @var ChatGptAdapter
     */
    private ChatGptAdapter $adapter;

    /**
     * @var ConfigProvider|\PHPUnit\Framework\MockObject\MockObject
     */
    private $configProvider;

    /**
     * @var ApiClient|\PHPUnit\Framework\MockObject\MockObject
     */
    private $apiClient;

    /**
     * @var PromptBuilder|\PHPUnit\Framework\MockObject\MockObject
     */
    private $promptBuilder;

    /**
     * @var LoggerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $logger;

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
            
        $this->apiClient = $this->getMockBuilder(ApiClient::class)
            ->disableOriginalConstructor()
            ->getMock();
            
        $this->promptBuilder = $this->getMockBuilder(PromptBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
            
        $this->logger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
            
        // Set up Guzzle mock
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $this->guzzleClient = new Client(['handler' => $handlerStack]);
        
        $this->adapter = new ChatGptAdapter(
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
} 