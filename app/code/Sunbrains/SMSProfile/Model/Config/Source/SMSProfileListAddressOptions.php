<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */
 
namespace Sunbrains\SMSProfile\Model\Config\Source;

class SMSProfileListAddressOptions implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'shipping_add', 'label' => __('Default Shipping Address')],
            ['value' => 'billing_add', 'label' => __('Default Billing Address')],
            ['value' => 'first_add', 'label' => __('First Address')],
            ['value' => 'last_add', 'label' => __('Last Address')],
        ];
    }
}
