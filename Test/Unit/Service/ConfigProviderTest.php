<?php
declare(strict_types=1);

namespace Pablobae\SimpleAiTranslator\Test\Unit\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\TestCase;
use Pablobae\SimpleAiTranslator\Service\ConfigProvider;

class ConfigProviderTest extends TestCase
{
    private ConfigProvider $configProvider;
    private ScopeConfigInterface $scopeConfig;
    private EncryptorInterface $encryptor;

    protected function setUp(): void
    {
        $this->scopeConfig = $this->getMockBuilder(ScopeConfigInterface::class)
            ->getMockForAbstractClass();
            
        $this->encryptor = $this->getMockBuilder(EncryptorInterface::class)
            ->getMockForAbstractClass();

        $this->configProvider = new ConfigProvider(
            $this->scopeConfig,
            $this->encryptor
        );
    }

    public function testGetStoreLocale(): void
    {
        $storeId = '1';
        $expectedLocale = 'en_US';
        
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(ConfigProvider::STORE_LOCALE, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expectedLocale);
            
        $result = $this->configProvider->getStoreLocale($storeId);
        
        $this->assertEquals($expectedLocale, $result);
    }

    public function testIsModuleEnabled(): void
    {
        $storeId = '1';
        
        $this->scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->with(ConfigProvider::XML_PATH_ENABLE, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn(true);
            
        $result = $this->configProvider->isModuleEnabled($storeId);
        
        $this->assertTrue($result);
    }

    public function testIsModuleDisabled(): void
    {
        $storeId = '1';
        
        $this->scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->with(ConfigProvider::XML_PATH_ENABLE, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn(false);
            
        $result = $this->configProvider->isModuleEnabled($storeId);
        
        $this->assertFalse($result);
    }

    public function testGetAiEngine(): void
    {
        $storeId = '1';
        $expectedEngine = 'deepl';
        
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(ConfigProvider::XML_PATH_AI_ENGINE, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expectedEngine);
            
        $result = $this->configProvider->getAiEngine($storeId);
        
        $this->assertEquals($expectedEngine, $result);
    }

    public function testGetDeeplApiDomain(): void
    {
        $storeId = '1';
        $expectedDomain = 'api-free.deepl.com';
        
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(ConfigProvider::XML_PATH_DEEPL_API_DOMAIN, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expectedDomain);
            
        $result = $this->configProvider->getDeeplApiDomain($storeId);
        
        $this->assertEquals($expectedDomain, $result);
    }

    public function testGetDeeplApiKey(): void
    {
        $storeId = '1';
        $encryptedKey = 'encrypted_api_key';
        $decryptedKey = 'actual_api_key';
        
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(ConfigProvider::XML_PATH_DEEPL_API_KEY, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($encryptedKey);
            
        $this->encryptor->expects($this->once())
            ->method('decrypt')
            ->with($encryptedKey)
            ->willReturn($decryptedKey);
            
        $result = $this->configProvider->getDeeplApiKey($storeId);
        
        $this->assertEquals($decryptedKey, $result);
    }

    public function testGetDeeplApiKeyWhenEmpty(): void
    {
        $storeId = '1';
        
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(ConfigProvider::XML_PATH_DEEPL_API_KEY, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn('');
            
        $this->encryptor->expects($this->never())
            ->method('decrypt');
            
        $result = $this->configProvider->getDeeplApiKey($storeId);
        
        $this->assertNull($result);
    }

    public function testGetDeeplDefaultSourceLang(): void
    {
        $storeId = '1';
        $expectedLang = 'EN';
        
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(ConfigProvider::XML_PATH_DEEPL_DEFAULT_SOURCE_LANG, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expectedLang);
            
        $result = $this->configProvider->getDeeplDefaultSourceLang($storeId);
        
        $this->assertEquals($expectedLang, $result);
    }

    public function testGetDeeplDefaultTargetLang(): void
    {
        $storeId = '1';
        $expectedLang = 'ES';
        
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(ConfigProvider::XML_PATH_DEEPL_DEFAULT_TARGET_LANG, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expectedLang);
            
        $result = $this->configProvider->getDeeplDefaultTargetLang($storeId);
        
        $this->assertEquals($expectedLang, $result);
    }

    public function testGetDeeplModelType(): void
    {
        $storeId = '1';
        $expectedModel = 'base';
        
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(ConfigProvider::XML_PATH_DEEPL_MODEL_TYPE, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expectedModel);
            
        $result = $this->configProvider->getDeeplModelType($storeId);
        
        $this->assertEquals($expectedModel, $result);
    }

    public function testGetDeeplSplitSentences(): void
    {
        $storeId = '1';
        $expectedValue = '1';
        
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(ConfigProvider::XML_PATH_DEEPL_SPLIT_SENTENCES, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expectedValue);
            
        $result = $this->configProvider->getDeeplSplitSentences($storeId);
        
        $this->assertEquals($expectedValue, $result);
    }

    public function testIsDeeplPreserveFormattingEnabled(): void
    {
        $storeId = '1';
        
        $this->scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->with(ConfigProvider::XML_PATH_DEEPL_PRESERVE_FORMATTING, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn(true);
            
        $result = $this->configProvider->isDeeplPreserveFormattingEnabled($storeId);
        
        $this->assertTrue($result);
    }

    public function testGetDeeplFormality(): void
    {
        $storeId = '1';
        $expectedValue = 'more';
        
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(ConfigProvider::XML_PATH_DEEPL_FORMALITY, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expectedValue);
            
        $result = $this->configProvider->getDeeplFormality($storeId);
        
        $this->assertEquals($expectedValue, $result);
    }

    public function testGetDeeplTagHandling(): void
    {
        $storeId = '1';
        $expectedValue = 'html';
        
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(ConfigProvider::XML_PATH_DEEPL_TAG_HANDLING, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expectedValue);
            
        $result = $this->configProvider->getDeeplTagHandling($storeId);
        
        $this->assertEquals($expectedValue, $result);
    }

    public function testIsDeeplOutlineDetectionEnabled(): void
    {
        $storeId = '1';
        
        $this->scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->with(ConfigProvider::XML_PATH_DEEPL_OUTLINE_DETECTION, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn(true);
            
        $result = $this->configProvider->isDeeplOutlineDetectionEnabled($storeId);
        
        $this->assertTrue($result);
    }

    public function testGetDeeplNonSplittingTags(): void
    {
        $storeId = '1';
        $expectedValue = 'p,br';
        
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(ConfigProvider::XML_PATH_DEEPL_NON_SPLITTING_TAGS, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expectedValue);
            
        $result = $this->configProvider->getDeeplNonSplittingTags($storeId);
        
        $this->assertEquals($expectedValue, $result);
    }

    public function testGetDeeplSplittingTags(): void
    {
        $storeId = '1';
        $expectedValue = 'h1,h2,h3';
        
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(ConfigProvider::XML_PATH_DEEPL_SPLITTING_TAGS, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expectedValue);
            
        $result = $this->configProvider->getDeeplSplittingTags($storeId);
        
        $this->assertEquals($expectedValue, $result);
    }

    public function testGetDeeplIgnoreTags(): void
    {
        $storeId = '1';
        $expectedValue = 'script,style';
        
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(ConfigProvider::XML_PATH_DEEPL_IGNORE_TAGS, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expectedValue);
            
        $result = $this->configProvider->getDeeplIgnoreTags($storeId);
        
        $this->assertEquals($expectedValue, $result);
    }

    public function testIsDeeplShowBilledCharactersEnabled(): void
    {
        $storeId = '1';
        
        $this->scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->with(ConfigProvider::XML_PATH_DEEPL_SHOW_BILLED_CHARACTERS, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn(true);
            
        $result = $this->configProvider->isDeeplShowBilledCharactersEnabled($storeId);
        
        $this->assertTrue($result);
    }

    public function testIsDeeplContextEnabled(): void
    {
        $storeId = '1';
        
        $this->scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->with(ConfigProvider::XML_PATH_DEEPL_ENABLE_CONTEXT, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn(true);
            
        $result = $this->configProvider->isDeeplContextEnabled($storeId);
        
        $this->assertTrue($result);
    }

    public function testGetDeeplRequestTimeout(): void
    {
        $storeId = '1';
        $expectedValue = 30;
        
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(ConfigProvider::XML_PATH_DEEPL_REQUEST_TIMEOUT, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expectedValue);
            
        $result = $this->configProvider->getDeeplRequestTimeout($storeId);
        
        $this->assertEquals($expectedValue, $result);
    }

    public function testGetChatGptApiKey(): void
    {
        $storeId = '1';
        $encryptedKey = 'encrypted_api_key';
        $decryptedKey = 'actual_api_key';
        
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(ConfigProvider::XML_PATH_CHATGPT_API_KEY, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($encryptedKey);
            
        $this->encryptor->expects($this->once())
            ->method('decrypt')
            ->with($encryptedKey)
            ->willReturn($decryptedKey);
            
        $result = $this->configProvider->getChatGptApiKey($storeId);
        
        $this->assertEquals($decryptedKey, $result);
    }

    public function testGetChatGptModel(): void
    {
        $storeId = '1';
        $expectedModel = 'gpt-3.5-turbo';
        
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(ConfigProvider::XML_PATH_CHATGPT_MODEL, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expectedModel);
            
        $result = $this->configProvider->getChatGptModel($storeId);
        
        $this->assertEquals($expectedModel, $result);
    }

    public function testGetChatGptTemperature(): void
    {
        $storeId = '1';
        $expectedValue = 0.7;
        
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(ConfigProvider::XML_PATH_CHATGPT_TEMPERATURE, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expectedValue);
            
        $result = $this->configProvider->getChatGptTemperature($storeId);
        
        $this->assertEquals($expectedValue, $result);
    }

    public function testGetChatGptDefaultSourceLang(): void
    {
        $storeId = '1';
        $expectedLang = 'en';
        
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(ConfigProvider::XML_PATH_CHATGPT_DEFAULT_SOURCE_LANG, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expectedLang);
            
        $result = $this->configProvider->getChatGptDefaultSourceLang($storeId);
        
        $this->assertEquals($expectedLang, $result);
    }

    public function testGetChatGptDefaultTargetLang(): void
    {
        $storeId = '1';
        $expectedLang = 'es';
        
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(ConfigProvider::XML_PATH_CHATGPT_DEFAULT_TARGET_LANG, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expectedLang);
            
        $result = $this->configProvider->getChatGptDefaultTargetLang($storeId);
        
        $this->assertEquals($expectedLang, $result);
    }

    public function testGetChatGptRequestTimeout(): void
    {
        $storeId = '1';
        $expectedValue = 30;
        
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(ConfigProvider::XML_PATH_CHATGPT_REQUEST_TIMEOUT, ScopeInterface::SCOPE_STORE, $storeId)
            ->willReturn($expectedValue);
            
        $result = $this->configProvider->getChatGptRequestTimeout($storeId);
        
        $this->assertEquals($expectedValue, $result);
    }
} 