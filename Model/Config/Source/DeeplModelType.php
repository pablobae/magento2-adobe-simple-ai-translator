<?php
declare(strict_types=1);

namespace Pablobae\SimpleAiTranslator\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class DeeplModelType implements OptionSourceInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('Default')],
            ['value' => 'base', 'label' => __('Base (Faster)')],
            ['value' => 'accurate', 'label' => __('Accurate (Higher quality)')]
        ];
    }
}
