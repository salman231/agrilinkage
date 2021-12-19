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

class ListApi implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'Twilio Api Service', 'label' => __('Twilio Api Service')],
            ['value' => 'BulkSms', 'label' => __('BulkSms')],
            ['value' => 'Other', 'label' => __('Other')],
        ];
    }
}
