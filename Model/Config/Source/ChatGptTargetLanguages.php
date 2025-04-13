<?php
declare(strict_types=1);

namespace Pablobae\SimpleAiTranslator\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ChatGptTargetLanguages implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
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