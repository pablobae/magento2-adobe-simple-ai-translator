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
