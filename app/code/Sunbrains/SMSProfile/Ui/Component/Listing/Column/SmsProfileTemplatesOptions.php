<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */
 
namespace Sunbrains\SMSProfile\Ui\Component\Listing\Column;

class SmsProfileTemplatesOptions implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
     {
        return [
            ['value' => 'customer_signup_otp', 'label' => __('Send Otp At Customer Signup Event')],
            ['value' => 'customer_login_otp', 'label' => __('Send Otp At Customer Login Event')],
            ['value' => 'customer_account_edit_otp', 'label' => __('Send Otp At Customer Account Update Event')],
            ['value' => 'cod_otp', 'label' => __('Send Otp For COD Payment Method During Checkout')],
            ['value' => 'forgot_password_otp', 'label' => __('Send Otp For Forgot Password Event')],
        ];
     }
}
