<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */

namespace Sunbrains\SMSProfile\Model\Config\Source;

class SmsProfileOtpFormat implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            'alphanum' =>   __('Alphanumeric'),
            'alpha'    =>   __('Alphabetical'),
            'num'      =>   __('Numeric')
        ];
    }
}
