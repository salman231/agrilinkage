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
use Magento\Backend\Model\View\Result\ForwardFactory as ResultForwardFactory;

class NewAction extends Action
{
    /**
    * @var string
    */
    const ACTION_RESOURCE = 'Sunbrains_SMSProfile::smsprofiletemplates';

     /**
      * @var ResultForwardFactory $resultForwardFactory
      */
    private $resultForwardFactory;

    /**
     * @param \Context $context
     * @param ResultForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        ResultForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ACTION_RESOURCE);
    }

    public function execute()
    {
        /** @var ResultForwardFactory $resultForward */
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
