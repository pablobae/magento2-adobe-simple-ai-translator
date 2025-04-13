<?php
declare(strict_types=1);

namespace Pablobae\SimpleAiTranslator\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ChatGptModel implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'gpt-4', 'label' => __('GPT-4')],
            ['value' => 'gpt-4-turbo-preview', 'label' => __('GPT-4 Turbo')],
            ['value' => 'gpt-3.5-turbo', 'label' => __('GPT-3.5 Turbo')],
        ];
    }
} 