<?php
declare(strict_types=1);

namespace Pablobae\SimpleAiTranslator\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class DeeplApiDomain implements OptionSourceInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'api.deepl.com', 'label' => __('DeepL API Pro (api.deepl.com)')],
            ['value' => 'api-free.deepl.com', 'label' => __('DeepL API Free (api-free.deepl.com)')]
        ];
    }
}
