<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */
 
namespace Sunbrains\SMSProfile\Controller\Adminhtml\SmsProfileTemplates;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory as ResultPageFactory;

class Index extends Action
{
    /**
    * @var string
    */
    const ACTION_RESOURCE = 'Sunbrains_SMSProfile::smsprofiletemplates';
    
     /**
      * @var ResultPageFactory $resultPageFactory
      */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param ResultPageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        ResultPageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

     protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ACTION_RESOURCE);
    }

    /**
     * SmsTemplates grid for AJAX request
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Sunbrains_SMSProfile::smsprofile');
        $resultPage->addBreadcrumb(__('Sunbrains'), __('Sunbrains'));
        $resultPage->addBreadcrumb(__('SmsProfileTemplates'), __('SmsProfileTemplates'));
        $resultPage->getConfig()->getTitle()->prepend((__('OTP SMS Templates')));
        return $resultPage;
    }
}
