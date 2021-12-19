<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */
 
namespace Sunbrains\SMSProfile\Controller\Adminhtml\SmsProfileLog;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Sunbrains\SMSProfile\Model\SMSProfileLogFactory;

class ClearLog extends Action
{
    /**
    * @var string
    */
    const ACTION_RESOURCE = 'Sunbrains_SMSProfile::smsprofilelog';

    /**
    * RedirectFactory
    *
    * @var resultRedirect
    */
    private $resultRedirect;

    /** @var SMSProfileLogFactory */
    private $smsprofilelog;

    /**
     * @param Context $context
     * @param SMSProfileLogFactory $smsprofilelog
     * @param RedirectFactory $resultRedirect
     */
    public function __construct(
        Context $context,
        SMSProfileLogFactory $smsprofilelog,
        RedirectFactory $resultRedirect
    ) {
        parent::__construct($context);
        $this->resultRedirect = $resultRedirect;
        $this->smsprofilelog = $smsprofilelog;
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
        $sms  = $this->smsprofilelog->create(); 
        try {
            $sms->SmsProfileClearelog();
            $this->messageManager->addSuccess(__('The Sms Log has been cleared.'));
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        return $resultRedirect->setPath('*/*/');
    }
}
