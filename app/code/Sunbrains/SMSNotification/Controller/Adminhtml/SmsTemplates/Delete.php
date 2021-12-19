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
 
namespace Sunbrains\SMSNotification\Controller\Adminhtml\SmsTemplates;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Sunbrains\SMSNotification\Api\SMSTemplatesRepositoryInterface;

class Delete extends Action
{
    /**
     * @var string
     */
    const ACTION_RESOURCE = 'Sunbrains_SMSNotification::smstemplates';

    /**
     * SMSTemplatesRepositoryInterface
     *
     * @var SMSTemplatesFactory
     */
    private $smsTemplatesRepository;

    /**
     * RedirectFactory
     *
     * @var resultRedirect
     */
    private $resultRedirect;

     /**
      * @param RedirectFactory  $resultRedirect
      * @param SMSTemplatesRepositoryInterface $smsTemplatesRepository
      * @param Context $context
      */

    public function __construct(
        Context $context,
        RedirectFactory $resultRedirect,
        SMSTemplatesRepositoryInterface $smsTemplatesRepository
    ) {
        $this->resultRedirect = $resultRedirect;
        $this->smsTemplatesRepository  = $smsTemplatesRepository;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ACTION_RESOURCE);
    }
    
    public function execute()
    {
        $id = $this->getRequest()->getParam('entity_id');
        $resultRedirect = $this->resultRedirect->create();

        if ($id) {
            try {
                 $smsTemplate = $this->smsTemplatesRepository->getById($id);
                $this->smsTemplatesRepository->delete($smsTemplate);
                $this->messageManager->addSuccess(__('The Sms Template has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['entity_id' => $id]);
            }
        }
        $this->messageManager->addError(__('We can\'t find a Sms Template to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
