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

namespace Pablobae\SimpleAiTranslator\Plugin;

use Magento\Catalog\Ui\DataProvider\Product\Form\ProductDataProvider;

class TranslatableProductDataProvider
{
    /**
     * @param ProductDataProvider $subject
     * @param array $result
     * @return array
     */
    public function afterGetMeta(ProductDataProvider $subject, array $result): array
    {
        foreach ($result as $groupCode => $groupData) {
            if (!array_key_exists('children', $groupData)) {
                continue;
            }
            foreach ($groupData['children'] as $containerCode => $containerData) {
                if (!array_key_exists('children', $containerData)) {
                    continue;
                }
                foreach ($containerData['children'] as $attributeCode => $attributeData) {
                    if (!isset($attributeData['arguments']['data']['config']['formElement'])) {
                        continue;
                    }
                    if (!isset($attributeData['arguments']['data']['config']['dataType'])) {
                        continue;
                    }

                    if ($this->isTranslatable($attributeData)) {
                        $result[$groupCode]['children'][$containerCode]['children'][$attributeCode]['arguments']['data']['config']['translatable'] = true;
                    }

                }
            }
        }

        return $result;
    }

    /**
     * Validate if an attribute is translatable or not.
     * @param array $attributeData
     * @return bool
     */
    private function isTranslatable(array $attributeData): bool
    {
        $formElement = $attributeData['arguments']['data']['config']['formElement'];
        $dataType = $attributeData['arguments']['data']['config']['dataType'];

        if ($dataType == 'text' && $formElement == 'input') {
            return true;
        }

        if ($dataType == 'textarea') {
            return true;
        }

        return false;
    }
}
