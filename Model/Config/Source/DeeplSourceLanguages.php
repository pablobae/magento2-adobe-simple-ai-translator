<?php
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
