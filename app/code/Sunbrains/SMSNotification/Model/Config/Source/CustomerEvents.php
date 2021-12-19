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
 
namespace Sunbrains\SMSNotification\Model\Config\Source;

class CustomerEvents implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'customer_neworder', 'label' => __('Order Place')],
            ['value' => 'customer_contact', 'label' => __('Contact')],
            ['value' => 'customer_order_cancel', 'label' => __('Admin Order Cancel')],
            ['value' => 'customer_invoice', 'label' => __('Admin Invoice Order')],
            ['value' => 'customer_creditmemo', 'label' => __('Admin Creditmemo Order')],
            ['value' => 'customer_shipment', 'label' => __('Admin Shipment Order')],
            ['value' => 'customer_shipment_tracking', 'label' => __('Admin Shipment Tracking')],
        ];
    }
}
