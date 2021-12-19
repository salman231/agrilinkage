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
use Magento\Backend\Model\Session as BackendSession;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Sunbrains\SMSNotification\Model\SMSTemplatesFactory;
use Sunbrains\SMSNotification\Api\SMSTemplatesRepositoryInterface;

class Save extends Action
{
    /**
     * @var string
     */
    const ACTION_RESOURCE = 'Sunbrains_SMSNotification::smstemplates';

    /**
     * SmsTemplates  factory
     *
     * @var SMSTemplatesFactory
     */
    private $smsTemplates;

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
     * DataPersistorInterface
     *
     * @var dataPersistor
     */
    private $dataPersistor;

    /**
     * @param RedirectFactory  $resultRedirect
     * @param SMSTemplatesFactory $smsTemplates
     * @param SMSTemplatesRepositoryInterface $smsTemplatesRepository
     * @param DataPersistorInterface $dataPersistor
     * @param Context $context
     */

    public function __construct(
        Context $context,
        RedirectFactory $resultRedirect,
        DataPersistorInterface $dataPersistor,
        SMSTemplatesFactory $smsTemplates,
        SMSTemplatesRepositoryInterface $smsTemplatesRepository
    ) {
        $this->resultRedirect = $resultRedirect;
        $this->smsTemplates  = $smsTemplates;
        $this->dataPersistor = $dataPersistor;
        $this->smsTemplatesRepository  = $smsTemplatesRepository;
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
        $smsTemplate =  $this->smsTemplates->create();

        try {
            $this->smsTemplatesRepository->save($smsTemplate->setData($data));
            $this->messageManager->addSuccess(__('You saved this Sms Template.'));
            $this->dataPersistor->clear('smstemplates');
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
