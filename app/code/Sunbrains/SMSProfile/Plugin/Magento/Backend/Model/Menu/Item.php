<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */
namespace Sunbrains\SMSProfile\Plugin\Magento\Backend\Model\Menu;

class Item
{
    public function afterGetUrl($subject, $result)
    {
        $menuId = $subject->getId();
        
        if ($menuId == 'Sunbrains_SMSProfile::documentation') {
            $result = 'http://docs.sunbrains.com/display/MAG/Mobile+OTP+Login+-+Magento+2';
        }

        return $result;
    }
}
