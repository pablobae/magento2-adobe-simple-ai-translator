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

namespace Pablobae\SimpleAiTranslator\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ChatGptSourceLanguages implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => '', 'label' => __('Auto-detect')],
            ['value' => 'en', 'label' => __('English')],
            ['value' => 'es', 'label' => __('Spanish')],
            ['value' => 'fr', 'label' => __('French')],
            ['value' => 'de', 'label' => __('German')],
            ['value' => 'it', 'label' => __('Italian')],
            ['value' => 'pt', 'label' => __('Portuguese')],
            ['value' => 'nl', 'label' => __('Dutch')],
            ['value' => 'ru', 'label' => __('Russian')],
            ['value' => 'ja', 'label' => __('Japanese')],
            ['value' => 'ko', 'label' => __('Korean')],
            ['value' => 'zh', 'label' => __('Chinese')],
            ['value' => 'ar', 'label' => __('Arabic')],
            ['value' => 'hi', 'label' => __('Hindi')],
            ['value' => 'tr', 'label' => __('Turkish')],
            ['value' => 'pl', 'label' => __('Polish')],
            ['value' => 'vi', 'label' => __('Vietnamese')],
            ['value' => 'th', 'label' => __('Thai')],
            ['value' => 'id', 'label' => __('Indonesian')],
            ['value' => 'sv', 'label' => __('Swedish')],
            ['value' => 'da', 'label' => __('Danish')],
            ['value' => 'fi', 'label' => __('Finnish')],
            ['value' => 'no', 'label' => __('Norwegian')],
            ['value' => 'cs', 'label' => __('Czech')],
            ['value' => 'el', 'label' => __('Greek')],
            ['value' => 'he', 'label' => __('Hebrew')],
            ['value' => 'ro', 'label' => __('Romanian')],
            ['value' => 'hu', 'label' => __('Hungarian')],
            ['value' => 'uk', 'label' => __('Ukrainian')]
        ];
    }
}
