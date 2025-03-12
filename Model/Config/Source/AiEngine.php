<?php
declare(strict_types=1);

namespace Pablobae\SimpleAiTranslator\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class AiEngine implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'deepl', 'label' => __('DeepL')],
            ['value' => 'chatgpt', 'label' => __('ChatGPT')],
        ];
    }
}
