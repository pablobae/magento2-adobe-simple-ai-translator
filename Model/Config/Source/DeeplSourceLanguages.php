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

class DeeplSourceLanguages implements OptionSourceInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('Auto Detect')],
            ['value' => 'BG', 'label' => __('Bulgarian')],
            ['value' => 'CS', 'label' => __('Czech')],
            ['value' => 'DA', 'label' => __('Danish')],
            ['value' => 'DE', 'label' => __('German')],
            ['value' => 'EL', 'label' => __('Greek')],
            ['value' => 'EN', 'label' => __('English')],
            ['value' => 'ES', 'label' => __('Spanish')],
            ['value' => 'ET', 'label' => __('Estonian')],
            ['value' => 'FI', 'label' => __('Finnish')],
            ['value' => 'FR', 'label' => __('French')],
            ['value' => 'HU', 'label' => __('Hungarian')],
            ['value' => 'ID', 'label' => __('Indonesian')],
            ['value' => 'IT', 'label' => __('Italian')],
            ['value' => 'JA', 'label' => __('Japanese')],
            ['value' => 'KO', 'label' => __('Korean')],
            ['value' => 'LT', 'label' => __('Lithuanian')],
            ['value' => 'LV', 'label' => __('Latvian')],
            ['value' => 'NB', 'label' => __('Norwegian')],
            ['value' => 'NL', 'label' => __('Dutch')],
            ['value' => 'PL', 'label' => __('Polish')],
            ['value' => 'PT', 'label' => __('Portuguese')],
            ['value' => 'RO', 'label' => __('Romanian')],
            ['value' => 'RU', 'label' => __('Russian')],
            ['value' => 'SK', 'label' => __('Slovak')],
            ['value' => 'SL', 'label' => __('Slovenian')],
            ['value' => 'SV', 'label' => __('Swedish')],
            ['value' => 'TR', 'label' => __('Turkish')],
            ['value' => 'UK', 'label' => __('Ukrainian')],
            ['value' => 'ZH', 'label' => __('Chinese')]
        ];
    }
}
