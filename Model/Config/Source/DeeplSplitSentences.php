<?php
declare(strict_types=1);

namespace Pablobae\SimpleAiTranslator\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class DeeplSplitSentences implements OptionSourceInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('Default - Split on punctuation and newlines')],
            ['value' => '0', 'label' => __('No splitting')],
            ['value' => 'nonewlines', 'label' => __('Split on punctuation only')]
        ];
    }
}
