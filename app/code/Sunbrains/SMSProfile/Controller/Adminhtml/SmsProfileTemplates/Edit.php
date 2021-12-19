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
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Backend\Model\Session as BackendSession;
use Sunbrains\SMSProfile\Model\SMSProfileTemplatesFactory;
use Sunbrains\SMSProfile\Api\SMSProfileTemplatesRepositoryInterface;

class Edit extends Action
{
	/**
    * @var string
    */
    const ACTION_RESOURCE = 'Sunbrains_SMSProfile::smsprofiletemplates';

    /**
    * SMSProfileTemplatesFactory  factory
    *
    * @var SMSProfileTemplatesFactory
    */
    private $smsProfileTemplates;

    /**
    * SMSProfileTemplatesRepositoryInterface
    *
    * @var SMSProfileTemplatesRepositoryInterface
    */
    private $smsProfileTemplatesRepository;

    /**
    * Core registry
    *
    * @var Registry
    */
    private $coreRegistry;

    /**
    * @var PageFactory
    */
    private $resultPageFactory;

     /**
    * DataPersistorInterface
    *
    * @var dataPersistor
    */
    private $dataPersistor;

    /**
    * BackendSession
    *
    * @var backendSession
    */
    private $backendSession;

    /**
     * @param Registry $registry
     * @param DataPersistorInterface $dataPersistor,
     * @param SMSProfileTemplatesFactory $smsProfileTemplates
     * @param  BackendSession $backendSession
     * @param SMSProfileTemplatesRepositoryInterface $smsProfileTemplatesRepository
     * @param PageFactory $resultPageFactory
     * @param Context $context
     */
    public function __construct(
        Registry $registry,
        SMSProfileTemplatesFactory $smsProfileTemplates,
        BackendSession $backendSession,
        SMSProfileTemplatesRepositoryInterface $smsProfileTemplatesRepository,
        DataPersistorInterface $dataPersistor,
        PageFactory $resultPageFactory,
        Context $context
    ) {
        $this->coreRegistry      = $registry;
        $this->dataPersistor = $dataPersistor;
        $this->smsProfileTemplates  = $smsProfileTemplates;
        $this->backendSession = $backendSession;
        $this->smsProfileTemplatesRepository  = $smsProfileTemplatesRepository;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    private function _initSmsProfileTemplates()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Sunbrains_SMSProfile::smsprofile');
        $resultPage->addBreadcrumb(__('Sunbrains'), __('Sunbrains'));
        $resultPage->addBreadcrumb(__('SmsProfileTemplates'), __('SmsProfileTemplates'));        
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ACTION_RESOURCE);
    }

     /**
     * Execute action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('entity_id');
        $smsProfileTemplate ='';
        if ($id) {
            $smsProfileTemplate = $this->smsProfileTemplatesRepository->getById($id);
            if (!$smsProfileTemplate->getId()) {
                $this->messageManager->addError(__('This Templates no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirect->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        if ($smsProfileTemplate) {
            $this->dataPersistor->set('smsprofiletemplate',$smsProfileTemplate);
        }
        $resultPage = $this->_initSmsProfileTemplates();
        $resultPage->addBreadcrumb(
            $id ? __('Edit SmsProfileTemplates') : __('New SmsProfileTemplates'),
            $id ? __('Edit SmsProfileTemplates') : __('New SmsProfileTemplates')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('New OTP SMS Template'));
        if ($smsProfileTemplate) {
            $resultPage->getConfig()->getTitle()
                ->prepend($smsProfileTemplate->getTemplateName() ? $smsProfileTemplate->getTemplateName() : __('New OTP SMS Template'));
        }
        return $resultPage;
    }
}
