<?php
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
