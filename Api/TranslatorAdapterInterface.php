<?php
declare(strict_types=1);

namespace Pablobae\SimpleAiTranslator\Api;

/**
 * Translate Adapter Interface
 */
interface TranslatorAdapterInterface {

    /**
     * Translate using store configuration
     *
     * @param string $text
     * @param string $storeId
     * @param string|null $sourceLang
     * @return string
     */
    public function translate(string $text, string $storeId): string;

    /**
     * Translate using specific target language
     *
     * @param string $text
     * @param string $targetLang
     * @return string
     */
    public function translateToLanguage(string $text, string $targetLang): string;
}
