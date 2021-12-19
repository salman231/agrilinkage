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

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Sunbrains\SMSNotification\Model\SMSLogFactory;

class ClearLog extends Action
{
    /**
     * @var string
     */
    const ACTION_RESOURCE = 'Sunbrains_SMSNotification::smslog';

    /**
     * RedirectFactory
     *
     * @var resultRedirect
     */
    private $resultRedirect;

    /**
     * @var SMSLogFactory
     */
    private $smslog;

    /**
     * @param Context $context
     * @param SMSLogFactory $smslog
     * @param RedirectFactory $resultRedirect
     */
    public function __construct(
        Context $context,
        SMSLogFactory $smslog,
        RedirectFactory $resultRedirect
    ) {
        parent::__construct($context);
        $this->resultRedirect = $resultRedirect;
        $this->smslog = $smslog;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ACTION_RESOURCE);
    }

    /**
     * SmsLog clear for AJAX request
     *
     * @return RedirectFactory
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirect->create();
        $sms  = $this->smslog->create();
        try {
            $sms->clearelog();
            $this->messageManager->addSuccess(__('The Sms Log has been cleared.'));
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        return $resultRedirect->setPath('*/*/');
    }
}
