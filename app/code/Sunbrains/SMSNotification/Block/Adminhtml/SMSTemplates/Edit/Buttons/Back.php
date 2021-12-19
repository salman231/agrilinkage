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
 
namespace Sunbrains\SMSNotification\Block\Adminhtml\SMSTemplates\Edit\Buttons;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Back extends Generic implements ButtonProviderInterface
{

    /**
     * get button data
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];
    }
    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }
}
