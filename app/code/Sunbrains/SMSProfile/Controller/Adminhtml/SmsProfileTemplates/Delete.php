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
use Magento\Backend\Model\Session as BackendSession;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Sunbrains\SMSProfile\Api\SMSProfileTemplatesRepositoryInterface;

class Delete extends Action
{	
    /**
    * @var string
    */
    const ACTION_RESOURCE = 'Sunbrains_SMSProfile::smsprofiletemplates';

    /**
    * SMSProfileTemplatesRepositoryInterface
    *
    * @var SMSProfileTemplatesRepositoryInterface
    */
    private $smsProfileTemplatesRepository;

    /**
    * BackendSession
    *
    * @var backendSession
    */
    private $backendSession;

    /**
    * RedirectFactory
    *
    * @var resultRedirect
    */
    private $resultRedirect;

     /**
     * @param RedirectFactory  $resultRedirect
     * @param SMSProfileTemplatesRepositoryInterface $smsProfileTemplatesRepository
     * @param BackendSession $backendSession
     * @param Context $context
     */

     public function __construct(
        Context $context,
        RedirectFactory $resultRedirect,
        BackendSession $backendSession,
        SMSProfileTemplatesRepositoryInterface $smsProfileTemplatesRepository
    ) {
        $this->resultRedirect = $resultRedirect;
        $this->backendSession = $backendSession;
        $this->smsProfileTemplatesRepository  = $smsProfileTemplatesRepository;
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
                 $smsProfileTemplate = $this->smsProfileTemplatesRepository->getById($id);
                $this->smsProfileTemplatesRepository->delete($smsProfileTemplate);
                $this->messageManager->addSuccess(__('The Sms Profile Template has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['entity_id' => $id]);
            }
        }
        $this->messageManager->addError(__('We can\'t find a Sms Profile Template to delete.'));
        return $resultRedirect->setPath('*/*/');
    }    
}
