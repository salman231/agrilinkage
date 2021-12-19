<?php
/**
 * Sunbrains
 * Copyright (C) 2019 Sunbrains <info@sunbrains.com>
 *
 * @category Sunbrains
 * @package Sunbrains_SMSNotification
 * @copyright Copyright (c) 2019 Mage Delight (http://www.sunbrains.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Sunbrains <info@sunbrains.com>
 */
 
namespace Sunbrains\SMSNotification\Plugin;

use Sunbrains\SMSNotification\Helper\Data as HelperData;

class LayoutProcessor
{

    /**
     * @var HelperData
     */
    protected $helper;
    /**
     * LayoutProcessor constructor.
     * @param HelperData $helper
     */
    public function __construct(HelperData $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    ) {
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'])) {
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
            ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']
            ['telephone']['validation'] = [
                    'required-entry' => true,
                    "validate-number"=>true,
                    "min_digit_length" => $this->helper->getCustomerNumberMinLength(),
                    "max_digit_length" =>  $this->helper->getCustomerNumberMaxLength()
                ];

            if ($this->helper->getNoticeBelowTelephone()) {
                $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
                ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']
                ['telephone']['notice'] = $this->helper->getNoticeBelowTelephone();
            }
        }

        /* config: checkout/options/display_billing_address_on = payment_method */
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['payments-list']['children'])) {
            foreach ($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                     ['payment']['children']['payments-list']['children'] as $key => $payment) {
                    /* telephone */
                if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children'] ['payment']['children']['payments-list']['children'][$key]['children']['form-fields'])) {
                            $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                            ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                            ['telephone']['validation'] = [
                                'required-entry' => true,
                                "validate-number"=>true,
                                "min_digit_length" => $this->helper->getCustomerNumberMinLength(),
                                "max_digit_length" =>  $this->helper->getCustomerNumberMaxLength()
                            ];

                            if ($this->helper->getNoticeBelowTelephone()) {
                                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                                ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                                ['telephone']['notice'] = $this->helper->getNoticeBelowTelephone();
                            }
                }
            }
        }

        return $jsLayout;
    }
}
