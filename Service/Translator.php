<?php
declare(strict_types=1);

namespace Pablobae\SimpleAiTranslator\Service;

use Magento\Framework\Exception\LocalizedException;
use Pablobae\SimpleAiTranslator\Api\TranslatorAdapterInterface;
use RuntimeException;

class Translator
{
    /**
     * @var TranslatorAdapterInterface[]
     */
    private array $translatorAdapters;

    public function __construct(
        private readonly ConfigProvider $configProvider,
        array $translatorAdapters = []
    ) {
        $this->translatorAdapters = $translatorAdapters;
    }

    /**
     * Translate given text using the configured Ai Engine
     * @param string $text
     * @param string $storeId
     * @return string
     * @throws LocalizedException
     */
    public function translate(string $text, string $storeId): string
    {
        if (!$this->configProvider->isModuleEnabled($storeId)) {
            throw new RuntimeException('Extension is not enabled');
        }

        $aiEngine = $this->configProvider->getAiEngine();

        if (!isset($this->translatorAdapters[$aiEngine])) {
            throw new LocalizedException(__("Translation adapter '%1' not found.", $aiEngine));
        }

        return $this->translatorAdapters[$aiEngine]->translate($text, $storeId);
    }

    /**
     * Translate text with specific target language
     *
     * @param string $text
     * @param string $targetLang
     * @return string
     * @throws LocalizedException
     */
    public function translateToLanguage(string $text, string $targetLang): string
    {
        if (!$this->configProvider->isModuleEnabled()) {
            throw new RuntimeException('Extension is not enabled');
        }

        $aiEngine = $this->configProvider->getAiEngine();

        if (!isset($this->translatorAdapters[$aiEngine])) {
            throw new LocalizedException(__("Translation adapter '%1' not found.", $aiEngine));
        }

        return $this->translatorAdapters[$aiEngine]->translateToLanguage($text, $targetLang);
    }
}

