<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */
 
namespace Sunbrains\SMSProfile\Model\Config\Source;

class SMSProfileListApi implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'Twilio Api Service', 'label' => __('Twilio Api Service')],
            ['value' => 'Other', 'label' => __('Other')],
        ];
    }
}
