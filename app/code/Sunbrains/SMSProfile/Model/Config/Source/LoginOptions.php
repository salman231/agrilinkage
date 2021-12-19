<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */
 
namespace Sunbrains\SMSProfile\Model\Config\Source;

class LoginOptions implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'login_pwd', 'label' => __('Login With Password Only')],
            ['value' => 'login_otp', 'label' => __('Login With OTP Only')],
            ['value' => 'login_both', 'label' => __('Login With OTP and Password')],
        ];
    }
}
