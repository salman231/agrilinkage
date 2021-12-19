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
use Magento\Framework\App\Request\DataPersistorInterface;
use Sunbrains\SMSProfile\Model\SMSProfileTemplatesFactory;
use Sunbrains\SMSProfile\Api\SMSProfileTemplatesRepositoryInterface;

class Save extends Action
{
    /**
    * @var string
    */
    const ACTION_RESOURCE = 'Sunbrains_SMSProfile::smsprofiletemplates';

	/**
    * sms profile templates  factory
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
    * DataPersistorInterface
    *
    * @var dataPersistor
    */
    private $dataPersistor;

     /**
     * @param RedirectFactory  $resultRedirect
     * @param SMSProfileTemplatesFactory $smsProfileTemplates
     * @param SMSProfileTemplatesRepositoryInterface $smsProfileTemplatesRepository
     * @param BackendSession $backendSession
     * @param DataPersistorInterface $dataPersistor
     * @param Context $context
     */

     public function __construct(
        Context $context,
        RedirectFactory $resultRedirect,
        BackendSession $backendSession,
        DataPersistorInterface $dataPersistor,
        SMSProfileTemplatesFactory $smsProfileTemplates,
        SMSProfileTemplatesRepositoryInterface $smsProfileTemplatesRepository
    ) {
        $this->resultRedirect = $resultRedirect;
        $this->backendSession = $backendSession;
        $this->smsProfileTemplates  = $smsProfileTemplates;
        $this->dataPersistor = $dataPersistor;
        $this->smsProfileTemplatesRepository  = $smsProfileTemplatesRepository;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ACTION_RESOURCE);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $data['store_id'] =$data['store_id'][0];
        $resultRedirect = $this->resultRedirect->create();
        $smsProfileTemplate =  $this->smsProfileTemplates->create();
        try {
            $this->smsProfileTemplatesRepository->save($smsProfileTemplate->setData($data));
            $this->messageManager->addSuccess(__('You saved this Sms Profile Template.'));
            $this->dataPersistor->clear('smsprofiletemplate');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while saving data.'));
        }   
        return $resultRedirect->setPath('*/*/');
    }    
}
