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
namespace Sunbrains\SMSNotification\Controller\Adminhtml\SmsLog;

class View extends \Magento\Backend\App\Action
{

    /**
    * @var string
    */
    const ACTION_RESOURCE = 'Sunbrains_SMSNotification::smslog';

     /**
      * @var \Magento\Framework\View\Result\PageFactory
      */
    private $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ACTION_RESOURCE);
    }

    /**
     * SmsLog view for AJAX request
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Sunbrains_SMSNotification::smsnotification');
        $resultPage->addBreadcrumb(__('Sunbrains'), __('Sunbrains'));
        $resultPage->addBreadcrumb(__('SMSLog'), __('SMSLog'));
        $resultPage->getConfig()->getTitle()->prepend((__('SMSLog View')));
        return $resultPage;
    }
}
