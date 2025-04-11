<?php
declare(strict_types=1);

namespace Pablobae\SimpleAiTranslator\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider
{
    /**
     * Configuration paths
     */
    const STORE_LOCALE = 'general/locale/code';

    const XML_PATH_ENABLE = 'pablobae_simpleaitranslator/general/enable';
    const XML_PATH_AI_ENGINE = 'pablobae_simpleaitranslator/general/ai_engine';

    // DeepL API Configuration Paths
    const XML_PATH_DEEPL_API_DOMAIN = 'pablobae_simpleaitranslator/deepl/api_domain';
    const XML_PATH_DEEPL_API_KEY = 'pablobae_simpleaitranslator/deepl/api_key';
    const XML_PATH_DEEPL_DEFAULT_SOURCE_LANG = 'pablobae_simpleaitranslator/deepl/default_source_lang';
    const XML_PATH_DEEPL_DEFAULT_TARGET_LANG = 'pablobae_simpleaitranslator/deepl/default_target_lang';
    const XML_PATH_DEEPL_MODEL_TYPE = 'pablobae_simpleaitranslator/deepl/model_type';
    const XML_PATH_DEEPL_SPLIT_SENTENCES = 'pablobae_simpleaitranslator/deepl/split_sentences';
    const XML_PATH_DEEPL_PRESERVE_FORMATTING = 'pablobae_simpleaitranslator/deepl/preserve_formatting';
    const XML_PATH_DEEPL_FORMALITY = 'pablobae_simpleaitranslator/deepl/formality';
    const XML_PATH_DEEPL_TAG_HANDLING = 'pablobae_simpleaitranslator/deepl/tag_handling';
    const XML_PATH_DEEPL_OUTLINE_DETECTION = 'pablobae_simpleaitranslator/deepl/outline_detection';
    const XML_PATH_DEEPL_NON_SPLITTING_TAGS = 'pablobae_simpleaitranslator/deepl/non_splitting_tags';
    const XML_PATH_DEEPL_SPLITTING_TAGS = 'pablobae_simpleaitranslator/deepl/splitting_tags';
    const XML_PATH_DEEPL_IGNORE_TAGS = 'pablobae_simpleaitranslator/deepl/ignore_tags';
    const XML_PATH_DEEPL_SHOW_BILLED_CHARACTERS = 'pablobae_simpleaitranslator/deepl/show_billed_characters';
    const XML_PATH_DEEPL_ENABLE_CONTEXT = 'pablobae_simpleaitranslator/deepl/enable_context';
    const XML_PATH_DEEPL_REQUEST_TIMEOUT = 'pablobae_simpleaitranslator/deepl/request_timeout';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get store locale by store id
     * @param $storeId
     * @return mixed
     */
    public function getStoreLocale($storeId)
    {
        return $this->scopeConfig->getValue(self::STORE_LOCALE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Check if the extension is enabled
     *
     * @param null|int|string $storeId
     * @return bool
     */
    public function isModuleEnabled($storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Get the preferred AI provider
     *
     * @param null|int|string $storeId
     * @return string|null
     */
    public function getAiEngine($storeId = null): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_AI_ENGINE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Get Deepl API domain (Free or Pro)
     *
     * @param null|int|string $storeId
     * @return string|null
     */
    public function getDeeplApiDomain($storeId = null): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_DEEPL_API_DOMAIN, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Get Deepl API key
     *
     * @param null|int|string $storeId
     * @return string|null
     */
    public function getDeeplApiKey($storeId = null): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_DEEPL_API_KEY, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Get default source language
     *
     * @param null|int|string $storeId
     * @return string|null
     */
    public function getDeeplDefaultSourceLang($storeId = null): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_DEEPL_DEFAULT_SOURCE_LANG, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Get default target language
     *
     * @param null|int|string $storeId
     * @return string|null
     */
    public function getDeeplDefaultTargetLang($storeId = null): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_DEEPL_DEFAULT_TARGET_LANG, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Get model type (base or accurate)
     *
     * @param null|int|string $storeId
     * @return string|null
     */
    public function getDeeplModelType($storeId = null): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_DEEPL_MODEL_TYPE, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Get split sentences setting
     *
     * @param null|int|string $storeId
     * @return string|null
     */
    public function getDeeplSplitSentences($storeId = null): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_DEEPL_SPLIT_SENTENCES, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Check if preserve formatting is enabled
     *
     * @param null|int|string $storeId
     * @return bool
     */
    public function isDeeplPreserveFormattingEnabled($storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_DEEPL_PRESERVE_FORMATTING, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Get formality setting
     *
     * @param null|int|string $storeId
     * @return string|null
     */
    public function getDeeplFormality($storeId = null): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_DEEPL_FORMALITY, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Get tag handling setting
     *
     * @param null|int|string $storeId
     * @return string|null
     */
    public function getDeeplTagHandling($storeId = null): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_DEEPL_TAG_HANDLING, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Check if outline detection is enabled
     *
     * @param null|int|string $storeId
     * @return bool
     */
    public function isDeeplOutlineDetectionEnabled($storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_DEEPL_OUTLINE_DETECTION, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Get non-splitting tags
     *
     * @param null|int|string $storeId
     * @return string|null
     */
    public function getDeeplNonSplittingTags($storeId = null): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_DEEPL_NON_SPLITTING_TAGS, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Get splitting tags
     *
     * @param null|int|string $storeId
     * @return string|null
     */
    public function getDeeplSplittingTags($storeId = null): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_DEEPL_SPLITTING_TAGS, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Get ignore tags
     *
     * @param null|int|string $storeId
     * @return string|null
     */
    public function getDeeplIgnoreTags($storeId = null): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_DEEPL_IGNORE_TAGS, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Check if show billed characters is enabled
     *
     * @param null|int|string $storeId
     * @return bool
     */
    public function isDeeplShowBilledCharactersEnabled($storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_DEEPL_SHOW_BILLED_CHARACTERS, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Check if context support is enabled
     *
     * @param null|int|string $storeId
     * @return bool
     */
    public function isDeeplContextEnabled($storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_DEEPL_ENABLE_CONTEXT, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Get request timeout in seconds
     *
     * @param null|int|string $storeId
     * @return int
     */
    public function getDeeplRequestTimeout($storeId = null): int
    {
        $timeout = (int)$this->scopeConfig->getValue(self::XML_PATH_DEEPL_REQUEST_TIMEOUT, ScopeInterface::SCOPE_STORE, $storeId);
        return $timeout > 0 ? $timeout : 30; // Default to 30 seconds if not set or invalid
    }
}
