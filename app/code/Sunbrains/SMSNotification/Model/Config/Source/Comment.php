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

class Comment implements \Magento\Config\Model\Config\CommentInterface
{
    public $_storeManager;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager=$storeManager;
    }

    /**
     * Retrieve element comment by element value
     * @param string $elementValue
     * @return string
     */
    public function getCommentText($elementValue)
    {
        //do some calculations here
        $storeurl = $this->_storeManager->getStore()
           ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK);
        return __('If your service provider sends delivery report through the webhook for the message which are processed and they include: delivered, failed, rejected,etc .And above 4 fields are not mandatory in this case Please specify below url in your service provider\'s account <br/>'.$storeurl.'smsnotification/pushurl/index');
    }
}
