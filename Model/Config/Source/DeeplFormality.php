<?php
declare(strict_types=1);

namespace Pablobae\SimpleAiTranslator\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class DeeplFormality implements OptionSourceInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'default', 'label' => __('Default')],
            ['value' => 'more', 'label' => __('More Formal')],
            ['value' => 'less', 'label' => __('Less Formal')],
            ['value' => 'prefer_more', 'label' => __('Prefer More Formal')],
            ['value' => 'prefer_less', 'label' => __('Prefer Less Formal')]
        ];
    }
}
