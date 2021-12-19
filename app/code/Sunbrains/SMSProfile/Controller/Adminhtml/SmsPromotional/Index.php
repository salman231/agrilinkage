<?php
/**
 * Sunbrains
 * @category  Sunbrains
 * @package   Sunbrains_SMSProfile
 * @author    Sunbrains
 */

namespace Sunbrains\SMSProfile\Controller\Adminhtml\SmsPromotional;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory as ResultPageFactory;

class Index extends Action
{

    /**
    * @var string
    */
    const ACTION_RESOURCE = 'Sunbrains_SMSProfile::smspromotional';

     /**
      * @var ResultPageFactory
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
     * SmsLog grid for AJAX request
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Sunbrains_SMSProfile::smsprofile');
        $resultPage->addBreadcrumb(__('Sunbrains'), __('Sunbrains'));
        $resultPage->addBreadcrumb(__('SmsPromotional'), __('SmsPromotional'));
        $resultPage->getConfig()->getTitle()->prepend((__('SMS Promotional')));
        return $resultPage;
    }
}
