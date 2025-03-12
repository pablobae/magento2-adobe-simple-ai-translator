<?php
declare(strict_types=1);

namespace Pablobae\SimpleAiTranslator\Api;

/**
 * Translate Adapter Interface
 */
interface TranslatorAdapterInterface {


    /**
     * Translate the provided text
     * @param string $text
     * @param string $storeId
     * @return string
     */
    public function translate(string $text, string $storeId): string;
}
