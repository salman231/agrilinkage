<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */
 
namespace Sunbrains\SMSProfile\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class SmsPromotionalOptions implements OptionSourceInterface
{
    public function toOptionArray()
    {
        $res = [];
        $res[] = ['value' => 'custom', 'label' => 'Custom No.'];
        $res[] = ['value' => 'customer', 'label' => 'Customer'];
        $res[] = ['value' => 'customer_group', 'label' => 'Customer Group'];
        $res[] = ['value' => 'abandoned_cart', 'label' => 'Abandoned Cart'];
        $res[] = ['value' => 'subscriber', 'label' => 'Newsletter Subscribed Customer'];
        $res[] = ['value' => 'csv', 'label' => 'Import CSV'];
        return $res;
    }
}